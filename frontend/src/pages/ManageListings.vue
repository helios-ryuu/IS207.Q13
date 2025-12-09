<template>
  <div class="manage-page">
    <Header />

    <main class="container">
      <div class="breadcrumbs">
        <span class="brand">VietMarket</span>
        <span class="separator">></span>
        <span class="current">Quản lý tin</span>
      </div>

      <div class="user-header-card">
        <div class="user-info">
          <div class="avatar-circle">
            {{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}
          </div>
          <span class="user-display-name">{{ user?.name || 'Người dùng' }}</span>
        </div>

        <div class="balance-wrapper">
          <div class="balance-pill">
            <div class="coin-icon"></div>
            <span class="balance-text">Số dư: {{ user?.balance || 0 }}</span>
            <button class="add-btn">+</button>
          </div>
        </div>
      </div>

      <div class="tabs-nav">
        <button
            v-for="tab in tabs"
            :key="tab.id"
            :class="['tab-item', { active: activeTab === tab.id }]"
            @click="selectTab(tab.id)"
        >
          {{ tab.label }}
        </button>
      </div>

      <div class="listings-container">
        <ManageListingCard
            v-for="item in filteredListings"
            :key="item.id"
            :product="item"
        />

        <div v-if="filteredListings.length === 0" class="empty-state">
          <img
              src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png"
              alt="No listings"
              class="empty-image"
          />

          <h3 class="empty-title">Không tìm thấy tin đăng</h3>
          <p class="empty-desc">Bạn hiện tại không có tin đăng nào cho trạng thái này</p>

          <button class="btn-post-new">Đăng tin</button>
        </div>
      </div>

    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuth } from '../utils/useAuth';
import Header from '../components/Header-Other.vue';
import Footer from '../components/Footer.vue';
import ManageListingCard from '../components/Product/ManageListingCard.vue';

const { user } = useAuth();

const tabs = [
  { id: 'active', label: 'Đang hiển thị' },
  { id: 'expired', label: 'Hết hạn' },
  { id: 'rejected', label: 'Bị từ chối' },
  { id: 'payment', label: 'Cần thanh toán' },
  { id: 'draft', label: 'Tin nháp' },
  { id: 'pending', label: 'Chờ duyệt' },
  { id: 'hidden', label: 'Đã ẩn' },
];

const activeTab = ref('active');

// Dữ liệu gốc (Giả lập Database)
const allListings = [
  { id: 1, status: 'active', title: 'Máy quạt mini giá tốt', condition: 'Đã sử dụng', price: '400.000 đ', location: 'Bình Dương', imageUrl: 'https://via.placeholder.com/150/ccffcc/000000?text=Fan+1', userName: user.value?.name || 'Tôi', userAvatar: 'https://via.placeholder.com/30/6366f1/ffffff?text=Me' },
  { id: 2, status: 'active', title: 'Iphone 12 Promax cũ', condition: 'Đã sử dụng', price: '10.500.000 đ', location: 'TP. HCM', imageUrl: 'https://via.placeholder.com/150/eee/000?text=Phone', userName: user.value?.name || 'Tôi', userAvatar: 'https://via.placeholder.com/30/6366f1/ffffff?text=Me' },
  { id: 3, status: 'expired', title: 'Tai nghe cũ (Đã bán)', condition: 'Cũ', price: '50.000 đ', location: 'TP. HCM', imageUrl: 'https://via.placeholder.com/150/eee/000?text=Headphone', userName: user.value?.name || 'Tôi', userAvatar: 'https://via.placeholder.com/30/6366f1/ffffff?text=Me' },
  // Các trạng thái khác đang để trống để test giao diện Empty State
];

// Computed property để lọc tin theo Tab đang chọn
const filteredListings = computed(() => {
  return allListings.filter(item => item.status === activeTab.value);
});

const selectTab = (tabId) => {
  activeTab.value = tabId;
};
</script>

<style scoped>
.manage-page {
  background-color: #f8f9fa;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.container {
  width: 100%;
  max-width: 960px;
  margin: 0 auto;
  padding: 20px 15px;
  flex-grow: 1;
}

/* Breadcrumbs */
.breadcrumbs {
  font-size: 14px;
  color: #007bff;
  margin-bottom: 20px;
  font-weight: 600;
}
.breadcrumbs .separator { margin: 0 5px; color: #777; }
.breadcrumbs .current { color: #333; }

/* User Header */
.user-header-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  background: #fff;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.user-info { display: flex; align-items: center; gap: 15px; }
.avatar-circle {
  width: 60px; height: 60px; border-radius: 50%; background: #6366f1; color: white;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 24px; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.user-display-name { font-size: 20px; font-weight: bold; color: #000; }

.balance-pill {
  background-color: #f0f0f0; border-radius: 20px; padding: 5px 5px 5px 15px;
  display: flex; align-items: center; gap: 10px;
}
.coin-icon { width: 20px; height: 20px; background-color: #ffd700; border-radius: 50%; border: 2px solid #e1b700; }
.balance-text { font-weight: bold; font-size: 15px; color: #333; }
.add-btn {
  background-color: #4caf50; color: white; border: none; width: 24px; height: 24px;
  border-radius: 50%; font-size: 18px; line-height: 1; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
}

/* Tabs */
.tabs-nav {
  display: flex; border-bottom: 1px solid #ddd; margin-bottom: 20px;
  overflow-x: auto; white-space: nowrap; background: #fff; border-radius: 8px 8px 0 0; padding: 0 10px;
}
.tab-item {
  background: none; border: none; padding: 15px 20px; font-size: 14px; font-weight: 600; color: #555;
  cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s;
}
.tab-item:hover { color: #000; }
.tab-item.active { color: #007bff; border-bottom-color: #007bff; }

/* Listings Container */
.listings-container {
  display: flex;
  flex-direction: column;
  gap: 15px;
  min-height: 300px; /* Đảm bảo có chiều cao để hiện empty state đẹp */
}

/* === EMPTY STATE STYLES (GIỐNG HÌNH) ===
*/
.empty-state {
  background-color: #fff;
  border-radius: 8px;
  padding: 40px 20px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  /* Box shadow nhẹ để nổi lên nền xám */
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.empty-image {
  width: 150px; /* Kích thước ảnh */
  height: auto;
  margin-bottom: 20px;
  opacity: 0.8;
}

.empty-title {
  font-size: 18px;
  font-weight: bold;
  color: #222;
  margin: 0 0 8px 0;
}

.empty-desc {
  font-size: 14px;
  color: #777;
  margin: 0 0 24px 0;
}

.btn-post-new {
  background-color: #ff9900; /* Màu cam */
  color: white;
  border: none;
  border-radius: 4px;
  padding: 10px 40px; /* Nút to ngang */
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-post-new:hover {
  background-color: #e68a00; /* Cam đậm hơn khi hover */
}
</style>