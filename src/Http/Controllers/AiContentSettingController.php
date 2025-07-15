<?php

namespace DoanQuang\AiContent\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use DoanQuang\AiContent\Http\Requests\AiContentRequest;
use DoanQuang\AiContent\Models\AiContent;
use Botble\Base\Http\Controllers\BaseController;
use DoanQuang\AiContent\Tables\AiContentTable;
use DoanQuang\AiContent\Forms\AiContentForm;
use DoanQuang\AiContent\Forms\AiContentSettingForm;
use DoanQuang\AiContent\Http\Requests\AiContentSettingRequest;
use DoanQuang\AiContent\Services\AIStreamService;
use Illuminate\Http\Request;

class AiContentSettingController extends BaseController
{

    public function edit()
    {
        $this->pageTitle(trans('plugins/ai-content::ai-content.name'));

        return AiContentSettingForm::create()->renderForm();
    }

    public function update(AiContentSettingRequest $request)
    {
        setting()->set('ai_content_openai_api_key', $request->input('ai_content_openai_api_key'));
        setting()->set('ai_content_openai_api_model', $request->input('ai_content_openai_api_model'));
        setting()->set('ai_content_openai_organization', $request->input('ai_content_openai_organization'));
        setting()->set('ai_content_gemini_api_key', $request->input('ai_content_gemini_api_key'));
        setting()->set('ai_content_gemini_api_model', $request->input('ai_content_gemini_api_model'));
        setting()->set('ai_content_claude_api_key', $request->input('ai_content_claude_api_key'));
        setting()->set('ai_content_claude_api_model', $request->input('ai_content_claude_api_model'));
        setting()->save();
        return $this
            ->httpResponse()
            ->setPreviousUrl(route('ai-content.settings'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

}
