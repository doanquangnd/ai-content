@extends('core/setting::forms.partials.action')

@section('content')
    <a href="{{ route('ai-content.index') }}"
        class="btn btn-info"
    >
        {{ trans('plugins/ai-content::ai-content.prompt_templates') }}
    </a> 
@stop
