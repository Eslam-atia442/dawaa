<template>
    <!-- Notification -->
    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1 ">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" @click.prevent="getNotifications" data-bs-toggle="dropdown"
           data-bs-auto-close="outside" aria-expanded="false">
            <i class="ti ti-bell ti-md"></i>
            <span class="badge bg-danger rounded-pill badge-notifications" > {{ unReaded }} </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
            <li class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                    <h5 class="text-body mb-0 me-auto">Notification</h5>
                    <button @click.prevent="markAllAsRead" class=" btn dropdown-notifications-all text-body"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top" aria-label="Mark all as read"
                            data-bs-original-title="Mark all as read"><i class="ti ti-mail-opened fs-4"></i></button>
                </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container" style="max-height: 300px; overflow-y: auto;">
                <ul class="list-group list-group-flush">
                    <li v-for="notification in notifications " :key="notification.id"
                        @click.prevent="goToNotification(notification)"
                        class="list-group-item list-group-item-action dropdown-notifications-item "
                        :class="{'marked-as-read bg-light': !notification.is_read }">
                        <div class="d-flex">

                            <div class="flex-shrink-0 me-3">
                                <div class="avatar">
                                    <img src="" alt="" class="h-auto rounded-circle">
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ notification.message }}</h6>

                                <p class="mb-0">{{ notification.message }}</p>

                                <small class="text-muted">{{ notification.created_at }}</small>
                            </div>
                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                <a v-if="!notification.is_read" href="javascript:void(0)"
                                   class="dropdown-notifications-read"><span
                                    class="badge badge-dot"></span></a>

                            </div>
                        </div>
                    </li>

                </ul>
                <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                    <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                </div>
                <div class="ps__rail-y" style="top: 0px; right: 0px;">
                    <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                </div>
            </li>
            <li class="dropdown-menu-footer border-top">
                <a href="javascript:void(0);"
                   class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                    View all notifications
                </a>
            </li>
        </ul>
    </li>
    <!--/ Notification -->
</template>
<script>
import Pusher from 'pusher-js';
import Swal from 'sweetalert2';


export default {

    name: "NotificationComponent",
    data() {
        return {
            unReaded: 0,
            notifications: []
        }
    },
    props: {
        admin_id: {
            type: String,
            default: 1
        }
    },

    async created() {
        this.pusherHandle();
        this.getNotifications();
    },
    methods: {
        pusherHandle() {
            const pusher = new Pusher('79402e7ec57b73114402', {
                cluster: 'mt1',
                encrypted: true,
                authEndpoint: '/admin/broadcasting/auth',
            });
            const channel = pusher.subscribe('private-App.Models.Admin.' + this.admin_id);
            channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', (data) => {

                Swal.fire({
                    title: 'Hello!',
                    text: data.message_ar,
                    icon: 'success',
                    confirmButtonText: 'Cool',
                    timer: 1500,
                    timerProgressBar: true
                });
                this.getNotifications()
            });
        },
        getNotifications() {
            axios.get('/admin/notifications')
                .then(response => {
                    this.notifications = response.data.data.notifications
                    this.unReaded = response.data.data.unReaded
                })
        },
        markAllAsRead() {
            axios.get('/admin/notifications/mark-all-as-read')
                .then(response => {
                    this.getNotifications()
                })
        },
        markAsRead(notification) {
            axios.get('/admin/notifications/mark-as-read/' + notification.id)
                .then(response => {
                    this.getNotifications()
                })
        },
        goToNotification(notification) {
            this.markAsRead(notification)
            if (notification.url) {
                window.open(notification.url, '_blank');
            }

        }
    }
}

</script>

<style scoped>

</style>
