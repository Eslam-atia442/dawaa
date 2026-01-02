/**
 * FCM Notification Modal JavaScript
 * Handles sending push notifications from admin dashboard
 */

class FCMNotificationModal {
    constructor() {
        this.selectedUsers = [];
        this.modal = null;
        this.form = null;
        this.init();
    }

    init() {
        this.modal = $('#fcmNotificationModal');
        this.form = $('#fcmNotificationForm');
        this.bindEvents();
        this.setupFormValidation();
    }

    bindEvents() {
        // Notification type change
        $('#notificationType').on('change', (e) => {
            this.handleNotificationTypeChange(e.target.value);
        });

        // Form input changes for preview
        $('#titleAr, #titleEn, #bodyAr, #bodyEn').on('input', () => {
            this.updatePreview();
        });

        // Form submission
        this.form.on('submit', (e) => {
            e.preventDefault();
            this.sendNotification();
        });

        // Modal events
        this.modal.on('hidden.bs.modal', () => {
            this.resetForm();
        });
    }

    setupFormValidation() {
        // Real-time validation
        $('#titleAr, #titleEn').on('input', function() {
            const maxLength = 255;
            const currentLength = $(this).val().length;
            const remaining = maxLength - currentLength;
            
            if (remaining < 50) {
                $(this).addClass('is-warning');
            } else {
                $(this).removeClass('is-warning');
            }
        });

        $('#bodyAr, #bodyEn').on('input', function() {
            const maxLength = 1000;
            const currentLength = $(this).val().length;
            const remaining = maxLength - currentLength;
            
            if (remaining < 100) {
                $(this).addClass('is-warning');
            } else {
                $(this).removeClass('is-warning');
            }
        });
    }

    handleNotificationTypeChange(type) {
        const deviceTypeGroup = $('#deviceTypeGroup');
        const selectedUsersInfo = $('#selectedUsersInfo');

        // Hide all conditional groups
        deviceTypeGroup.hide();
        selectedUsersInfo.hide();

        // Show relevant group based on selection
        switch (type) {
            case 'device_type':
                deviceTypeGroup.show();
                break;
            case 'selected_users':
                if (this.selectedUsers.length > 0) {
                    selectedUsersInfo.show();
                    $('#selectedUsersCount').text(this.selectedUsers.length);
                }
                break;
        }
    }

    updatePreview() {
        const titleAr = $('#titleAr').val() || 'العنوان بالعربية';
        const titleEn = $('#titleEn').val() || 'Title in English';
        const bodyAr = $('#bodyAr').val() || 'محتوى الإشعار بالعربية';
        const bodyEn = $('#bodyEn').val() || 'Notification content in English';

        $('#previewTitleAr').text(titleAr);
        $('#previewTitleEn').text(titleEn);
        $('#previewBodyAr').text(bodyAr);
        $('#previewBodyEn').text(bodyEn);
    }

    setSelectedUsers(users) {
        this.selectedUsers = users;
        if (users.length > 0) {
            $('#selectedUsersCount').text(users.length);
            $('#selectedUsersInfo').show();
        }
    }

    async sendNotification() {
        if (!this.validateForm()) {
            return;
        }

        const formData = this.getFormData();
        const endpoint = this.getEndpoint();

        this.showLoading(true);

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
            this.handleResponse(result);

        } catch (error) {
            console.error('Error sending notification:', error);
            this.showError('حدث خطأ أثناء إرسال الإشعار');
        } finally {
            this.showLoading(false);
        }
    }

    validateForm() {
        const titleAr = $('#titleAr').val().trim();
        const titleEn = $('#titleEn').val().trim();
        const bodyAr = $('#bodyAr').val().trim();
        const bodyEn = $('#bodyEn').val().trim();

        if (!titleAr || !titleEn || !bodyAr || !bodyEn) {
            this.showError('يرجى ملء جميع الحقول المطلوبة');
            return false;
        }

        if (titleAr.length > 255 || titleEn.length > 255) {
            this.showError('العنوان يجب أن يكون أقل من 255 حرف');
            return false;
        }

        if (bodyAr.length > 1000 || bodyEn.length > 1000) {
            this.showError('المحتوى يجب أن يكون أقل من 1000 حرف');
            return false;
        }

        return true;
    }

    getFormData() {
        const formData = {
            title_ar: $('#titleAr').val().trim(),
            title_en: $('#titleEn').val().trim(),
            body_ar: $('#bodyAr').val().trim(),
            body_en: $('#bodyEn').val().trim(),
        };

        // Add user_ids for selected users
        if (this.selectedUsers.length > 0) {
            formData.user_ids = this.selectedUsers.map(user => parseInt(user.id));
        }

        // Add device_type for device type notifications
        const notificationType = $('#notificationType').val();
        if (notificationType === 'device_type') {
            formData.device_type = $('#deviceType').val();
        }

        return formData;
    }

    getEndpoint() {
        const notificationType = $('#notificationType').val();
        const baseUrl = window.location.origin + '/admin/fcm-notifications';

        switch (notificationType) {
            case 'selected_users':
                return baseUrl + '/send-to-users';
            case 'all_users':
                return baseUrl + '/send-to-all-users';
            case 'device_type':
                return baseUrl + '/send-by-device-type';
            default:
                return baseUrl + '/send-to-users';
        }
    }

    handleResponse(result) {
        if (result.success) {
            this.showSuccess(result.message, result.data);
            this.modal.modal('hide');
        } else {
            this.showError(result.message);
            if (result.errors) {
                this.showValidationErrors(result.errors);
            }
        }
    }

    showSuccess(message, data = null) {
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

    formatSuccessData(data) {
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
        
        html += '</small></div>';
        return html;
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: message,
            confirmButtonText: 'موافق',
            confirmButtonColor: '#dc3545'
        });
    }

    showValidationErrors(errors) {
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

    showAlert(html) {
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at top of page
        $('body').prepend(html);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }

    showLoading(show) {
        const overlay = $('#notificationLoadingOverlay');
        if (show) {
            overlay.show();
        } else {
            overlay.hide();
        }
    }

    resetForm() {
        this.form[0].reset();
        this.selectedUsers = [];
        $('#deviceTypeGroup').hide();
        $('#selectedUsersInfo').hide();
        this.updatePreview();
        $('.form-control').removeClass('is-warning is-invalid');
    }

    // Static method to open modal with selected users
    static openWithSelectedUsers(users) {
        const modal = new FCMNotificationModal();
        modal.setSelectedUsers(users);
        modal.modal.modal('show');
        return modal;
    }
}

// Global function to open notification modal
function openFCMNotificationModal(selectedUsers = []) {
    const modal = new FCMNotificationModal();
    if (selectedUsers.length > 0) {
        modal.setSelectedUsers(selectedUsers);
    }
    modal.modal.modal('show');
    return modal;
}

// Initialize when document is ready
$(document).ready(function() {
    // Add notification button to users table if it exists
    if ($('#usersTable').length) {
        addNotificationButtonToUsersTable();
    }
});

function addNotificationButtonToUsersTable() {
    // Add notification button to users table header
    const tableHeader = $('#usersTable thead tr');
    if (tableHeader.length) {
        const notificationButton = `
            <th>
                <button type="button" class="btn btn-primary btn-sm" onclick="openFCMNotificationModal()">
                    <i class="fas fa-bell"></i> إرسال إشعار
                </button>
            </th>
        `;
        tableHeader.append(notificationButton);
    }

    // Add checkboxes to each user row
    $('#usersTable tbody tr').each(function() {
        const userId = $(this).find('td:first').text();
        const userName = $(this).find('td:nth-child(2)').text();
        
        const checkbox = `
            <td>
                <div class="form-check">
                    <input class="form-check-input user-checkbox" type="checkbox" 
                           value="${userId}" data-user-name="${userName}">
                </div>
            </td>
        `;
        $(this).append(checkbox);
    });

    // Add "Send to Selected" button
    const tableContainer = $('#usersTable').closest('.card-body');
    if (tableContainer.length) {
        const selectedButton = `
            <div class="mt-3">
                <button type="button" class="btn btn-success" id="sendToSelectedBtn" onclick="sendToSelectedUsers()">
                    <i class="fas fa-paper-plane"></i> إرسال للمحددين
                </button>
                <span id="selectedCount" class="badge badge-info ml-2">0 محدد</span>
            </div>
        `;
        tableContainer.append(selectedButton);
    }

    // Handle checkbox changes
    $(document).on('change', '.user-checkbox', function() {
        updateSelectedCount();
    });
}

function sendToSelectedUsers() {
    const selectedUsers = [];
    $('.user-checkbox:checked').each(function() {
        selectedUsers.push({
            id: $(this).val(),
            name: $(this).data('user-name')
        });
    });

    if (selectedUsers.length === 0) {
        alert('يرجى تحديد مستخدم واحد على الأقل');
        return;
    }

    openFCMNotificationModal(selectedUsers);
}

function updateSelectedCount() {
    const count = $('.user-checkbox:checked').length;
    $('#selectedCount').text(`${count} محدد`);
}
