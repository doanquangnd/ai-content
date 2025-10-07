import * as showdown from 'showdown';
require('showdown-twitter');

let externalId = null;
let promptId = "";
let selectedProvider = 'gemini'; // Default provider - will be updated from PHP
let isStreaming = false; // Flag to prevent multiple requests
let cleanedContent = '';

// Translation function
function trans(key) {
    return window.trans && window.trans[key] ? window.trans[key] : key;
}

// Định nghĩa hàm thông báo dự phòng nếu toastr không tồn tại
if (typeof toastr === 'undefined') {
    window.toastr = {
        success: function(message) {
            if (typeof Botble !== 'undefined' && Botble.showSuccess) {
                Botble.showSuccess(message);
            } else {
                alert('Success: ' + message);
            }
        },
        error: function(message) {
            console.error('Error:', message);
            if (typeof Botble !== 'undefined' && Botble.showError) {
                Botble.showError(message);
            } else {
                alert('Error: ' + message);
            }
        }
    };
}

// Hàm xử lý stream dữ liệu từ AI và hiển thị trong preview
function ajaxAi(button, ask, callback) {
    try {
        // Kiểm tra xem có đang streaming không
        if (isStreaming) {
            console.log('Đang trong quá trình streaming, bỏ qua request');
            return;
        }

        // Đánh dấu đang streaming
        isStreaming = true;

        // Disable các input fields
        disableInputs();

        // Ngăn chặn đóng modal khi đang streaming
        preventModalClose();

        // Vô hiệu hóa nút và hiển thị trạng thái đang tải
        $(button).prop('disabled', true).addClass('button-loading');

        // Lấy container preview
        const previewContainer = document.getElementById('ai-content-preview-container');
        const streamingIndicator = document.querySelector('.ai-content-streaming-indicator');

        // Xóa nội dung cũ và thêm class streaming
        if (previewContainer) {
            previewContainer.style.display = 'block';
            previewContainer.innerHTML = '<p><i class="fa-solid fa-spinner fa-spin me-2"></i>' + trans('plugins/ai-content::ai-content.loading_content') + '...</p>';
            previewContainer.classList.add('streaming');
        }

        // Hiển thị indicator
        if (streamingIndicator) {
            streamingIndicator.style.display = 'block';
        }

        // Biến lưu trữ nội dung tích lũy
        let fullContent = '';

        // Kiểm tra nếu đầu vào rỗng
        if (!ask || ask.trim() === '') {
            Botble.showError(trans('plugins/ai-content::ai-content.content_is_empty'));
            resetUI();
            if (typeof callback === 'function') {
                callback(null);
            }
            return;
        }

        // Tạo URL và tham số
        const url = new URL(window.AiContentRoute.stream, window.location.origin);
        const params = new URLSearchParams({
            'message': ask,
            'prompt_id': promptId,
            'provider': selectedProvider,
        });
        
        // Chỉ thêm externalId nếu có giá trị
        if (externalId) {
            params.append('externalId', externalId);
        }

        // Biến theo dõi trạng thái
        let receivedFirstData = false;

        // Thiết lập timeout cho kết nối ban đầu
        let connectionTimeout = setTimeout(() => {
            if (!receivedFirstData) {
                Botble.showError(trans('plugins/ai-content::ai-content.connection_timeout'));
                resetUI();
                if (typeof callback === 'function') {
                    callback(null);
                }
            }
        }, 60000);

        // Khởi tạo EventSource để stream dữ liệu
        const source = new EventSource(url.toString() + '?' + params.toString());

        // Xử lý khi nhận được message
        source.addEventListener('message', function(event) {
            handleStreamData(event.data);
        });

        // Xử lý sự kiện update
        source.addEventListener('update', function(event) {
            handleStreamData(event.data);
        });

        // Xử lý lỗi
        source.addEventListener('error', function(event) {
            console.error('Lỗi stream:', event);

            if (!receivedFirstData) {
                Botble.showError(trans('plugins/ai-content::ai-content.connection_error'));
            } else {
                Botble.showError(trans('plugins/ai-content::ai-content.connection_timeout'));
            }

            resetUI();

            // Nếu đã nhận được dữ liệu, hiển thị nội dung hiện có
            if (fullContent.trim() && previewContainer) {
                previewContainer.innerHTML = fullContent;
            }

            // Gọi callback nếu có
            if (typeof callback === 'function') {
                callback(null);
            }
        });

        // Hàm xử lý dữ liệu từ stream
        function handleStreamData(data) {
            // Đánh dấu đã nhận dữ liệu đầu tiên
            if (!receivedFirstData) {
                receivedFirstData = true;
                clearTimeout(connectionTimeout);

                // Xóa thông báo "Đang tải"
                if (previewContainer) {
                    previewContainer.innerHTML = '';
                }
            }

            // Kiểm tra nếu stream kết thúc
            if (data === '[DONE]' || data === '</stream>') {
                finishStreaming();
                return;
            }

            // Xử lý dữ liệu
            processStreamData(data);
        }

        // Xử lý dữ liệu stream
        function processStreamData(data) {
            try {
                // Xử lý JSON nếu có
                if (data.trim().startsWith('{') && data.trim().endsWith('}')) {
                    try {
                        const jsonData = JSON.parse(data);
                        if (jsonData && jsonData.external_id) {
                            externalId = jsonData.external_id;
                            return; // Không cập nhật nội dung
                        }
                        if (jsonData && jsonData.content) {
                            data = jsonData.content;
                        }
                    } catch (e) {
                        // Xử lý như text thường
                    }
                }

                // Cập nhật nội dung tích lũy
                fullContent += data;

                // Clean HTML content to remove document structure
                cleanedContent = cleanHtmlContent(fullContent);

                // Cập nhật preview với HTML đã được làm sạch
                if (previewContainer) {
                    previewContainer.innerHTML = cleanedContent;

                    // Luôn scroll xuống dưới khi đang streaming để người dùng thấy nội dung mới
                    setTimeout(() => {
                        scrollToBottom();
                    }, 10);
                }

                // Cập nhật indicator
                updateStreamingIndicator();
            } catch (error) {
                console.error('Lỗi xử lý dữ liệu stream:', error);
                // Không dừng stream nếu xử lý lỗi
            }
        }

        // Clean HTML content to remove document structure tags
        function cleanHtmlContent(content) {
            // Remove HTML document structure tags
            content = content.replace(/<!DOCTYPE[^>]*>/gi, '');
            content = content.replace(/<html[^>]*>/gi, '');
            content = content.replace(/<\/html>/gi, '');
            content = content.replace(/<head[^>]*>[\s\S]*?<\/head>/gi, '');
            content = content.replace(/<body[^>]*>/gi, '');
            content = content.replace(/<\/body>/gi, '');
            content = content.replace(/<title[^>]*>[\s\S]*?<\/title>/gi, '');
            content = content.replace(/<meta[^>]*>/gi, '');
            
            // Remove any remaining document structure
            content = content.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
            content = content.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '');
            
            // Remove backtick characters
            // content = content.replace(/`/g, '');
            // content = content.replace(/html/g, '');
            
            // Clean up extra whitespace and newlines
            // content = content.replace(/\s+/g, ' ');
            content = content.trim();
            
            return content;
        }

        // Cuộn xuống cuối modal hoặc trang
        function scrollToBottom() {
            // Tìm modal hiện tại
            const modal = document.querySelector('.modal.show');
            if (!modal) {
                // Nếu không có modal, scroll window
                window.scrollTo({
                    top: document.documentElement.scrollHeight,
                    behavior: 'auto'
                });
                return;
            }

            // Scroll modal
            const modalBody = modal.querySelector('.modal-body');
            if (modalBody) {
                modalBody.scrollTo({
                    top: modalBody.scrollHeight,
                    behavior: 'auto'
                });
            }
        }

        // Cập nhật indicator với thông tin tiến trình
        function updateStreamingIndicator() {
            if (streamingIndicator) {
                const dots = (fullContent.length % 4) + 1;
                streamingIndicator.textContent = trans('plugins/ai-content::ai-content.loading_content') + '.'.repeat(dots);
            }
        }

        // Hoàn tất quá trình streaming
        function finishStreaming() {
            resetUI();

            // Hiển thị thông báo thành công
            Botble.showSuccess(trans('plugins/ai-content::ai-content.content_created_successfully'));

            // Clean HTML content before final display
            cleanedContent = cleanHtmlContent(fullContent);

            // Cập nhật preview với nội dung cuối cùng đã được làm sạch
            if (previewContainer && cleanedContent.trim()) {
                previewContainer.innerHTML = cleanedContent;
            }

            // Gọi callback nếu có, truyền nội dung đầy đủ đã được làm sạch
            if (typeof callback === 'function') {
                callback(cleanedContent);
            }

            autoImportToEditor();

        }

        // Reset UI khi kết thúc hoặc lỗi
        function resetUI() {
            // Đóng stream
            if (source) {
                source.close();
            }

            // Xóa timeout
            clearTimeout(connectionTimeout);

            // Reset streaming flag
            isStreaming = false;

            // Khôi phục trạng thái nút
            $(button).prop('disabled', false).removeClass('button-loading');

            // Enable các input fields
            enableInputs();

            // Cho phép đóng modal
            allowModalClose();

            // Ẩn indicator
            if (streamingIndicator) {
                streamingIndicator.style.display = 'none';
            }

            // Loại bỏ class streaming
            if (previewContainer) {
                previewContainer.classList.remove('streaming');
            }
            
        }

    } catch (error) {
        console.error('Lỗi khởi tạo:', error);
        
        // Reset streaming flag
        isStreaming = false;
        
        $(button).prop('disabled', false).removeClass('button-loading');
        Botble.showError(trans('plugins/ai-content::ai-content.connection_error') + ': ' + error.message);

        // Enable các input fields khi có lỗi
        enableInputs();

        // Cho phép đóng modal khi có lỗi
        allowModalClose();

        // Reset UI
        const previewContainer = document.getElementById('ai-content-preview-container');
        const streamingIndicator = document.querySelector('.ai-content-streaming-indicator');

        if (previewContainer) {
            previewContainer.classList.remove('streaming');
        }

        if (streamingIndicator) {
            streamingIndicator.style.display = 'none';
        }

        // Gọi callback nếu có
        if (typeof callback === 'function') {
            callback(null);
        }
    }
}

// Hàm disable các input fields
function disableInputs() {
    // Disable provider radio buttons
    const providerRadios = document.querySelectorAll('input[name="provider"]');
    providerRadios.forEach(radio => {
        radio.disabled = true;
        const label = radio.parentElement;
        if (label) {
            label.classList.add('disabled');
        }
    });

    // Disable prompt select
    const promptSelect = document.getElementById('completion-select-prompt');
    if (promptSelect) {
        promptSelect.disabled = true;
    }

    // Disable textarea
    const textarea = document.getElementById('completion-ask');
    if (textarea) {
        textarea.disabled = true;
        textarea.setAttribute('readonly', true);
    }

    // Hide re-import to editor button
    const reImportToEditor = document.querySelector('.re-import-to-editor');
    if (reImportToEditor) {
        reImportToEditor.style.display = 'none';
    }
}

// Hàm enable các input fields
function enableInputs() {
    // Enable provider radio buttons
    const providerRadios = document.querySelectorAll('input[name="provider"]');
    providerRadios.forEach(radio => {
        radio.disabled = false;
        const label = radio.parentElement;
        if (label) {
            label.classList.remove('disabled');
        }
    });

    // Enable prompt select
    const promptSelect = document.getElementById('completion-select-prompt');
    if (promptSelect) {
        promptSelect.disabled = false;
    }

    // Enable textarea
    const textarea = document.getElementById('completion-ask');
    if (textarea) {
        textarea.disabled = false;
        textarea.removeAttribute('readonly');
    }

    // Show re-import to editor button
    const reImportToEditor = document.querySelector('.re-import-to-editor');
    if (reImportToEditor) {
        reImportToEditor.style.display = 'block';
    }
}

// Hàm ngăn chặn đóng modal
function preventModalClose() {
    const modal = document.getElementById('ai-content-modal');
    if (modal) {
        // Lấy instance Bootstrap Modal
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            // Cấu hình modal để không thể đóng
            modalInstance._config.backdrop = 'static';
            modalInstance._config.keyboard = false;
        }
        
        // Disable nút close
        const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"]');
        closeButtons.forEach(button => {
            button.disabled = true;
        });
    }
}

// Hàm cho phép đóng modal
function allowModalClose() {
    const modal = document.getElementById('ai-content-modal');
    if (modal) {
        // Lấy instance Bootstrap Modal
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            // Cấu hình modal để có thể đóng
            modalInstance._config.backdrop = true;
            modalInstance._config.keyboard = true;
        }
        
        // Enable nút close
        const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"]');
        closeButtons.forEach(button => {
            button.disabled = false;
        });
    }
}

// Tự động import nội dung vào editor và kích hoạt chức năng chỉnh sửa
function autoImportToEditor() {
    if (!cleanedContent) return;
    try {
        // Import vào CKEditor nếu có
        if (window.EDITOR && window.EDITOR.CKEDITOR && Object.keys(window.EDITOR.CKEDITOR).length !== 0) {
            const editorInstance = window.EDITOR.CKEDITOR['content'];
            if (editorInstance && typeof editorInstance.setData === 'function') {
                editorInstance.setData(cleanedContent);
                Botble.showSuccess(trans('plugins/ai-content::ai-content.auto_add_content'));
            } else {
                console.error('Không tìm thấy instance CKEditor hợp lệ', window.EDITOR.CKEDITOR);
            }
        }

        // Import vào TinyMCE nếu có
        if (typeof window.tinyMCE !== 'undefined' && window.tinyMCE.activeEditor) {
            window.tinyMCE.activeEditor.setContent(cleanedContent);
            Botble.showSuccess(trans('plugins/ai-content::ai-content.auto_add_content'));
        }
    } catch (error) {
        console.error('Lỗi khi import vào editor:', error);
        Botble.showError(trans('plugins/ai-content::ai-content.auto_add_content_error') + ': ' + error.message);
    }
}

// Thêm container cho preview nếu chưa có
document.addEventListener("DOMContentLoaded", function() {
    // Tạo container để preview nội dung trong modal
    if (!document.getElementById('ai-content-preview-container')) {
        const previewContainer = document.createElement('div');
        previewContainer.id = 'ai-content-preview-container';
        previewContainer.style.display = 'none';
        previewContainer.innerHTML = '<p class="text-muted">Nội dung sẽ được hiển thị ở đây...</p>';

        // Chèn container vào sau textarea input trong modal
        const inputArea = document.getElementById('completion-ask');
        if (inputArea && inputArea.parentNode) {
            inputArea.parentNode.insertBefore(previewContainer, inputArea.nextSibling);
        } else {
            // Nếu không tìm thấy input, thêm vào modal body
            const modalBody = document.getElementById('modal-ai-content-body');
            if (modalBody) {
                modalBody.appendChild(previewContainer);
            }
        }
    }

    // Tạo indicator cho streaming
    if (!document.querySelector('.ai-content-streaming-indicator')) {
        const indicator = document.createElement('div');
        indicator.className = 'ai-content-streaming-indicator';
        indicator.textContent = trans('plugins/ai-content::ai-content.loading_content') + '...';
        indicator.style.position = 'fixed';
        indicator.style.bottom = '20px';
        indicator.style.right = '20px';
        indicator.style.backgroundColor = '#4CAF50';
        indicator.style.color = 'white';
        indicator.style.padding = '8px 15px';
        indicator.style.borderRadius = '5px';
        indicator.style.boxShadow = '0 2px 5px rgba(0,0,0,0.3)';
        indicator.style.zIndex = '9999';
        indicator.style.display = 'none';
       
        document.body.appendChild(indicator);
    }

    // Cập nhật label cho nút chính
    const generateBtn = document.querySelector('.btn-ai-content-completion');
    if (generateBtn) {
        generateBtn.textContent = trans('plugins/ai-content::ai-content.create_content_automatically');
        generateBtn.title = trans('plugins/ai-content::ai-content.create_content_automatically_title');
    }

    // Khởi tạo selectedProvider từ PHP config hoặc radio buttons
    if (typeof window.aiContentProviders !== 'undefined' && window.aiContentProviders.defaultProvider) {
        selectedProvider = window.aiContentProviders.defaultProvider;
    } else {
        const selectedRadio = document.querySelector('input[name="provider"]:checked');
        if (selectedRadio && selectedRadio.value) {
            selectedProvider = selectedRadio.value;
        }
    }

    // Add event listener for provider radio buttons
    document.addEventListener('change', function(e) {
        if (e.target.name === 'provider' && e.target.type === 'radio') {
            selectedProvider = e.target.value;
        }
    });

    // Thêm event listener cho modal để đảm bảo scroll hoạt động
    const modal = document.getElementById('ai-content-modal');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function() {
            // Reset scroll position khi modal mở
            const modalBody = modal.querySelector('.modal-body');
            if (modalBody) {
                modalBody.scrollTop = 0;
            }
        });

        // Ngăn chặn đóng modal bằng phím ESC khi đang streaming
        modal.addEventListener('keydown', function(e) {
            if (isStreaming && e.key === 'Escape') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });

        // Ngăn chặn đóng modal khi click ra ngoài (backdrop)
        modal.addEventListener('click', function(e) {
            if (isStreaming && e.target === modal) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });

        // Ngăn chặn đóng modal bằng mọi cách khi đang streaming
        modal.addEventListener('hide.bs.modal', function(e) {
            if (isStreaming) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }

});

// Sự kiện thay đổi provider
$(document).on('change', '#completion-select-provider', function (event) {
    event.preventDefault();
    event.stopPropagation();
    selectedProvider = $(this).val();
});

// Sự kiện thay đổi loại nội dung
$(document).on('change', '#completion-select-prompt', function (event) {
    event.preventDefault();
    event.stopPropagation();
    promptId = $(this).val();
});

// Sự kiện click vào nút tạo nội dung
$(document).on('click', '.btn-ai-content-completion', function (event) {
    event.preventDefault();
    event.stopPropagation();
    cleanedContent = '';
    // Lấy nội dung từ input
    let ask = $('#completion-ask').val();
    if (!ask || ask.trim() === '') {
        Botble.showError(trans('plugins/ai-content::ai-content.content_is_empty'));
        return;
    }

    // Lấy promptId từ dropdown
    promptId = $('#completion-select-prompt').val();
    
    // Tạo nội dung với prompt được chọn
    ajaxAi(this, ask);
});

// Sự kiện click vào nút re-import to editor
$(document).on('click', '.re-import-to-editor', function (event) {
    event.preventDefault();
    event.stopPropagation();
    autoImportToEditor();
});

// Gọi khởi tạo khi tài liệu đã sẵn sàng
$(document).ready(function() {
    // Tạo biến mặc định nếu chưa được định nghĩa
    window.aiContentConfig = window.aiContentConfig || {
        enableInlineEditing: false,
        routes: {
            inlineEdit: window.AiContentRoute?.stream || ''
        }
    };
});

