<div class="d-inline-block editor-action-item">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ai-content-modal">
        <i class="fa-solid fa-robot"></i> {{ trans('plugins/ai-content::ai-content.button_show') }}
    </button>
</div>


<div class="modal fade"
     id="ai-content-modal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-xl" style="min-width: 80vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-robot me-2"></i>
                    {{ trans('plugins/ai-content::ai-content.name') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-ai-content-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    @if (!\DoanQuang\AiContent\Helpers\AIProviderHelper::hasAnyProvider())
                                        <div class="alert alert-danger" role="alert">
                                            {{ trans('plugins/ai-content::ai-content.alert_api') }}
                                        </div>
                                    @endif

                                    @if (\DoanQuang\AiContent\Helpers\AIProviderHelper::hasAnyProvider())
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label class="control-label mb-3">{{ trans('plugins/ai-content::ai-content.select_provider') }}</label>
                                                <div class="provider-radio-group">
                                                    @foreach(\DoanQuang\AiContent\Helpers\AIProviderHelper::getAvailableProviders() as $provider)
                                                        <div class="provider-radio-item">
                                                            <input type="radio" 
                                                                   id="provider-{{ $provider }}" 
                                                                   name="provider" 
                                                                   value="{{ $provider }}" 
                                                                   class="provider-radio-input"
                                                                   {{ $provider == \DoanQuang\AiContent\Helpers\AIProviderHelper::getDefaultProvider() ? 'checked = "checked"' : '' }}>
                                                            <label for="provider-{{ $provider }}" class="provider-radio-label">
                                                                <div class="provider-icon">
                                                                    @if($provider === 'openai')
                                                                        <i class="fas fa-robot text-primary"></i>
                                                                    @elseif($provider === 'gemini')
                                                                        <i class="fas fa-brain text-warning"></i>
                                                                    @elseif($provider === 'claude')
                                                                        <i class="fa-solid fa-a text-info"></i>
                                                                    @else
                                                                        <i class="fas fa-cog text-secondary"></i>
                                                                    @endif
                                                                </div>
                                                                <div class="provider-info">
                                                                    <div class="provider-name">{{ trans('plugins/ai-content::ai-content.provider_' . $provider) }}</div>
                                                                    <div class="provider-description">
                                                                        @if($provider === 'openai')
                                                                            GPT-4o-mini
                                                                        @elseif($provider === 'gemini')
                                                                            Gemini 2.0 Flash
                                                                        @elseif($provider === 'claude')
                                                                            Claude 3.5 Sonnet
                                                                        @else
                                                                            AI Assistant
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">{{ trans('plugins/ai-content::ai-content.select_prompt') }}</label>
                                                <small class="text-muted">{{ trans('plugins/ai-content::ai-content.select_prompt_description') }}</small>
                                                
                                                <select class="form-control" id="completion-select-prompt">
                                                    <option value="">{{ trans('plugins/ai-content::ai-content.select_default_prompt') }}</option>
                                                    @foreach(\DoanQuang\AiContent\Models\AiContent::where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)->get() as $prompt)
                                                        <option value="{{ $prompt->id }}">{{ $prompt->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label class="control-label">{{ trans('plugins/ai-content::ai-content.prompt') }}</label>
                                                <textarea type="text" class="form-control" value="" id="completion-ask"></textarea>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('plugins/ai-content::ai-content.close') }}</button>
                @if (\DoanQuang\AiContent\Helpers\AIProviderHelper::hasAnyProvider())

                    <button type="button" class="btn btn-info re-import-to-editor" style="display: none;">{{ trans('plugins/ai-content::ai-content.re_import_to_editor') }}</button>
                    <button type="button" class="btn btn-primary btn-ai-content-completion">{{ trans('plugins/ai-content::ai-content.completion_get') }}</button>
                @endif
            </div>
        </div>
    </div>
</div>

@push('header')

    <script>
        window.AiContentRoute = {
            uuid: "{{ Str::uuid() }}",
            stream: "{{ route('ai-content.stream') }}",
            upload_media: "{{ route('media.files.upload') }}",
            upload_url: "{{ route('media.download_url') }}",
            csrf: "{{ csrf_token() }}"
        };

        // Định nghĩa cấu hình cho tính năng chỉnh sửa inline
        window.aiContentConfig = {
            enableInlineEditing: true,
            routes: {
                inlineEdit: "{{ route('ai-content.stream') }}"
            }
        };

        // Cấu hình AI providers
        window.aiContentProviders = {
            defaultProvider: "{{ \DoanQuang\AiContent\Helpers\AIProviderHelper::getDefaultProvider() }}",
            availableProviders: @json(\DoanQuang\AiContent\Helpers\AIProviderHelper::getAvailableProviders())
        };
        window.trans = {
            'plugins/ai-content::ai-content.content_is_empty': "{{ trans('plugins/ai-content::ai-content.content_is_empty') }}",
            'plugins/ai-content::ai-content.connection_timeout': "{{ trans('plugins/ai-content::ai-content.connection_timeout') }}",
            'plugins/ai-content::ai-content.connection_error': "{{ trans('plugins/ai-content::ai-content.connection_error') }}",
            'plugins/ai-content::ai-content.content_created_successfully': "{{ trans('plugins/ai-content::ai-content.content_created_successfully') }}",
            'plugins/ai-content::ai-content.auto_add_content': "{{ trans('plugins/ai-content::ai-content.auto_add_content') }}",
            'plugins/ai-content::ai-content.auto_add_title': "{{ trans('plugins/ai-content::ai-content.auto_add_title') }}",
            'plugins/ai-content::ai-content.auto_add_content_error': "{{ trans('plugins/ai-content::ai-content.auto_add_content_error') }}",
            'plugins/ai-content::ai-content.loading_content': "{{ trans('plugins/ai-content::ai-content.loading_content') }}",
            'plugins/ai-content::ai-content.create_content_automatically': "{{ trans('plugins/ai-content::ai-content.create_content_automatically') }}",
            'plugins/ai-content::ai-content.create_content_automatically_title': "{{ trans('plugins/ai-content::ai-content.create_content_automatically_title') }}",
            'plugins/ai-content::ai-content.re_import_to_editor': "{{ trans('plugins/ai-content::ai-content.re_import_to_editor') }}",
        }

    </script>
@endpush
