<?php

namespace DoanQuang\AiContent\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use DoanQuang\AiContent\Http\Requests\AiContentRequest;
use DoanQuang\AiContent\Models\AiContent;
use Botble\Base\Http\Controllers\BaseController;
use DoanQuang\AiContent\Tables\AiContentTable;
use DoanQuang\AiContent\Forms\AiContentForm;
use DoanQuang\AiContent\Forms\AiContentSettingForm;
use DoanQuang\AiContent\Services\AIStreamService;
use Illuminate\Http\Request;

class AiContentController extends BaseController
{

    private AIStreamService $aiStreamService;

    public function __construct()
    {
        $this->aiStreamService = app(AIStreamService::class);
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/ai-content::ai-content.name')), route('ai-content.index'));
    }

    public function index(AiContentTable $table)
    {
        $this->pageTitle(trans('plugins/ai-content::ai-content.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/ai-content::ai-content.create'));

        return AiContentForm::create()->renderForm();
    }

    public function store(AiContentRequest $request)
    {
        $form = AiContentForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('ai-content.index'))
            ->setNextUrl(route('ai-content.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(AiContent $aiContent)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $aiContent->name]));

        return AiContentForm::createFromModel($aiContent)->renderForm();
    }

    public function update(AiContent $aiContent, AiContentRequest $request)
    {
        AiContentForm::createFromModel($aiContent)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('ai-content.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(AiContent $aiContent)
    {
        return DeleteResourceAction::make($aiContent);
    }

    public function stream(Request $request)
    {
        // Rate limiting - max 10 requests per minute per user
        $key = 'ai_content_stream_' . ($request->ip() ?? 'unknown');
        $maxAttempts = 10;
        $decayMinutes = 1;
        
        if (cache()->has($key) && cache()->get($key) >= $maxAttempts) {
            return response()->json([
                'error' => trans('plugins/ai-content::ai-content.error_rate_limit')
            ], 429);
        }
        
        // Increment request count
        cache()->put($key, (cache()->get($key, 0) + 1), $decayMinutes * 60);
        
        // Validate request data
        $validated = $request->validate([
            'message' => 'required|string|min:3|max:1000',
            'prompt_id' => 'nullable|integer|exists:ai_content_prompts,id',
            'externalId' => 'nullable|string|max:255',
            'provider' => 'required|string|in:openai,gemini,claude',
        ], [
            'message.required' => trans('plugins/ai-content::ai-content.error_message_required'),
            'message.min' => trans('plugins/ai-content::ai-content.error_message_min'),
            'message.max' => trans('plugins/ai-content::ai-content.error_message_max'),
            'prompt_id.exists' => trans('plugins/ai-content::ai-content.error_prompt_not_found'),
            'provider.in' => trans('plugins/ai-content::ai-content.error_provider_not_supported'),
        ]);
        $topic = $validated['message'];
        $promptId = $validated['prompt_id'] ?? null;
        $externalId = $validated['externalId'] ?? null;
        $provider = $validated['provider'];

        // Check if provider is available
        if (!$this->aiStreamService->isProviderSupported($provider)) {
            return response()->json([
                'error' => trans('plugins/ai-content::ai-content.error_provider_not_supported'),
                'available_providers' => $this->aiStreamService->getAvailableProviders()
            ], 400);
        }

        // Check if any provider is configured
        if (empty($this->aiStreamService->getAvailableProviders())) {
            return response()->json([
                'error' => trans('plugins/ai-content::ai-content.error_no_provider'),
                'settings_url' => route('ai-content.settings')
            ], 400);
        }

        // Load prompt template from database with cache
        $promptTemplate = $this->getPromptTemplate($promptId);
        
        // Validate prompt template
        if (empty($promptTemplate)) {
            return response()->json([
                'error' => trans('plugins/ai-content::ai-content.error_load_prompt')
            ], 400);
        }
        
        $message = $this->buildPrompt($topic, $promptTemplate);
        
        // Validate final message length
        if (strlen($message) > 4000) {
            return response()->json([
                'error' => trans('plugins/ai-content::ai-content.error_prompt_too_long')
            ], 400);
        }

        return $this->aiStreamService->stream($message, $externalId, $provider);
    }

    /**
     * Get prompt template from database with cache
     */
    private function getPromptTemplate($promptId): string
    {
        if (!$promptId) {
            return $this->getDefaultPrompt();
        }

        // Try to get from cache first
        $cacheKey = "ai_content_prompt_{$promptId}";
        $promptTemplate = cache()->remember($cacheKey, 3600, function () use ($promptId) {
            $aiContent = \DoanQuang\AiContent\Models\AiContent::where('id', $promptId)
                ->where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)
                ->first();
            return $aiContent ? $aiContent->prompt_content : null;
        });

        if ($promptTemplate) {
            return $promptTemplate;
        }

        // Fallback to default prompt if not found
        return $this->getDefaultPrompt();
    }

    /**
     * Build prompt with topic replacement
     */
    private function buildPrompt(string $topic, string $promptTemplate): string
    {
        return str_replace('{topic}', $topic, $promptTemplate);
    }

    /**
     * Get default prompt template
     */
    public function getDefaultPrompt(): string
    {
        return "You are an expert content writer. Write a blog post with the title \"{topic}\".
                    Format the content using proper HTML tags:
                    - Use <h1> for the main title
                    - Use <h2> for main sections
                    - Use <h3> for subsections
                    - Use <h4> for smaller sections
                    - Use <p> for paragraphs
                    - Use <strong> or <b> for emphasis
                    - Use <em> or <i> for italics
                    - Use <ul> and <li> for unordered lists
                    - Use <ol> and <li> for ordered lists
                    - Use <blockquote> for quotations
                    - Use <code> for code snippets
                    - Use <pre><code> for code blocks
                    The length of the article should be between 1000 and 3000 words to ensure comprehensive and valuable information for readers.
                    Structure the content with a clear hierarchy of headings to help readers easily find the information they need.
                The content should be arranged in a logical order and provide complete information to answer readers\' questions.
                Please detect the language of \"{topic}\" and write the content in that same language.";
    }
}
