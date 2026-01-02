@php
    use Illuminate\Support\Collection;
    use Spatie\MediaLibrary\MediaCollections\Models\Media;

    // Generate unique ID for this component instance
    $componentId = 'file-component-' . uniqid();
@endphp

@push('css_files')
<style>
.file-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
    margin-top: 15px;
}

.file-container {
    position: relative;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    background: #fff;
    cursor: pointer;
    border: 1px solid #e9ecef;
}

.file-container:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.file-container .file-actions {
    opacity: 0;
}

.file-container:hover .file-actions {
    opacity: 1;
}

.file-container img,
.file-container video {
    width: 100%;
    height: 80px;
    object-fit: cover;
    display: block;
}

.file-container .file-icon {
    width: 100%;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    font-size: 1.5rem;
}

.file-container .file-name {
    padding: 6px;
    font-size: 0.7rem;
    text-align: center;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    word-break: break-word;
    line-height: 1.2;
}

.file-actions {
    position: absolute;
    top: 2px;
    right: 2px;
    display: flex;
    flex-direction: row;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.2s;
    z-index: 10;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 4px;
    backdrop-filter: blur(2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    align-items: center;
    justify-content: center;
}

.file-container:hover .file-actions {
    opacity: 1;
}

.delete-btn2, .download-btn2 {
    width: 26px;
    height: 26px;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    color: white;
    position: relative;
}

.delete-btn2 {
    background: #dc3545;
    margin-left: 4px;
}

.delete-btn2:hover {
    background: #c82333;
    transform: scale(1.05);
}

.download-btn2 {
    background: #28a745;
}

.download-btn2:hover {
    background: #218838;
    transform: scale(1.05);
}

.download-icon2 {
    font-size: 1.1em;
    vertical-align: middle;
}

/* Lightbox Styles */
.lightbox {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    display: none;
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.lightbox.active {
    display: flex;
}

.lightbox-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    text-align: center;
}

.lightbox-content img,
.lightbox-content video {
    max-width: 100%;
    max-height: 90vh;
    object-fit: contain;
}

.lightbox-content .file-icon {
    font-size: 8rem;
    color: white;
    margin-bottom: 20px;
}

.lightbox-content .file-name {
    color: white;
    font-size: 1.2rem;
    margin-top: 15px;
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 1.5rem;
    cursor: pointer;
    transition: background 0.2s;
}

.lightbox-nav:hover {
    background: rgba(255,255,255,0.3);
}

.lightbox-nav.prev {
    left: 20px;
}

.lightbox-nav.next {
    right: 20px;
}

.lightbox-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 1.2rem;
    cursor: pointer;
    transition: background 0.2s;
    type: button;
}

.lightbox-close:hover {
    background: rgba(255,255,255,0.3);
}

.lightbox-counter {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-size: 1rem;
    background: rgba(0,0,0,0.5);
    padding: 8px 16px;
    border-radius: 20px;
}

.upload-box {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
    transition: border-color 0.2s;
    position: relative;
}

.upload-box:hover {
    border-color: #007bff;
}

.upload-box.has-files {
    padding: 15px;
    text-align: left;
}

.upload-box.has-files .upload-input {
    display: none;
}

.upload-box.has-files .upload-label {
    display: none;
}

.upload-box.has-files .add-more-btn {
    display: inline-block;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 6px 12px;
    font-size: 0.8rem;
    cursor: pointer;
    margin-bottom: 10px;
    transition: background 0.2s;
}

.upload-box.has-files .add-more-btn:hover {
    background: #0056b3;
}

.upload-box:not(.has-files) .file-gallery {
    display: none;
}

.upload-box:not(.has-files) .add-more-btn {
    display: none;
}

.upload-box.has-files .file-gallery {
    margin-top: 0;
}

.upload-input {
    display: none;
}

.upload-box label {
    cursor: pointer;
    color: #007bff;
    font-weight: 500;
    margin-bottom: 0;
}

.upload-box label:hover {
    color: #0056b3;
}
</style>
@endpush
<div class="mb-3 form-group {{$class}}" id="{{ $componentId }}">
    <h6> @lang('trans.'.$title)</h6>

    <div class="upload-box single-upload">
        <input @if($multiple) multiple @endif type="file" name="{{$name}}" class="upload-input single-upload-input"
               accept="{{$accept}}" id="{{$id ?? $componentId . '-input'}}" data-multiple="{{ $multiple ? 'true' : 'false' }}">
        <label for="{{$id ?? $componentId . '-input'}}" class="upload-label">
            <i class="fa-solid fa-cloud-upload-alt" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
            @lang('trans.choose_a_file') @lang('trans.'.$title)
        </label>

        <button type="button" class="add-more-btn" onclick="document.getElementById('{{$id ?? $componentId . '-input'}}').click()">
            <i class="fa-solid fa-plus"></i> @lang('trans.add_more_files')
        </button>

        <div class="file-gallery single-preview" id="{{ $componentId }}-gallery">
            @if ($files instanceof Collection && $files->count() > 0)
                @foreach ($files as $index => $file)
                    @php
                        $fileUrl = $file instanceof Media ? $file->getUrl() : ($file->full_url ?? $file->url ?? '');
                        $fileId = $file->id ?? null;
                        $fileName = $file instanceof Media ? $file->file_name : ($file->name ?? $file->file_name ?? '');
                        $fileExt = $file instanceof Media ? pathinfo($file->file_name, PATHINFO_EXTENSION) : ($file->ext ?? '');
                    @endphp

                    <div class="file-container" data-index="{{ $index }}" data-url="{{ $fileUrl }}" data-name="{{ $fileName }}" data-type="{{ strtolower($fileExt) }}" data-component="{{ $componentId }}" data-existing="true">
                        @if (in_array(strtolower($fileExt),['jpg','jpeg','png','svg','webp','gif']))
                            <img src="{{ $fileUrl }}" alt="{{ $fileName }}">
                        @elseif (in_array(strtolower($fileExt),['mp4','mov','avi','wmv','mkv']))
                            <video src="{{ $fileUrl }}" preload="metadata"></video>
                            <div class="play-overlay">
                                <i class="fa-solid fa-play"></i>
                            </div>
                        @elseif (in_array(strtolower($fileExt),['pdf','doc','docx']))
                            <div class="file-icon">
                                @if(strtolower($fileExt) === 'pdf')
                                    <i class="fa-solid fa-file-pdf text-danger"></i>
                                @else
                                    <i class="fa-solid fa-file-word text-primary"></i>
                                @endif
                            </div>
                            <div class="file-name">{{ $fileName }}</div>
                        @else
                            <div class="file-icon">
                                <i class="fa-solid fa-file text-secondary"></i>
                            </div>
                            <div class="file-name">{{ $fileName }}</div>
                        @endif

                        <div class="file-actions">

                            <button type="button" class="download-btn2 btn-light" onclick="downloadFile('{{ $fileUrl }}', '{{ $fileName }}')">
                                <i class="fa-solid fa-download download-icon2"></i>
                            </button>

                            @if($multiple)
                            <button type="button" class="delete-btn2 btn-danger" data-id="{{ $fileId }}" data-media-id="{{ $fileId }}" data-url="{{ route('admin.files.destroy', $fileId) }}">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            @elseif ($files instanceof Media)
                @php
                    $fileUrl = $files->getUrl();
                    $fileId = $files->id;
                    $fileName = $files->file_name;
                    $fileExt = pathinfo($files->file_name, PATHINFO_EXTENSION);
                @endphp

                <div class="file-container" data-index="0" data-url="{{ $fileUrl }}" data-name="{{ $fileName }}" data-type="{{ strtolower($fileExt) }}" data-component="{{ $componentId }}" data-existing="true">
                    @if (in_array(strtolower($fileExt),['jpg','jpeg','png','svg','webp','gif']))
                        <img src="{{ $fileUrl }}" alt="{{ $fileName }}">
                    @elseif (in_array(strtolower($fileExt),['mp4','mov','avi','wmv','mkv']))
                        <video src="{{ $fileUrl }}" preload="metadata"></video>
                        <div class="play-overlay">
                            <i class="fa-solid fa-play"></i>
                        </div>
                    @elseif (in_array(strtolower($fileExt),['pdf','doc','docx']))
                        <div class="file-icon">
                            @if(strtolower($fileExt) === 'pdf')
                                <i class="fa-solid fa-file-pdf text-danger"></i>
                            @else
                                <i class="fa-solid fa-file-word text-primary"></i>
                            @endif
                        </div>
                        <div class="file-name">{{ $fileName }}</div>
                    @else
                        <div class="file-icon">
                            <i class="fa-solid fa-file text-secondary"></i>
                        </div>
                        <div class="file-name">{{ $fileName }}</div>
                    @endif

                    <div class="file-actions">
                        <button type="button" class="download-btn2 btn-light" onclick="downloadFile('{{ $fileUrl }}', '{{ $fileName }}')">
                            <i class="fa-solid fa-download download-icon2"></i>
                        </button>
                        @if($multiple)
                        <button data-id="{{ $fileId }}" type="button" class="delete-btn2 btn-danger" data-media-id="{{ $fileId }}">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                        @endif
                    </div>
                </div>
            @elseif (is_object($files) && isset($files->ext))
                @php
                    $fileUrl = $files->full_url ?? $files->url ?? '';
                    $fileId = $files->id;
                    $fileName = $files->name ?? $files->file_name ?? '';
                    $fileExt = $files->ext;
                @endphp

                <div class="file-container" data-index="0" data-url="{{ $fileUrl }}" data-name="{{ $fileName }}" data-type="{{ strtolower($fileExt) }}" data-component="{{ $componentId }}" data-existing="true">
                    @if (in_array(strtolower($fileExt),['jpg','jpeg','png','svg','webp','gif']))
                        <img src="{{ $fileUrl }}" alt="{{ $fileName }}">
                    @elseif (in_array(strtolower($fileExt),['mp4','mov','avi','wmv','mkv']))
                        <video src="{{ $fileUrl }}" preload="metadata"></video>
                        <div class="play-overlay">
                            <i class="fa-solid fa-play"></i>
                        </div>
                    @elseif (in_array(strtolower($fileExt),['pdf','doc','docx']))
                        <div class="file-icon">
                            @if(strtolower($fileExt) === 'pdf')
                                <i class="fa-solid fa-file-pdf text-danger"></i>
                            @else
                                <i class="fa-solid fa-file-word text-primary"></i>
                            @endif
                        </div>
                        <div class="file-name">{{ $fileName }}</div>
                    @else
                        <div class="file-icon">
                            <i class="fa-solid fa-file text-secondary"></i>
                        </div>
                        <div class="file-name">{{ $fileName }}</div>
                    @endif

                    <div class="file-actions">
                        <button type="button" class="download-btn2 btn-light" onclick="downloadFile('{{ $fileUrl }}', '{{ $fileName }}')">
                            <i class="fa-solid fa-download download-icon2"></i>
                        </button>
                        @if($multiple)
                        <button data-id="{{ $fileId }}" type="button" class="delete-btn2 btn-danger" data-media-id="{{ $fileId }}">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                        @endif
                    </div>
                </div>
            @else
              {{--  <!-- Debug info - remove this in production -->
                @if(config('app.debug'))
                    <div class="alert alert-info">
                        <strong>Debug Info:</strong><br>
                        Files value: {{ $files === null ? 'NULL' : (is_object($files) ? 'Object' : 'Not Object') }}<br>
                        Files type: {{ $files === null ? 'NULL' : get_class($files) }}<br>
                        Is Collection: {{ $files instanceof Collection ? 'Yes' : 'No' }}<br>
                        Collection count: {{ $files instanceof Collection ? $files->count() : 'N/A' }}<br>
                        Is Media: {{ $files instanceof Media ? 'Yes' : 'No' }}<br>
                        Is Object: {{ is_object($files) ? 'Yes' : 'No' }}<br>
                        Has ext property: {{ is_object($files) && isset($files->ext) ? 'Yes' : 'No' }}
                    </div>
                @endif--}}
            @endif
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div class="lightbox" id="{{ $componentId }}-lightbox">
    <button class="lightbox-close" type="button" onclick="closeLightbox('{{ $componentId }}')">
        <i class="fa-solid fa-times"></i>
    </button>

    <button class="lightbox-nav prev" type="button" onclick="previousMedia('{{ $componentId }}')">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <button class="lightbox-nav next" type="button" onclick="nextMedia('{{ $componentId }}')">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    <div class="lightbox-content">
        <div id="{{ $componentId }}-lightbox-media"></div>
        <div class="file-name" id="{{ $componentId }}-lightbox-filename"></div>
    </div>

    <div class="lightbox-counter" id="{{ $componentId }}-lightbox-counter"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Component-specific variables
window.fileComponents = window.fileComponents || {};

// Initialize this specific component
(function() {
    const componentId = '{{ $componentId }}';
    let currentMediaIndex = 0;
    let mediaItems = [];

    // Store component data globally
    window.fileComponents[componentId] = {
        currentMediaIndex: 0,
        mediaItems: [],
        componentId: componentId
    };

    // Initialize gallery when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const containers = document.querySelectorAll(`#${componentId} .file-container`);
        mediaItems = Array.from(containers);
        window.fileComponents[componentId].mediaItems = mediaItems;

        // Update upload box state
        updateUploadBoxState(componentId);

        containers.forEach((container, index) => {
            container.addEventListener('click', function(e) {
                // Don't open lightbox if clicking on action buttons
                if (e.target.closest('.file-actions')) {
                    return;
                }

                currentMediaIndex = index;
                window.fileComponents[componentId].currentMediaIndex = index;
                openLightbox(componentId);
            });
        });

        // Handle file input changes
        const fileInput = document.getElementById(`${componentId}-input`);
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                handleFileUpload(componentId, e.target.files);
            });
        }
    });
})();

function handleFileUpload(componentId, files) {
    const gallery = document.getElementById(`${componentId}-gallery`);
    const fileInput = document.getElementById(`${componentId}-input`);
    const component = window.fileComponents[componentId];
    const isMultiple = fileInput.getAttribute('data-multiple') === 'true';

    if (!isMultiple) {
        // For single file mode: clear all existing previews and keep only the latest file
        gallery.innerHTML = '';

        // Only keep the last file selected
        if (files.length > 0) {
            const lastFile = files[files.length - 1];

            // Update file input to only have the last file
            const dt = new DataTransfer();
            dt.items.add(lastFile);
            fileInput.files = dt.files;

            // Create preview for the single file
            const fileContainer = createFilePreview(componentId, lastFile, 0);
            gallery.appendChild(fileContainer);
        }
    } else {
        // For multiple file mode: add new files to existing ones
        Array.from(files).forEach((file, index) => {
            const fileContainer = createFilePreview(componentId, file, index);
            gallery.appendChild(fileContainer);
        });
    }

    // Update component media items
    updateComponentMediaItems(componentId);

    // Update upload box state
    updateUploadBoxState(componentId);
}

function createFilePreview(componentId, file, index) {
    const container = document.createElement('div');
    container.className = 'file-container';
    container.setAttribute('data-index', index);
    container.setAttribute('data-name', file.name);
    container.setAttribute('data-type', getFileExtension(file.name));
    container.setAttribute('data-component', componentId);
    container.setAttribute('data-existing', 'false');
    container.setAttribute('data-file', 'true');

    const fileExt = getFileExtension(file.name);
    const fileUrl = URL.createObjectURL(file);

    let content = '';

    if (['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'].includes(fileExt)) {
        content = `<img src="${fileUrl}" alt="${file.name}">`;
    } else if (['mp4', 'mov', 'avi', 'wmv', 'mkv'].includes(fileExt)) {
        content = `
            <video src="${fileUrl}" preload="metadata"></video>
            <div class="play-overlay">
                <i class="fa-solid fa-play"></i>
            </div>
        `;
    } else if (['pdf', 'doc', 'docx'].includes(fileExt)) {
        const iconClass = fileExt === 'pdf' ? 'fa-file-pdf text-danger' : 'fa-file-word text-primary';
        content = `
            <div class="file-icon">
                <i class="fa-solid ${iconClass}"></i>
            </div>
            <div class="file-name">${file.name}</div>
        `;
    } else {
        content = `
            <div class="file-icon">
                <i class="fa-solid fa-file text-secondary"></i>
            </div>
            <div class="file-name">${file.name}</div>
        `;
    }

    // Add file actions with proper download functionality
    const actions = `
        <div class="file-actions">
            <button type="button" class="download-btn2 btn-light" onclick="downloadFile('${fileUrl}', '${file.name}')">
                <i class="fa-solid fa-download download-icon2"></i>
            </button>
            @if($multiple)
            <button type="button" class="delete-btn2 btn-danger" onclick="removeUploadedFile('${componentId}', ${index})">
                <i class="fa-regular fa-trash-can"></i>
            </button>
            @endif
        </div>
    `;

    container.innerHTML = content + actions;

    // Add click event for lightbox
    container.addEventListener('click', function(e) {
        if (e.target.closest('.file-actions')) {
            return;
        }

        const component = window.fileComponents[componentId];
        const currentIndex = component.mediaItems.findIndex(item => item === container);
        if (currentIndex !== -1) {
            component.currentMediaIndex = currentIndex;
            openLightbox(componentId);
        }
    });

    return container;
}

function downloadFile(url, filename) {
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function removeUploadedFile(componentId, index) {
    const component = window.fileComponents[componentId];
    const gallery = document.getElementById(`${componentId}-gallery`);
    const fileInput = document.getElementById(`${componentId}-input`);

    // Remove the file container
    const containers = gallery.querySelectorAll('.file-container[data-existing="false"]');
    if (containers[index]) {
        containers[index].remove();
    }

    // Update file input (remove the file from input)
    if (fileInput && fileInput.files) {
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    // Update component media items
    updateComponentMediaItems(componentId);

    // Update upload box state
    updateUploadBoxState(componentId);
}

function updateComponentMediaItems(componentId) {
    const gallery = document.getElementById(`${componentId}-gallery`);
    const containers = gallery.querySelectorAll('.file-container');
    const component = window.fileComponents[componentId];

    component.mediaItems = Array.from(containers);

    // Update indices
    containers.forEach((container, index) => {
        container.setAttribute('data-index', index);
    });
}

function getFileExtension(filename) {
    return filename.split('.').pop().toLowerCase();
}

function openLightbox(componentId) {
    const component = window.fileComponents[componentId];
    const lightbox = document.getElementById(`${componentId}-lightbox`);
    const mediaContainer = document.getElementById(`${componentId}-lightbox-media`);
    const filenameContainer = document.getElementById(`${componentId}-lightbox-filename`);
    const counterContainer = document.getElementById(`${componentId}-lightbox-counter`);

    const currentItem = component.mediaItems[component.currentMediaIndex];
    const mediaUrl = currentItem.dataset.url || currentItem.querySelector('img, video')?.src;
    const mediaName = currentItem.dataset.name;
    const mediaType = currentItem.dataset.type;

    // Clear previous content
    mediaContainer.innerHTML = '';

    // Add media content
    if (['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'].includes(mediaType)) {
        const img = document.createElement('img');
        img.src = mediaUrl;
        img.alt = mediaName;
        mediaContainer.appendChild(img);
    } else if (['mp4', 'mov', 'avi', 'wmv', 'mkv'].includes(mediaType)) {
        const video = document.createElement('video');
        video.src = mediaUrl;
        video.controls = true;
        video.autoplay = true;
        mediaContainer.appendChild(video);
    } else {
        const icon = document.createElement('div');
        icon.className = 'file-icon';
        if (mediaType === 'pdf') {
            icon.innerHTML = '<i class="fa-solid fa-file-pdf text-danger"></i>';
        } else if (['doc', 'docx'].includes(mediaType)) {
            icon.innerHTML = '<i class="fa-solid fa-file-word text-primary"></i>';
        } else {
            icon.innerHTML = '<i class="fa-solid fa-file text-secondary"></i>';
        }
        mediaContainer.appendChild(icon);
    }

    filenameContainer.textContent = mediaName;
    counterContainer.textContent = `${component.currentMediaIndex + 1} / ${component.mediaItems.length}`;

    // Show/hide navigation buttons
    const prevBtn = lightbox.querySelector('.lightbox-nav.prev');
    const nextBtn = lightbox.querySelector('.lightbox-nav.next');

    prevBtn.style.display = component.currentMediaIndex > 0 ? 'block' : 'none';
    nextBtn.style.display = component.currentMediaIndex < component.mediaItems.length - 1 ? 'block' : 'none';

    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox(componentId) {
    const lightbox = document.getElementById(`${componentId}-lightbox`);
    lightbox.classList.remove('active');
    document.body.style.overflow = 'auto';

    // Stop video if playing
    const video = lightbox.querySelector('video');
    if (video) {
        video.pause();
    }
}

function previousMedia(componentId) {
    const component = window.fileComponents[componentId];
    if (component.currentMediaIndex > 0) {
        component.currentMediaIndex--;
        openLightbox(componentId);
    }
}

function nextMedia(componentId) {
    const component = window.fileComponents[componentId];
    if (component.currentMediaIndex < component.mediaItems.length - 1) {
        component.currentMediaIndex++;
        openLightbox(componentId);
    }
}

// Global keyboard navigation (only for active lightbox)
document.addEventListener('keydown', function(e) {
    const activeLightbox = document.querySelector('.lightbox.active');
    if (!activeLightbox) return;

    const componentId = activeLightbox.id.replace('-lightbox', '');
    const component = window.fileComponents[componentId];
    if (!component) return;

    switch(e.key) {
        case 'Escape':
            closeLightbox(componentId);
            break;
        case 'ArrowLeft':
            previousMedia(componentId);
            break;
        case 'ArrowRight':
            nextMedia(componentId);
            break;
    }
});

// Close lightbox when clicking outside content
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('lightbox') && e.target.classList.contains('active')) {
        const componentId = e.target.id.replace('-lightbox', '');
        closeLightbox(componentId);
    }
});

// 3. Add delegated event handler for delete button clicks (SweetAlert + AJAX)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.delete-btn2');
    const container = btn ? btn.closest('.file-container[data-existing="true"]') : null;
    if (btn && container) {
        e.preventDefault();
        const fileId = btn.getAttribute('data-id');
        const deleteUrl = btn.getAttribute('data-url');
        if (!fileId || !deleteUrl) return;

        Swal.fire({
            title: 'Are you sure?',
            text: 'This file will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX DELETE request
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        // Remove the file container from the DOM
                        container.remove();
                        // Optionally show success
                        Swal.fire('Deleted!', 'The file has been deleted.', 'success');
                        // Update component media items and upload box state
                        const componentId = container.getAttribute('data-component');
                        if (componentId) {
                            updateComponentMediaItems(componentId);
                            updateUploadBoxState(componentId);
                        }
                    } else {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Delete failed');
                        });
                    }
                })
                .catch(error => {
                    Swal.fire('Error', error.message, 'error');
                });
            }
        });
    }
});

function updateUploadBoxState(componentId) {
    const uploadBox = document.querySelector(`#${componentId} .upload-box`);
    const gallery = document.getElementById(`${componentId}-gallery`);
    const hasFiles = gallery && gallery.children.length > 0;

    if (hasFiles) {
        uploadBox.classList.add('has-files');
    } else {
        uploadBox.classList.remove('has-files');
    }
}
</script>
