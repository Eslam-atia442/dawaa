<!-- FCM Notification Modal -->
<div class="modal fade" id="fcmNotificationModal" tabindex="-1" role="dialog" aria-labelledby="fcmNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fcmNotificationModalLabel">
                    <i class="fas fa-bell"></i> إرسال إشعار FCM
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="fcmNotificationForm">
                <div class="modal-body">
                    <!-- Notification Type Selection -->
                    <div class="form-group">
                        <label for="notificationType">نوع الإشعار</label>
                        <select class="form-control" id="notificationType" name="notification_type" required>
                            <option value="selected_users">المستخدمون المحددون</option>
                            <option value="all_users">جميع المستخدمين</option>
                            <option value="device_type">حسب نوع الجهاز</option>
                        </select>
                    </div>

                    <!-- Device Type Selection (shown when device_type is selected) -->
                    <div class="form-group" id="deviceTypeGroup" style="display: none;">
                        <label for="deviceType">نوع الجهاز</label>
                        <select class="form-control" id="deviceType" name="device_type">
                            <option value="android">Android</option>
                            <option value="ios">iOS</option>
                            <option value="web">Web</option>
                        </select>
                    </div>

                    <!-- Selected Users Info -->
                    <div class="form-group" id="selectedUsersInfo" style="display: none;">
                        <label>المستخدمون المحددون</label>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            سيتم إرسال الإشعار إلى <span id="selectedUsersCount">0</span> مستخدم
                        </div>
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
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">العربية</h6>
                                        <div class="notification-preview-ar">
                                            <strong id="previewTitleAr">العنوان بالعربية</strong>
                                            <p id="previewBodyAr" class="mb-0">محتوى الإشعار بالعربية</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-primary">English</h6>
                                        <div class="notification-preview-en">
                                            <strong id="previewTitleEn">Title in English</strong>
                                            <p id="previewBodyEn" class="mb-0">Notification content in English</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary" id="sendNotificationBtn">
                        <i class="fas fa-paper-plane"></i> إرسال الإشعار
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="notificationLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; text-align: center;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">جاري الإرسال...</span>
        </div>
        <p class="mt-2">جاري إرسال الإشعارات...</p>
    </div>
</div>

<style>
.notification-preview-ar {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
    border-right: 3px solid #007bff;
}

.notification-preview-en {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

#fcmNotificationModal .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

#fcmNotificationModal .modal-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

#fcmNotificationModal .modal-header .close {
    color: white;
    opacity: 0.8;
}

#fcmNotificationModal .modal-header .close:hover {
    opacity: 1;
}
</style>
