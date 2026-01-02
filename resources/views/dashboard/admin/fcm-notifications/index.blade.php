@extends('dashboard.admin.layout.main')

@section('title')
    إشعارات FCM
@endsection

@push('css_files')
<style>
.notification-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.notification-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
    transform: translateY(-2px);
}

.notification-card.selected {
    border-color: #28a745;
    background-color: #f8fff9;
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-card .card-body {
    padding: 1.5rem;
}

.stats-number {
    font-size: 2rem;
    font-weight: bold;
}

.stats-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.btn-notification {
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 20px;
}

.btn-notification i {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.btn-notification .btn-title {
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.btn-notification .btn-description {
    font-size: 0.9rem;
    opacity: 0.8;
}

#notificationModal .modal-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

#notificationModal .modal-header .close {
    color: white;
    opacity: 0.8;
}

#notificationModal .modal-header .close:hover {
    opacity: 1;
}

.preview-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
}

.preview-ar {
    border-right: 3px solid #007bff;
    padding-right: 15px;
}

.preview-en {
    border-left: 3px solid #007bff;
    padding-left: 15px;
}
</style>
@endpush

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="ti ti-home-bolt me-2"></i>
                <a href="{{route('admin.home')}}">@lang('trans.home')</a>
            </li>
            <li class="breadcrumb-item active">
                <i class="ti ti-bell me-2"></i> إشعارات FCM
            </li>
        </ol>
    </nav>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="stats-number">{{ $stats['total_users'] ?? 0 }}</div>
                    <div class="stats-label">إجمالي المستخدمين</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="stats-number">{{ $stats['users_with_devices'] ?? 0 }}</div>
                    <div class="stats-label">مستخدمون لديهم أجهزة</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="stats-number">{{ $stats['total_devices'] ?? 0 }}</div>
                    <div class="stats-label">إجمالي الأجهزة</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="stats-number">{{ $stats['active_devices'] ?? 0 }}</div>
                    <div class="stats-label">أجهزة نشطة</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Options -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="ti ti-bell me-2"></i>
                إرسال إشعارات FCM
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Authenticated Users -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card notification-card h-100" onclick="selectNotificationType('authenticated')">
                        <div class="card-body btn-notification">
                            <i class="ti ti-user text-primary"></i>
                            <div class="btn-title">المستخدمون المسجلون</div>
                            <div class="btn-description">إرسال إشعار للمستخدمين المسجلين فقط</div>
                            <div class="mt-2">
                                <small class="text-muted">{{ $stats['users_with_devices'] ?? 0 }} مستخدم</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guest Users -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card notification-card h-100" onclick="selectNotificationType('guests')">
                        <div class="card-body btn-notification">
                            <i class="ti ti-user-off text-warning"></i>
                            <div class="btn-title">الضيوف</div>
                            <div class="btn-description">إرسال إشعار للمستخدمين غير المسجلين</div>
                            <div class="mt-2">
                                <small class="text-muted">{{ $stats['guest_devices'] ?? 0 }} جهاز</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- All Devices -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card notification-card h-100" onclick="selectNotificationType('all')">
                        <div class="card-body btn-notification">
                            <i class="ti ti-devices text-success"></i>
                            <div class="btn-title">جميع الأجهزة</div>
                            <div class="btn-description">إرسال إشعار لجميع الأجهزة</div>
                            <div class="mt-2">
                                <small class="text-muted">{{ $stats['active_devices'] ?? 0 }} جهاز</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Android Users -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card notification-card h-100" onclick="selectNotificationType('android')">
                        <div class="card-body btn-notification">
                            <i class="ti ti-brand-android text-success"></i>
                            <div class="btn-title">مستخدمو Android</div>
                            <div class="btn-description">إرسال إشعار لمستخدمي Android فقط</div>
                            <div class="mt-2">
                                <small class="text-muted">{{ $stats['android_devices'] ?? 0 }} جهاز</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- iOS Users -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card notification-card h-100" onclick="selectNotificationType('ios')">
                        <div class="card-body btn-notification">
                            <i class="ti ti-brand-apple text-dark"></i>
                            <div class="btn-title">مستخدمو iOS</div>
                            <div class="btn-description">إرسال إشعار لمستخدمي iOS فقط</div>
                            <div class="mt-2">
                                <small class="text-muted">{{ $stats['ios_devices'] ?? 0 }} جهاز</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Web Users -->
{{--                <div class="col-md-6 col-lg-4 mb-4">--}}
{{--                    <div class="card notification-card h-100" onclick="selectNotificationType('web')">--}}
{{--                        <div class="card-body btn-notification">--}}
{{--                            <i class="ti ti-world text-info"></i>--}}
{{--                            <div class="btn-title">مستخدمو الويب</div>--}}
{{--                            <div class="btn-description">إرسال إشعار لمستخدمي الويب فقط</div>--}}
{{--                            <div class="mt-2">--}}
{{--                                <small class="text-muted">{{ $stats['web_devices'] ?? 0 }} جهاز</small>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
    </div>

    <!-- FCM Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">
                        <i class="ti ti-bell"></i> إرسال إشعار FCM
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="notificationForm">
                    <div class="modal-body">
                        <!-- Selected Type Info -->
                        <div class="alert alert-info" id="selectedTypeInfo">
                            <i class="ti ti-info-circle me-2"></i>
                            <span id="selectedTypeText">يرجى اختيار نوع الإشعار</span>
                        </div>

                        <!-- Title Arabic -->
                        <div class="form-group">
                            <label for="titleAr">العنوان (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="titleAr" name="title_ar"
                                   placeholder="أدخل العنوان باللغة العربية" maxlength="255" required>
                            <small class="form-text text-muted">الحد الأقصى 255 حرف</small>
                        </div>

                        <!-- Title English -->
                        <div class="form-group">
                            <label for="titleEn">العنوان (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="titleEn" name="title_en"
                                   placeholder="Enter title in English" maxlength="255" required>
                            <small class="form-text text-muted">Maximum 255 characters</small>
                        </div>

                        <!-- Body Arabic -->
                        <div class="form-group">
                            <label for="bodyAr">المحتوى (عربي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="bodyAr" name="body_ar" rows="3"
                                      placeholder="أدخل محتوى الإشعار باللغة العربية" maxlength="1000" required></textarea>
                            <small class="form-text text-muted">الحد الأقصى 1000 حرف</small>
                        </div>

                        <!-- Body English -->
                        <div class="form-group">
                            <label for="bodyEn">المحتوى (إنجليزي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="bodyEn" name="body_en" rows="3"
                                      placeholder="Enter notification content in English" maxlength="1000" required></textarea>
                            <small class="form-text text-muted">Maximum 1000 characters</small>
                        </div>

                        <!-- Preview Section -->
                        <div class="form-group">
                            <label>معاينة الإشعار</label>
                            <div class="preview-card">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">العربية</h6>
                                        <div class="preview-ar">
                                            <strong id="previewTitleAr">العنوان بالعربية</strong>
                                            <p id="previewBodyAr" class="mb-0">محتوى الإشعار بالعربية</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-primary">English</h6>
                                        <div class="preview-en">
                                            <strong id="previewTitleEn">Title in English</strong>
                                            <p id="previewBodyEn" class="mb-0">Notification content in English</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="ti ti-x"></i> إلغاء
                        </button>
                        <button type="submit" class="btn btn-primary" id="sendNotificationBtn">
                            <i class="ti ti-paper-plane"></i> إرسال الإشعار
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; text-align: center;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">جاري الإرسال...</span>
            </div>
            <p class="mt-2">جاري إرسال الإشعارات...</p>
        </div>
    </div>

@endsection

@push('js_files')
<script>
let selectedNotificationType = null;

function selectNotificationType(type) {
    selectedNotificationType = type;

    // Remove selected class from all cards
    $('.notification-card').removeClass('selected');

    // Add selected class to clicked card
    event.currentTarget.classList.add('selected');

    // Update modal info
    const typeTexts = {
        'authenticated': 'المستخدمون المسجلون',
        'guests': 'الضيوف',
        'all': 'جميع الأجهزة',
        'android': 'مستخدمو Android',
        'ios': 'مستخدمو iOS',
        'web': 'مستخدمو الويب'
    };

    $('#selectedTypeText').text(`تم اختيار: ${typeTexts[type]}`);

    // Show modal
    $('#notificationModal').modal('show');
}

// Update preview on input change
$('#titleAr, #titleEn, #bodyAr, #bodyEn').on('input', function() {
    updatePreview();
});

function updatePreview() {
    const titleAr = $('#titleAr').val() || 'العنوان بالعربية';
    const titleEn = $('#titleEn').val() || 'Title in English';
    const bodyAr = $('#bodyAr').val() || 'محتوى الإشعار بالعربية';
    const bodyEn = $('#bodyEn').val() || 'Notification content in English';

    $('#previewTitleAr').text(titleAr);
    $('#previewTitleEn').text(titleEn);
    $('#previewBodyAr').text(bodyAr);
    $('#previewBodyEn').text(bodyEn);
}

// Form submission
$('#notificationForm').on('submit', function(e) {
    e.preventDefault();

    if (!selectedNotificationType) {
        alert('يرجى اختيار نوع الإشعار');
        return;
    }

    if (!validateForm()) {
        return;
    }

    sendNotification();
});

function validateForm() {
    const titleAr = $('#titleAr').val().trim();
    const titleEn = $('#titleEn').val().trim();
    const bodyAr = $('#bodyAr').val().trim();
    const bodyEn = $('#bodyEn').val().trim();

    if (!titleAr || !titleEn || !bodyAr || !bodyEn) {
        alert('يرجى ملء جميع الحقول المطلوبة');
        return false;
    }

    if (titleAr.length > 255 || titleEn.length > 255) {
        alert('العنوان يجب أن يكون أقل من 255 حرف');
        return false;
    }

    if (bodyAr.length > 1000 || bodyEn.length > 1000) {
        alert('المحتوى يجب أن يكون أقل من 1000 حرف');
        return false;
    }

    return true;
}

async function sendNotification() {
    const formData = {
        title_ar: $('#titleAr').val().trim(),
        title_en: $('#titleEn').val().trim(),
        body_ar: $('#bodyAr').val().trim(),
        body_en: $('#bodyEn').val().trim(),
    };

    let endpoint = '';
    switch (selectedNotificationType) {
        case 'authenticated':
            endpoint = '{{ route("admin.fcm-notifications.send-to-authenticated") }}';
            break;
        case 'guests':
            endpoint = '{{ route("admin.fcm-notifications.send-to-guests") }}';
            break;
        case 'all':
            endpoint = '{{ route("admin.fcm-notifications.send-to-all-devices") }}';
            break;
        case 'android':
        case 'ios':
        case 'web':
            endpoint = '{{ route("admin.fcm-notifications.send-by-device-type") }}';
            formData.device_type = selectedNotificationType;
            break;
    }

    showLoading(true);

    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();
        handleResponse(result);

    } catch (error) {
        console.error('Error sending notification:', error);
        showError('حدث خطأ أثناء إرسال الإشعار');
    } finally {
        showLoading(false);
    }
}

function handleResponse(result) {
    if (result.success) {
        showSuccess(result.message, result.data);
        $('#notificationModal').modal('hide');
        resetForm();
    } else {
        showError(result.message);
        if (result.errors) {
            showValidationErrors(result.errors);
        }
    }
}

function showSuccess(message, data = null) {
    let successMessage = message;

    if (data) {
        successMessage += '\n\n';
        if (data.total_users_selected) {
            successMessage += `المستخدمون المحددون: ${data.total_users_selected}\n`;
        }
        if (data.users_with_devices) {
            successMessage += `المستخدمون الذين لديهم أجهزة: ${data.users_with_devices}\n`;
        }
        if (data.notifications_sent) {
            successMessage += `الإشعارات المرسلة: ${data.notifications_sent}\n`;
        }
        if (data.notifications_failed) {
            successMessage += `الإشعارات الفاشلة: ${data.notifications_failed}\n`;
        }
        if (data.guest_devices_count) {
            successMessage += `أجهزة الضيوف: ${data.guest_devices_count}\n`;
        }
    }

    Swal.fire({
        icon: 'success',
        title: 'تم بنجاح!',
        text: successMessage,
        confirmButtonText: 'موافق',
        confirmButtonColor: '#28a745',
        timer: 5000,
        timerProgressBar: true
    });
}

function formatSuccessData(data) {
    let html = '<div class="mt-2"><small>';

    if (data.total_users_selected) {
        html += `<strong>المستخدمون المحددون:</strong> ${data.total_users_selected}<br>`;
    }
    if (data.users_with_devices) {
        html += `<strong>المستخدمون الذين لديهم أجهزة:</strong> ${data.users_with_devices}<br>`;
    }
    if (data.notifications_sent) {
        html += `<strong>الإشعارات المرسلة:</strong> ${data.notifications_sent}<br>`;
    }
    if (data.notifications_failed) {
        html += `<strong>الإشعارات الفاشلة:</strong> ${data.notifications_failed}<br>`;
    }
    if (data.guest_devices_count) {
        html += `<strong>أجهزة الضيوف:</strong> ${data.guest_devices_count}<br>`;
    }

    html += '</small></div>';
    return html;
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'خطأ!',
        text: message,
        confirmButtonText: 'موافق',
        confirmButtonColor: '#dc3545'
    });
}

function showValidationErrors(errors) {
    let errorMessage = 'أخطاء في التحقق:\n\n';

    Object.keys(errors).forEach(field => {
        errors[field].forEach(error => {
            errorMessage += `• ${error}\n`;
        });
    });

    Swal.fire({
        icon: 'error',
        title: 'خطأ في التحقق!',
        text: errorMessage,
        confirmButtonText: 'موافق',
        confirmButtonColor: '#dc3545'
    });
}

function showAlert(html) {
    // Remove existing alerts
    $('.alert').remove();

    // Add new alert at top of page
    $('body').prepend(html);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

function showLoading(show) {
    const overlay = $('#loadingOverlay');
    if (show) {
        overlay.show();
    } else {
        overlay.hide();
    }
}

function resetForm() {
    $('#notificationForm')[0].reset();
    selectedNotificationType = null;
    $('.notification-card').removeClass('selected');
    updatePreview();
}

// Initialize preview
updatePreview();
</script>
@endpush
