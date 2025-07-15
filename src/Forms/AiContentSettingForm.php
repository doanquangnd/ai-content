<?php

namespace DoanQuang\AiContent\Forms;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;
use DoanQuang\AiContent\Http\Requests\AiContentSettingRequest;

class AiContentSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/ai-content::ai-content.name'))
            ->setSectionDescription(trans('plugins/ai-content::ai-content.description'))
            ->setValidatorClass(AiContentSettingRequest::class)
            ->setUrl(route('ai-content.settings.update'))
            ->setActionButtons(view('plugins/ai-content::partials.action-buttons')->with('form', $this->getFormOption('id'))->render()) 
            ->add(
                'ai_content_openai_api_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/ai-content::ai-content.openai_api_key'))
                    ->value(setting('ai_content_openai_api_key'))
                    ->helperText(trans('plugins/ai-content::ai-content.openai_api_key_help'))
                    ->placeholder(trans('plugins/ai-content::ai-content.openai_api_key_placeholder'))
            )
            ->add(
                'ai_content_openai_api_model',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/ai-content::ai-content.openai_api_model'))
                    ->value(setting('ai_content_openai_api_model'))
                    ->placeholder(trans('plugins/ai-content::ai-content.openai_api_model_placeholder'))
            )
            ->add(
                'ai_content_openai_organization',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/ai-content::ai-content.openai_organization'))
                    ->value(setting('ai_content_openai_organization'))
                    ->placeholder(trans('plugins/ai-content::ai-content.openai_organization_placeholder'))
            )
            ->add(
                'ai_content_gemini_api_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/ai-content::ai-content.gemini_api_key'))
                    ->value(setting('ai_content_gemini_api_key'))
                    ->helperText(trans('plugins/ai-content::ai-content.gemini_api_key_help'))
                    ->placeholder(trans('plugins/ai-content::ai-content.gemini_api_key_placeholder'))
            )
            ->add(
                'ai_content_gemini_api_model',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/ai-content::ai-content.gemini_api_model'))
                    ->value(setting('ai_content_gemini_api_model'))
                    ->placeholder(trans('plugins/ai-content::ai-content.gemini_api_model_placeholder'))
            )
            ->add(
                'ai_content_claude_api_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/ai-content::ai-content.claude_api_key'))
                    ->value(setting('ai_content_claude_api_key'))
                    ->helperText(trans('plugins/ai-content::ai-content.claude_api_key_help'))
                    ->placeholder(trans('plugins/ai-content::ai-content.claude_api_key_placeholder'))
            )
            ->add(
                'ai_content_claude_api_model',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/ai-content::ai-content.claude_api_model'))
                    ->value(setting('ai_content_claude_api_model'))
                    ->placeholder(trans('plugins/ai-content::ai-content.claude_api_model_placeholder'))
            );
    }
}
