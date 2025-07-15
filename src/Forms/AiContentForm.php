<?php

namespace DoanQuang\AiContent\Forms;

use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use DoanQuang\AiContent\Http\Requests\AiContentRequest;
use DoanQuang\AiContent\Models\AiContent;

class AiContentForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(AiContent::class)
            ->setValidatorClass(AiContentRequest::class)
            ->add(
                'name',
                TextField::class,
                NameFieldOption::make()
                    ->label(trans('plugins/ai-content::ai-content.prompt_name'))
                    ->placeholder(trans('plugins/ai-content::ai-content.prompt_name_placeholder'))
                    ->maxLength(50)
                    ->required()
            )
            ->add(
                'prompt_content',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/ai-content::ai-content.prompt_content'))
                    ->placeholder(trans('plugins/ai-content::ai-content.prompt_content_placeholder'))
                    ->rows(20)
                    ->maxLength(2500)
                    ->allowOverLimit()
                    ->required()
            )
            ->add(
                'prompt_content_examples',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content($this->getPromptContentExamples())
            )
            ->add('status', SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
    }

    protected function getPromptContentExamples(): string
    {
        return view('plugins/ai-content::partials.prompt-examples')->render();
    }
}
