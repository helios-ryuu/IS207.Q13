import { defineStore } from 'pinia';
import api from '../utils/api';

export const useNotificationStore = defineStore('notification', {
    state: () => ({
        notifications: [],
        unreadCount: 0,
        timer: null,
    }),
    actions: {
        // Poll unread count
        startPolling() {
            // Gọi ngay lập tức 1 lần
            this.fetchUnreadCount();

            if (this.timer) clearInterval(this.timer);

            // Poll mỗi 5s
            this.timer = setInterval(() => {
                this.fetchUnreadCount();
            }, 5000);
        },

        stopPolling() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },

        async fetchUnreadCount() {
            try {
                const response = await api.get('/notifications/unread-count');
                if (response.data?.success) {
                    const newCount = response.data.data.unread_count;
                    // Nếu có thông báo mới (count tăng) và đang mở dropdown (logic này xử lý ở component), 
                    // có thể trigger fetch list.
                    // Ở đây chỉ cập nhật count.
                    this.unreadCount = newCount;
                }
            } catch (error) {
                // Silent error (polling shouldn't spam console logs on auth fail etc)
            }
        },

        async fetchNotifications() {
            try {
                const response = await api.get('/notifications');
                if (response.data?.success) {
                    this.notifications = response.data.data.data; // Paginated data
                }
            } catch (error) {
                console.error('Lỗi tải thông báo:', error);
            }
        },

        async markAsRead(id) {
            try {
                await api.put(`/notifications/${id}/read`);
                // Update local state optimistic
                const notif = this.notifications.find(n => n.id === id);
                if (notif && !notif.is_read) {
                    notif.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Lỗi đánh dấu đã đọc:', error);
            }
        },

        async markAllRead() {
            try {
                await api.put('/notifications/read-all');
                this.notifications.forEach(n => n.is_read = true);
                this.unreadCount = 0;
            } catch (error) {
                console.error('Lỗi đánh dấu tất cả đã đọc:', error);
            }
        }
    }
});
