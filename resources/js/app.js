import './bootstrap';
import { createApp } from 'vue';
import NotificationComponent from './components/NotificationComponent.vue';

const Notifiapp = createApp({});
Notifiapp.component('notification-component', NotificationComponent);
Notifiapp.mount('#vue-notifications');
