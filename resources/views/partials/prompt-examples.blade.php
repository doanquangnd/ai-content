<fieldset class="form-fieldset">
    <div class="card border-info mb-3">
       <div class="card-body">
          <div class="d-flex align-items-start">
             <div class="me-3">
                <div class="avatar avatar-lg bg-info-lt">
                   <svg class="icon icon-lg svg-icon-ti-ti-sitemap" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M3 15m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
                      <path d="M15 15m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
                      <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
                      <path d="M6 15v-1a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v1"></path>
                      <path d="M12 9l0 3"></path>
                   </svg>
                </div>
             </div>
             <div class="flex-fill">
                <h4 class="card-title mb-2">{{ trans('plugins/ai-content::ai-content.prompt_content_examples') }}</h4>
                <div class="mb-5 p-2 bg-success-lt rounded">
                  <div class="d-flex align-items-center">
                          <svg class="icon text-success me-2 svg-icon-ti-ti-info-circle" xmlns="http://www.w3.org/2000/svg" width="24"
                              height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round">
                              <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                              <path d="M12 9h.01"></path>
                              <path d="M11 12h1v4h1"></path>
                          </svg> <small class="text-success fw-medium">
                          {!! trans('plugins/ai-content::ai-content.prompt_content_examples_description') !!}
                      </small>
                  </div>
              </div>
              <div class="row g-3">
                    <pre class="bg-dark text-light rounded" style="font-size: 0.875rem; padding: 10px; margin: 0;">
                        <code>{!! htmlspecialchars('
You are an expert content writer. Write a blog post with the title \"{topic}\".
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
Please detect the language of \"{topic}\" and write the content in that same language.
                        ') !!}</code>
                    </pre>
              </div>
             </div>
          </div>
       </div>
    </div>
 </fieldset>