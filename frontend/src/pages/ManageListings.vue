<template>
  <div class="manage-page">
    <Header />

    <main class="container">
      <div class="breadcrumbs">
        <span class="brand">VietMarket</span>
        <span class="separator">></span>
        <span class="current">Quản lý tin</span>
      </div>

      <!-- User Header -->
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
            <span class="balance-text">Số dư: 0</span>
            <button class="add-btn">+</button>
          </div>
        </div>
      </div>

      <!-- Tabs Navigation -->
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

      <!-- TOOLBAR: TÌM KIẾM & SẮP XẾP (MỚI) -->
      <div class="filter-toolbar">
        <div class="search-box">
          <input 
            type="text" 
            v-model="searchKeyword" 
            placeholder="Tìm theo tên tin đăng..." 
          />
          <font-awesome-icon icon="search" class="search-icon" />
        </div>

        <div class="sort-box">
          <label>Sắp xếp:</label>
          <select v-model="sortOption">
            <option value="newest">Mới nhất</option>
            <option value="oldest">Cũ nhất</option>
            <option value="price_desc">Giá cao xuống thấp</option>
            <option value="price_asc">Giá thấp lên cao</option>
          </select>
        </div>
      </div>

      <!-- Listings List -->
      <div class="listings-container">
        <div v-if="isLoading" class="loading-state">
          <div class="spinner"></div> Đang tải dữ liệu...
        </div>

        <div v-else-if="filteredListings.length > 0" class="list-wrapper">
          <ManageListingCard
              v-for="item in filteredListings"
              :key="item.id"
              :product="item"
              @delete="handleDeleteListing"
              @edit="handleEditListing"
              @toggle-hidden="handleToggleHidden"
          />
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state">
          <img
              src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png"
              alt="No listings"
              class="empty-image"
          />
          <h3 class="empty-title">Không tìm thấy tin đăng</h3>
          <p class="empty-desc">
            {{ searchKeyword ? 'Không có kết quả nào khớp với từ khóa.' : 'Bạn hiện tại không có tin đăng nào cho trạng thái này' }}
          </p>
          <button class="btn-post-new" @click="router.push('/post')">Đăng tin ngay</button>
        </div>
      </div>

    </main>
    <Footer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../utils/useAuth';
import api from '../utils/api';
import { useToast } from '../utils/useToast';
import Header from '../components/layout/SearchHeader.vue';
import Footer from '../components/layout/AppFooter.vue';
import ManageListingCard from '../components/product/ManageListingCard.vue';

const router = useRouter();
const { user, isLoggedIn } = useAuth();
const { showSuccess, showError } = useToast();

const tabs = [
  { id: 'active', label: 'Đang hiển thị' },
  { id: 'hidden', label: 'Đã ẩn' },
  { id: 'pending', label: 'Chờ duyệt' }, // Nếu backend có status này
];

const activeTab = ref('active');
const allListings = ref([]);
const isLoading = ref(false);

// State cho bộ lọc mới
const searchKeyword = ref('');
const sortOption = ref('newest');

// Helper xử lý ảnh - sử dụng data URI để tránh lỗi network
const FALLBACK_IMAGE = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="150" height="150" fill="%23eee"%3E%3Crect width="100%25" height="100%25"/%3E%3Ctext x="50%25" y="50%25" fill="%23999" font-size="12" text-anchor="middle" dy=".3em"%3ENo Image%3C/text%3E%3C/svg%3E';
const getImageUrl = (url) => {
  if (!url) return FALLBACK_IMAGE;
  if (url.startsWith('http')) return url;
  return `http://localhost:8000${url}`;
};

// Fetch Listings
const fetchListings = async () => {
  if (!isLoggedIn.value || !user.value?.id) return;
  
  isLoading.value = true;
  try {
    const response = await api.get(`/products/seller/${user.value.id}`);
    const rawData = response.data.data || response.data || [];
    
    allListings.value = rawData.map(item => ({
      id: item.id,
      status: item.status || 'active', 
      title: item.name,
      condition: 'Đã sử dụng', 
      // Format giá để hiển thị
      price: new Intl.NumberFormat('vi-VN').format(item.price_range?.min || 0) + ' đ',
      // Lưu giá gốc để sắp xếp (QUAN TRỌNG)
      rawPrice: item.price_range?.min || 0,
      imageUrl: getImageUrl(item.thumbnail),
      // Lưu ngày gốc để sắp xếp
      createdAt: item.created_at,
      location: item.location || 'Toàn quốc'
    }));

  } catch (err) {
    console.error('Failed to fetch listings:', err);
  } finally {
    isLoading.value = false;
  }
};

// --- LOGIC LỌC & SẮP XẾP ---
const filteredListings = computed(() => {
  // 1. Lọc theo Tab (Status)
  let result = allListings.value.filter(item => {
    if (activeTab.value === 'active') return item.status === 'active';
    if (activeTab.value === 'hidden') return item.status === 'hidden';
    return item.status === activeTab.value;
  });

  // 2. Lọc theo Từ khóa tìm kiếm
  if (searchKeyword.value.trim()) {
    const k = searchKeyword.value.toLowerCase();
    result = result.filter(item => item.title.toLowerCase().includes(k));
  }

  // 3. Sắp xếp
  result.sort((a, b) => {
    if (sortOption.value === 'newest') {
      return new Date(b.createdAt) - new Date(a.createdAt);
    } else if (sortOption.value === 'oldest') {
      return new Date(a.createdAt) - new Date(b.createdAt);
    } else if (sortOption.value === 'price_desc') {
      return b.rawPrice - a.rawPrice;
    } else if (sortOption.value === 'price_asc') {
      return a.rawPrice - b.rawPrice;
    }
    return 0;
  });

  return result;
});

// --- ACTIONS ---

const handleDeleteListing = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa tin này không?')) return;
  try {
    await api.delete(`/products/${id}`);
    showSuccess('Đã xóa tin đăng');
    allListings.value = allListings.value.filter(item => item.id !== id);
  } catch (e) {
    showError('Xóa thất bại');
  }
};

// Sửa lại hàm Ẩn/Hiện tin cho chuẩn
const handleToggleHidden = async (id) => {
  const product = allListings.value.find(p => p.id === id);
  if (!product) return;

  // Đảo trạng thái logic
  const newStatus = product.status === 'active' ? 'hidden' : 'active';
  
  // Lưu trạng thái cũ để rollback nếu lỗi
  const oldStatus = product.status;

  try {
    // Cập nhật UI trước cho mượt (Optimistic Update)
    product.status = newStatus;

    // Gọi API
    // Lưu ý: Backend phải cho phép update status qua PUT /products/{id}
    await api.put(`/products/${id}`, { 
      name: product.title, // Một số backend yêu cầu gửi lại name/description khi PUT
      description: 'update status', // Gửi đại description nếu backend validate required
      status: newStatus 
    });
    
    showSuccess(newStatus === 'active' ? 'Đã hiển thị tin' : 'Đã ẩn tin');
  } catch (e) {
    console.error(e);
    // Rollback nếu lỗi
    product.status = oldStatus;
    showError('Không thể cập nhật trạng thái: ' + (e.response?.data?.message || 'Lỗi server'));
  }
};

const handleEditListing = (id) => {
  router.push(`/edit-post/${id}`);
};

const selectTab = (tabId) => {
  activeTab.value = tabId;
  searchKeyword.value = ''; // Reset tìm kiếm khi chuyển tab
};

watch(() => user.value, (newUser) => {
  if (newUser?.id) fetchListings();
}, { immediate: true });
</script>

<style scoped>
.manage-page { background-color: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
.container { width: 100%; max-width: 960px; margin: 0 auto; padding: 20px 15px; flex-grow: 1; }
.breadcrumbs { font-size: 14px; color: #007bff; margin-bottom: 20px; font-weight: 600; }
.breadcrumbs .separator { margin: 0 5px; color: #777; }
.breadcrumbs .current { color: #333; }

/* User Header */
.user-header-card { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
.user-info { display: flex; align-items: center; gap: 15px; }
.avatar-circle { width: 60px; height: 60px; border-radius: 50%; background: #6366f1; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 24px; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.user-display-name { font-size: 20px; font-weight: bold; color: #000; }
.balance-pill { background-color: #f0f0f0; border-radius: 20px; padding: 5px 5px 5px 15px; display: flex; align-items: center; gap: 10px; }
.coin-icon { width: 20px; height: 20px; background-color: #ffd700; border-radius: 50%; border: 2px solid #e1b700; }
.balance-text { font-weight: bold; font-size: 15px; color: #333; }
.add-btn { background-color: #4caf50; color: white; border: none; width: 24px; height: 24px; border-radius: 50%; font-size: 18px; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; }

/* Tabs */
.tabs-nav { display: flex; border-bottom: 1px solid #ddd; margin-bottom: 20px; overflow-x: auto; white-space: nowrap; background: #fff; border-radius: 8px 8px 0 0; padding: 0 10px; }
.tab-item { background: none; border: none; padding: 15px 20px; font-size: 14px; font-weight: 600; color: #555; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s; }
.tab-item:hover { color: #000; }
.tab-item.active { color: #007bff; border-bottom-color: #007bff; }

/* TOOLBAR (New) */
.filter-toolbar {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 15px; gap: 15px; flex-wrap: wrap;
}
.search-box {
  flex-grow: 1; position: relative;
}
.search-box input {
  width: 100%; padding: 10px 10px 10px 35px; border: 1px solid #ddd; border-radius: 6px; outline: none;
}
.search-box .search-icon {
  position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #888;
}
.sort-box {
  display: flex; align-items: center; gap: 8px; font-size: 14px; color: #555;
}
.sort-box select {
  padding: 8px; border: 1px solid #ddd; border-radius: 6px; outline: none; background: white; cursor: pointer;
}

/* Listings */
.listings-container { display: flex; flex-direction: column; gap: 15px; min-height: 300px; }
.list-wrapper { display: flex; flex-direction: column; gap: 15px; }
.loading-state { text-align: center; padding: 30px; color: #666; display: flex; align-items: center; justify-content: center; gap: 10px; }
.spinner { width: 20px; height: 20px; border: 2px solid #ddd; border-top-color: #007bff; border-radius: 50%; animation: spin 1s infinite linear; }
@keyframes spin { 100% { transform: rotate(360deg); } }

/* Empty State */
.empty-state { background-color: #fff; border-radius: 8px; padding: 40px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
.empty-image { width: 150px; height: auto; margin-bottom: 20px; opacity: 0.8; }
.empty-title { font-size: 18px; font-weight: bold; color: #222; margin: 0 0 8px 0; }
.empty-desc { font-size: 14px; color: #777; margin: 0 0 24px 0; }
.btn-post-new { background-color: #ff9900; color: white; border: none; border-radius: 4px; padding: 10px 40px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background-color 0.2s; }
.btn-post-new:hover { background-color: #e68a00; }

@media (max-width: 600px) {
  .filter-toolbar { flex-direction: column; align-items: stretch; }
  .sort-box { justify-content: space-between; }
}
</style>