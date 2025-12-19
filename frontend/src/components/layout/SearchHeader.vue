<template>
  <header class="app-header" ref="headerRef">
    <div class="container">

      <!-- Left Section -->
      <div class="left-section">
        <button class="menu-btn" title="Danh mục" @click.stop="toggleCategoryMenu">
          <font-awesome-icon icon="bars" />
        </button>
        <router-link to="/" class="logo">
          <img src="/logo.jpg" alt="VietMarket Logo">
        </router-link>
        <div class="category-dropdown-menu" v-if="isCategoryMenuOpen">
          <ul>
            <!-- Danh sách danh mục tĩnh (sau này có thể thay bằng v-for từ API nếu muốn) -->
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

      <!-- Center Section (SEARCH BAR) -->
      <div class="center-section">
        <div class="location-picker" @click="isLocationPickerOpen = true">
          <font-awesome-icon icon="map-marker-alt" />
          <span>{{ selectedLocation }}</span>
          <font-awesome-icon icon="chevron-down" class="arrow" />
        </div>
        
        <div class="search-bar">
          <!-- SỬA Ở ĐÂY: Thêm v-model và sự kiện Enter -->
          <input 
            type="text" 
            v-model="keyword" 
            placeholder="Tìm sản phẩm..."
            @keyup.enter="handleSearch"
          >
          <button class="search-btn" @click="handleSearch">
            <font-awesome-icon icon="search" />
          </button>
        </div>
      </div>
      
      <!-- Right Section -->
      <div class="right-section">
        <div class="action-icons">
          <button class="icon-btn" title="Yêu thích" @click="$router.push('/favorites')">
            <font-awesome-icon icon="heart" />
          </button>
          <button class="icon-btn" title="Giỏ hàng" @click="$router.push('/cart')">
            <font-awesome-icon icon="shopping-cart" />
            <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
          </button>
          <button class="icon-btn" title="Trò chuyện" @click="handleChatClick">
            <font-awesome-icon icon="comment" />
          </button>
          
          <!-- Notifications -->
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
        
        <button class="post-btn" @click="router.push('/post')">
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
              <div v-if="!user?.avatar_url" class="avatar-letter">
                {{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}
              </div>
              <img v-else :src="user.avatar_url" alt="Avatar" class="avatar">
              <font-awesome-icon icon="chevron-down" class="arrow-small" />
              <div v-if="isUserMenuOpen" class="user-dropdown">
                <router-link to="/profile">Trang cá nhân</router-link>
                <router-link to="/wallet" class="dropdown-item">Ví của tôi</router-link>
                <router-link to="/purchase-orders" class="dropdown-item">Quản lý đơn mua</router-link>
                <router-link to="/sales-orders" class="dropdown-item">Quản lý đơn bán</router-link>
                <router-link v-if="user && user.role === 'admin'" to="/admin">Admin</router-link>
                <button @click="handleLogout">Đăng xuất</button>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
    
  </header>

  <LocationPickerModal 
    v-if="isLocationPickerOpen"
    @close="isLocationPickerOpen = false"
    @applyLocation="handleLocationApply"
  />
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import LocationPickerModal from '../modals/LocationPickerModal.vue';
import { useAuth } from '../../utils/useAuth';
import { useCart } from '../../stores/cart';
import api from '../../utils/api';


const router = useRouter();
const route = useRoute(); // Lấy thông tin URL hiện tại

// --- LOGIC TÌM KIẾM ---
const keyword = ref('');

// Đồng bộ ô input với URL (nếu user gõ URL trực tiếp hoặc back lại)
watch(() => route.query.q, (newQ) => {
  keyword.value = newQ || '';
}, { immediate: true });

const handleSearch = () => {
  // Logic: Giữ lại các filter cũ (category, sort...), chỉ thay đổi 'q'
  const query = { ...route.query };
  
  if (keyword.value.trim()) {
    query.q = keyword.value.trim();
  } else {
    delete query.q; // Xóa nếu rỗng
  }

  // Chuyển hướng
  router.push({ path: '/products', query });
};

// --- CÁC LOGIC KHÁC GIỮ NGUYÊN ---
const isCategoryMenuOpen = ref(false);
const headerRef = ref(null);
const { isLoggedIn, user, logout } = useAuth();
const { cartCount } = useCart();
const isUserMenuOpen = ref(false);
const isLocationPickerOpen = ref(false);
const selectedLocation = ref('Toàn quốc');
const isNotificationOpen = ref(false);
const notifications = ref([]);

// Fetch notifications từ API
const fetchNotifications = async () => {
  if (!isLoggedIn.value) return;
  try {
    const res = await api.get('/notifications');
    const rawData = res.data.data || res.data || [];
    notifications.value = rawData.map(n => ({
      id: n.id,
      type: n.type || 'system',
      title: n.title || 'Thông báo',
      message: n.content || n.message || '',
      time: formatTimeAgo(n.created_at),
      read: !!n.read_at,
      link: n.link || null
    }));
  } catch (e) {
    console.error('Failed to fetch notifications:', e);
  }
};

// Helper format time
const formatTimeAgo = (dateStr) => {
  if (!dateStr) return '';
  const diff = Date.now() - new Date(dateStr).getTime();
  const mins = Math.floor(diff / 60000);
  if (mins < 60) return `${mins} phút trước`;
  const hours = Math.floor(mins / 60);
  if (hours < 24) return `${hours} giờ trước`;
  return `${Math.floor(hours / 24)} ngày trước`;
};

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

const handleLocationApply = (locationName) => {
  selectedLocation.value = locationName;
  isLocationPickerOpen.value = false;
};

const handleChatClick = () => {
  if (isLoggedIn.value) router.push('/chat');
  else router.push('/login');
};

const handleLogout = () => { 
  logout(); 
  isUserMenuOpen.value = false;
  router.push('/home');
};

const toggleUserMenu = () => { isUserMenuOpen.value = !isUserMenuOpen.value; };
const toggleCategoryMenu = () => { isCategoryMenuOpen.value = !isCategoryMenuOpen.value; };
const toggleNotifications = () => { 
  isNotificationOpen.value = !isNotificationOpen.value;
  if (isNotificationOpen.value) fetchNotifications(); // Refresh khi mở
};

const markAllAsRead = async () => {
  try {
    await api.put('/notifications/read-all');
    notifications.value.forEach(n => n.read = true);
  } catch (e) {
    console.error('Failed to mark all as read:', e);
  }
};

const handleNotificationClick = (notif) => {
  notif.read = true;
  if (notif.link) router.push(notif.link);
  isNotificationOpen.value = false;
};

const viewAllNotifications = () => { isNotificationOpen.value = false; };

const getNotificationIcon = (type) => {
  const icons = { order: '📦', message: '💬', like: '❤️', system: '🔔' };
  return icons[type] || '🔔';
};

const selectCategory = (categoryName) => {
  // SỬA: Chuyển sang /products và kèm params category
  router.push({ 
    path: '/products', 
    query: { category: categoryName } 
  });
  isCategoryMenuOpen.value = false;
};

const handleClickOutside = (event) => {
  if (isCategoryMenuOpen.value && headerRef.value && !headerRef.value.contains(event.target)) isCategoryMenuOpen.value = false;
  if (isUserMenuOpen.value && headerRef.value && !headerRef.value.contains(event.target)) isUserMenuOpen.value = false;
  if (isNotificationOpen.value && headerRef.value && !headerRef.value.contains(event.target)) isNotificationOpen.value = false;
};

onMounted(() => { document.addEventListener('click', handleClickOutside); });
onBeforeUnmount(() => { document.removeEventListener('click', handleClickOutside); });
</script>

<style scoped>
/* CSS GIỮ NGUYÊN HOÀN TOÀN */
.app-header { background: white; border-bottom: 1px solid #e0e0e0; padding: 0.75rem 0; position: relative; z-index: 1001; }
.container { max-width: 1400px; margin: 0 auto; padding: 0 0.5rem; display: flex; align-items: center; gap: 1.5rem; }
.left-section { display: flex; align-items: center; gap: 0.5rem; position: relative; }
.menu-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #333; }
.logo img { height: 40px; display: block; }
.center-section { flex-grow: 1; display: flex; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
.location-picker { display: flex; align-items: center; gap: 0.5rem; padding: 0 1.25rem; background-color: #f5f5f5; border-right: 1px solid #ddd; cursor: pointer; white-space: nowrap; }
.location-picker .arrow { font-size: 0.8rem; }
.search-bar { display: flex; flex-grow: 1; }
.search-bar input { flex-grow: 1; border: none; outline: none; padding: 0.75rem 1rem; font-size: 1rem; }
.search-bar .search-btn { background-color: #f5a623; border: none; padding: 0 1.5rem; cursor: pointer; font-size: 1.1rem; color: black; }
.right-section { display: flex; align-items: center; gap: 1.5rem; }
.action-icons { display: flex; align-items: center; gap: 1.5rem; }
.icon-btn { background: none; border: none; cursor: pointer; font-size: 1.4rem; color: #333; }
.post-btn { background-color: #f5a623; color: black; font-weight: bold; border: none; padding: 0.75rem 1.25rem; border-radius: 8px; cursor: pointer; font-size: 0.9rem; white-space: nowrap; }
.user-actions { display: flex; align-items: center; gap: 1rem; }
.manage-btn { text-decoration: none; color: #333; font-weight: 500; white-space: nowrap; border: 1px solid #ccc; padding: 0.6rem 1rem; border-radius: 8px; }
.manage-btn:hover { border-color: #007bff; background-color: #f5f5f5; }
.auth-btn { padding: 0.75rem 1.25rem; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 0.9rem; text-decoration: none; display: inline-block; }
.login-btn { background-color: #f5a623; color: black; }
.register-btn { background-color: #eee; color: #333; }
.avatar-wrapper { position: relative; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
.avatar { width: 36px; height: 36px; border-radius: 50%; background-color: #6f42c1; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.2); object-fit: cover; }
.avatar-letter { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.2); }
.arrow-small { font-size: 0.8rem; }
.user-dropdown { position: absolute; top: 120%; right: 0; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); padding: 0.5rem; width: 200px; z-index: 100; display: flex; flex-direction: column; }
.user-dropdown a, .user-dropdown button { padding: 0.75rem 1rem; border: none; background: none; text-align: left; cursor: pointer; border-radius: 4px; text-decoration: none; color: #333; font-size: 14px; }
.user-dropdown a:hover, .user-dropdown button:hover { background-color: #f5f5f5; }
.category-dropdown-menu { position: absolute; top: calc(100% + 10px); left: 0; min-width: 300px; background: white; border-bottom: 1px solid #eee; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); z-index: 1000; max-height: 400px; overflow-y: auto; border-radius: 8px; }
.category-dropdown-menu ul { list-style: none; padding: 0.5rem; margin: 0; }
.category-dropdown-menu li { padding: 0.75rem 1rem; font-size: 1rem; cursor: pointer; border-radius: 4px; }
.category-dropdown-menu li:hover { background-color: #f5f5f5; }
.notification-wrapper { position: relative; }
.notification-badge { position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; font-weight: bold; display: flex; align-items: center; justify-content: center; border: 2px solid white; }
.notification-dropdown { position: absolute; top: calc(100% + 15px); right: 0; width: 380px; max-height: 500px; background: white; border-radius: 12px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15); z-index: 1000; overflow: hidden; display: flex; flex-direction: column; }
.notification-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #f9fafb; }
.notification-header h3 { font-size: 16px; font-weight: 700; color: #111827; margin: 0; }
.mark-all-read { background: none; border: none; color: #3b82f6; font-size: 13px; font-weight: 600; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 6px; transition: all 0.2s; }
.mark-all-read:hover { background: #eff6ff; }
.notification-list { max-height: 400px; overflow-y: auto; }
.empty-notifications { padding: 3rem 1rem; text-align: center; color: #9ca3af; }
.empty-icon { font-size: 48px; display: block; margin-bottom: 0.75rem; opacity: 0.5; }
.empty-notifications p { font-size: 14px; margin: 0; }
.notification-item { padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: all 0.2s; display: flex; gap: 0.75rem; position: relative; }
.notification-item:hover { background: #f9fafb; }
.notification-item.unread { background: #eff6ff; }
.notification-item.unread:hover { background: #dbeafe; }
.notif-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.notif-icon.order { background: #dbeafe; }
.notif-icon.message { background: #fef3c7; }
.notif-icon.like { background: #fce7f3; }
.notif-icon.system { background: #e0e7ff; }
.notif-content { flex: 1; min-width: 0; }
.notif-title { font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 0.25rem 0; }
.notif-message { font-size: 13px; color: #6b7280; margin: 0 0 0.5rem 0; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.notif-time { font-size: 12px; color: #9ca3af; }
.unread-dot { width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; position: absolute; top: 1.25rem; right: 1rem; }
.notification-footer { padding: 0.75rem; border-top: 1px solid #e5e7eb; background: #f9fafb; }
.notification-footer button { width: 100%; padding: 0.625rem; background: white; border: 1px solid #e5e7eb; border-radius: 8px; color: #374151; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.notification-footer button:hover { background: #f3f4f6; border-color: #d1d5db; }
</style>