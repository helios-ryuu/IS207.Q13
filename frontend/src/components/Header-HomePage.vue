<template>
  <header class="app-header" ref="headerRef">
    <div class="container">

      <div class="left-section">
        <button class="menu-btn" title="Danh mục" @click.stop="toggleCategoryMenu">
          <font-awesome-icon icon="bars" />
        </button>
        <router-link to="/" class="logo">
          <img src="/logo.jpg" alt="VietMarket Logo">
        </router-link>
        <div class="category-dropdown-menu" v-if="isCategoryMenuOpen">
          <ul>
            <li @click="selectCategory('Xe cộ')">Xe cộ</li>
            <li @click="selectCategory('Đồ điện tử')">Đồ điện tử</li>
            <li @click="selectCategory('Thú cưng')">Thú cưng</li>
            <li @click="selectCategory('Đồ ăn, Thực phẩm và các loại khác')">Đồ ăn, Thực phẩm và các loại khác</li>
            <li @click="selectCategory('Tủ lạnh, Máy lạnh, Máy giặt')">Tủ lạnh, Máy lạnh, Máy giặt</li>
            <li @click="selectCategory('Đồ gia dụng, Nội thất, Cây cảnh')">Đồ gia dụng, Nội thất, Cây cảnh</li>
            <li @click="selectCategory('Thời trang, Đồ dùng cá nhân')">Thời trang, Đồ dùng cá nhân</li>
            <li @click="selectCategory('Giải trí, Thể thao, Sở thích')">Giải trí, Thể thao, Sở thích</li>
            <li @click="selectCategory('Đồ dùng văn phòng, Công nông nghiệp')">Đồ dùng văn phòng, Công nông nghiệp</li>
          </ul>
        </div>
      </div>

      <div class="center-section">
        <button class="shop-now-btn" @click="goToProducts">
          <font-awesome-icon icon="shopping-bag" /> Mua sắm ngay
        </button>
        <button class="support-btn">
          <font-awesome-icon icon="headset" /> Liên hệ hỗ trợ
        </button>
      </div>
      
      <div class="right-section">
        <div class="action-icons">
          <button class="icon-btn" title="Yêu thích" @click="$router.push('/favorites')">
            <font-awesome-icon icon="heart" />
          </button>
          <button class="icon-btn" title="Trò chuyện" @click="handleChatClick">
            <font-awesome-icon icon="comment" />
          </button>
          <div class="notification-wrapper">
            <button class="icon-btn" title="Thông báo" @click.stop="toggleNotifications">
              <font-awesome-icon icon="bell" />
              <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
            </button>
            <div v-if="isNotificationOpen" class="notification-dropdown">
              <div class="notification-header">
                <h3>Thông báo</h3>
                <button v-if="notifications.length > 0" class="mark-all-read" @click="markAllAsRead">
                  Đánh dấu đã đọc
                </button>
              </div>
              <div class="notification-list">
                <div v-if="notifications.length === 0" class="empty-notifications">
                  <span class="empty-icon">🔔</span>
                  <p>Chưa có thông báo mới</p>
                </div>
                <div 
                  v-for="notif in notifications" 
                  :key="notif.id" 
                  class="notification-item"
                  :class="{ unread: !notif.read }"
                  @click="handleNotificationClick(notif)"
                >
                  <div class="notif-icon" :class="notif.type">
                    {{ getNotificationIcon(notif.type) }}
                  </div>
                  <div class="notif-content">
                    <p class="notif-title">{{ notif.title }}</p>
                    <p class="notif-message">{{ notif.message }}</p>
                    <span class="notif-time">{{ notif.time }}</span>
                  </div>
                  <div v-if="!notif.read" class="unread-dot"></div>
                </div>
              </div>
              <div v-if="notifications.length > 0" class="notification-footer">
                <button @click="viewAllNotifications">Xem tất cả thông báo</button>
              </div>
            </div>
          </div>
        </div>
        <button class="post-btn" @click="$router.push('/post')">
          Đăng tin
        </button>
        
        <div class="user-actions">
          <template v-if="!isLoggedIn">
            <router-link to="/login" class="auth-btn login-btn">Đăng nhập</router-link>
            <router-link to="/register" class="auth-btn register-btn">Đăng ký</router-link>
          </template>
          <template v-else>
            <router-link to="/manage-posts" class="manage-btn">Quản lý tin</router-link>
            <div class="avatar-wrapper" @click.stop="toggleUserMenu">
              <img src="/avatar.jpg" alt="Avatar" class="avatar">
              <font-awesome-icon icon="chevron-down" class="arrow-small" />
              <div v-if="isUserMenuOpen" class="user-dropdown">
                <router-link to="/profile/social">Trang cá nhân</router-link>
                <router-link to="/profile">Thông tin cá nhân</router-link>
                <router-link v-if="user && user.role === 'admin'" to="/admin" class="admin-link">Admin</router-link>
                <button @click="handleLogout">Đăng xuất</button>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </header>
  <AuthRedirectModal
    v-if="isAuthModalOpen"
    @close="isAuthModalOpen = false"
  />
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router'; 
import { useAuth } from '../utils/useAuth';
// import LoginModal from './LoginModal.vue'; // <-- ĐÃ XÓA (Không dùng modal)

const isCategoryMenuOpen = ref(false);
const headerRef = ref(null);
const router = useRouter();

// Lấy trạng thái từ useAuth
const { isLoggedIn, user, logout } = useAuth(); 
const isUserMenuOpen = ref(false);

// Notification state
const isNotificationOpen = ref(false);
const notifications = ref([
  {
    id: 1,
    type: 'order',
    title: 'Đơn hàng mới',
    message: 'Bạn có đơn hàng mới từ Nguyễn Văn A',
    time: '5 phút trước',
    read: false,
    link: '/orders'
  },
  {
    id: 2,
    type: 'message',
    title: 'Tin nhắn mới',
    message: 'Trần Thị B đã gửi tin nhắn cho bạn',
    time: '15 phút trước',
    read: false,
    link: '/chat'
  },
  {
    id: 3,
    type: 'like',
    title: 'Sản phẩm được yêu thích',
    message: 'Sản phẩm "iPhone 13 Pro Max" của bạn được 5 người yêu thích',
    time: '1 giờ trước',
    read: false,
    link: '/manage-posts'
  },
  {
    id: 4,
    type: 'system',
    title: 'Cập nhật hệ thống',
    message: 'Chúng tôi đã cập nhật tính năng mới cho ứng dụng',
    time: '2 giờ trước',
    read: true,
    link: null
  }
]);

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

// const isLoginModalOpen = ref(false); // <-- ĐÃ XÓA
// const handleLogin = () => { ... }; // <-- ĐÃ XÓA (Vì <router-link> sẽ xử lý)
// const onLoginSuccess = () => { ... }; // <-- ĐÃ XÓA

// SỬA ĐỔI: Hàm xử lý khi bấm nút Chat
const handleChatClick = () => {
  if (isLoggedIn.value) {
    router.push('/chat'); // Nếu đã đăng nhập, chuyển trang
  } else {
    router.push('/login'); // Nếu chưa, chuyển đến trang /login
  }
};

// (Các hàm logic cũ giữ nguyên)
const handleLogout = () => { 
  logout(); // Gọi hàm logout của useAuth
  isUserMenuOpen.value = false; 
};

const toggleUserMenu = () => { isUserMenuOpen.value = !isUserMenuOpen.value; };
const toggleCategoryMenu = () => { isCategoryMenuOpen.value = !isCategoryMenuOpen.value; };
const toggleNotifications = () => { isNotificationOpen.value = !isNotificationOpen.value; };

const markAllAsRead = () => {
  notifications.value.forEach(n => n.read = true);
};

const handleNotificationClick = (notif) => {
  notif.read = true;
  if (notif.link) {
    router.push(notif.link);
  }
  isNotificationOpen.value = false;
};

const viewAllNotifications = () => {
  console.log('View all notifications');
  isNotificationOpen.value = false;
};

const getNotificationIcon = (type) => {
  const icons = {
    order: '📦',
    message: '💬',
    like: '❤️',
    system: '🔔'
  };
  return icons[type] || '🔔';
};

const selectCategory = (categoryName) => {
  router.push({ 
    path: '/products', 
    query: { category: categoryName } 
  });
  isCategoryMenuOpen.value = false;
};

// Hàm cho nút "Mua sắm ngay"
const goToProducts = () => {
  router.push('/products');
};

const handleClickOutside = (event) => {
  if (isCategoryMenuOpen.value && headerRef.value && !headerRef.value.contains(event.target)) {
    isCategoryMenuOpen.value = false;
  }
  if (isUserMenuOpen.value && headerRef.value && !headerRef.value.contains(event.target)) {
     isUserMenuOpen.value = false;
  }
  if (isNotificationOpen.value && headerRef.value && !headerRef.value.contains(event.target)) {
    isNotificationOpen.value = false;
  }
};
onMounted(() => { document.addEventListener('click', handleClickOutside); });
onBeforeUnmount(() => { document.removeEventListener('click', handleClickOutside); });
</script>

<style scoped>
/* (Toàn bộ CSS của Header-HomePage.vue giữ nguyên) */
.app-header {
  background: white;
  border-bottom: 1px solid #e0e0e0;
  padding: 0.75rem 0;
  position: relative; 
  z-index: 1001;
}
.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 0.5rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
}
.left-section {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  position: relative;
}
.menu-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #333;
}
.logo img {
  height: 40px;
  display: block;
}
.center-section {
  flex-grow: 1;
  display: flex;
  justify-content: center;
  gap: 1rem;
}
.shop-now-btn, .support-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: transform 0.2s;
}
.shop-now-btn:hover, .support-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}
.shop-now-btn {
  background-color: #f5a623;
  color: black;
}
.support-btn {
  background-color: #f5f5ff;
  color: #333;
  border: 1px solid #ddd;
}
.right-section {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}
.action-icons {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}
.icon-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.4rem;
  color: #333;
}
.post-btn {
  background-color: #f5a623;
  color: black;
  font-weight: bold;
  border: none;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  white-space: nowrap;
}
.user-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.manage-btn {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  white-space: nowrap;

  /* BỔ SUNG: Thêm các dòng này để tạo viền nút */
  border: 1px solid #ccc;
  padding: 0.6rem 1rem;
  border-radius: 8px;
}

/* (Tùy chọn) Thêm hiệu ứng hover */
.manage-btn:hover {
  border-color: #007bff;
  background-color: #f5f5f5;
}
/* SỬA ĐỔI: Thêm CSS cho <router-link> */
.auth-btn {
  padding: 0.75rem 1.25rem; 
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold; 
  font-size: 0.9rem; 
  text-decoration: none;
  display: inline-block;
}
.login-btn {
  background-color: #f5a623;
  color: black;
}
.register-btn {
  background-color: #eee;
  color: #333;
}
.avatar-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}
.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: #6f42c1;
  border: 2px solid white;
  box-shadow: 0 0 5px rgba(0,0,0,0.2);
}
.arrow-small {
  font-size: 0.8rem;
}
.user-dropdown {
  position: absolute;
  top: 120%;
  right: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  padding: 0.5rem;
  width: 200px;
  z-index: 100;
  display: flex;
  flex-direction: column;
}
.user-dropdown a,
.user-dropdown button {
  padding: 0.75rem 1rem;
  border: none;
  background: none;
  text-align: left;
  cursor: pointer;
  border-radius: 4px;
  text-decoration: none;
  color: #333;
}
.user-dropdown a:hover,
.user-dropdown button:hover {
  background-color: #f5f5f5;
}
.category-dropdown-menu {
  position: absolute; 
  top: calc(100% + 10px);
  left: 0;
  min-width: 300px;
  background: white;
  border-bottom: 1px solid #eee;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  max-height: 400px;
  overflow-y: auto;
  border-radius: 8px;
}
.category-dropdown-menu ul {
  list-style: none;
  padding: 0.5rem;
  margin: 0;
}
.category-dropdown-menu li {
  padding: 0.75rem 1rem;
  font-size: 1rem;
  cursor: pointer;
  border-radius: 4px;
}
.category-dropdown-menu li:hover {
  background-color: #f5f5f5;
}

/* Notification Dropdown */
.notification-wrapper {
  position: relative;
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ef4444;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 11px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid white;
}

.notification-dropdown {
  position: absolute;
  top: calc(100% + 15px);
  right: 0;
  width: 380px;
  max-height: 500px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.notification-header {
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f9fafb;
}

.notification-header h3 {
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.mark-all-read {
  background: none;
  border: none;
  color: #3b82f6;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  transition: all 0.2s;
}

.mark-all-read:hover {
  background: #eff6ff;
}

.notification-list {
  max-height: 400px;
  overflow-y: auto;
}

.empty-notifications {
  padding: 3rem 1rem;
  text-align: center;
  color: #9ca3af;
}

.empty-icon {
  font-size: 48px;
  display: block;
  margin-bottom: 0.75rem;
  opacity: 0.5;
}

.empty-notifications p {
  font-size: 14px;
  margin: 0;
}

.notification-item {
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #f3f4f6;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  gap: 0.75rem;
  position: relative;
}

.notification-item:hover {
  background: #f9fafb;
}

.notification-item.unread {
  background: #eff6ff;
}

.notification-item.unread:hover {
  background: #dbeafe;
}

.notif-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.notif-icon.order {
  background: #dbeafe;
}

.notif-icon.message {
  background: #fef3c7;
}

.notif-icon.like {
  background: #fce7f3;
}

.notif-icon.system {
  background: #e0e7ff;
}

.notif-content {
  flex: 1;
  min-width: 0;
}

.notif-title {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.25rem 0;
}

.notif-message {
  font-size: 13px;
  color: #6b7280;
  margin: 0 0 0.5rem 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.notif-time {
  font-size: 12px;
  color: #9ca3af;
}

.unread-dot {
  width: 8px;
  height: 8px;
  background: #3b82f6;
  border-radius: 50%;
  position: absolute;
  top: 1.25rem;
  right: 1rem;
}

.notification-footer {
  padding: 0.75rem;
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
}

.notification-footer button {
  width: 100%;
  padding: 0.625rem;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  color: #374151;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.notification-footer button:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
}
</style>