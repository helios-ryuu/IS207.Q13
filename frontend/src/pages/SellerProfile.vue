<template>
  <div class="seller-profile-page">
    <Header />

    <main class="container">
      <nav class="breadcrumbs">
        <router-link to="/">Trang chủ</router-link> / Người dùng /
        <strong>{{ seller.name || 'Đang tải...' }}</strong>
      </nav>

      <!-- Seller Header -->
      <div class="seller-header-card" v-if="seller.id">
        <div class="seller-info-left">
          <div class="avatar-wrapper">
            <div v-if="!seller.avatar" class="seller-avatar-letter">
              {{ seller.name ? seller.name.charAt(0).toUpperCase() : 'U' }}
            </div>
            <img v-else :src="seller.avatar" :alt="seller.name" class="seller-avatar">
          </div>
          <div class="seller-basic">
            <h1 class="seller-name">{{ seller.name }}</h1>
            <div class="seller-meta">
              <span class="active-time">Thành viên từ {{ seller.joinDate }}</span>
            </div>
            <div class="seller-actions">
              <button class="btn-chat" @click="handleChat">
                <font-awesome-icon icon="comment" /> Chat ngay
              </button>
            </div>
          </div>
        </div>

        <div class="seller-stats-right">
          <div class="stat-item">
            <strong v-if="seller.rating > 0">{{ seller.rating.toFixed(1) }} <span style="color:#ffc107">★</span></strong>
            <strong v-else style="font-size: 0.9rem; color: #888;">Chưa có</strong>
            <span>Đánh giá{{ seller.reviewCount > 0 ? ` (${seller.reviewCount})` : '' }}</span>
          </div>
          <div class="stat-item">
            <strong>{{ listings.length }}</strong>
            <span>Tin đang bán</span>
          </div>
          <div class="stat-item">
            <strong>{{ seller.joinDate }}</strong>
            <span>Tham gia</span>
          </div>
        </div>
      </div>

      <!-- Listings Section -->
      <section class="shop-products">
        <div class="section-header">
          <h2>Tin đang đăng <span>({{ listings.length }})</span></h2>
          <!-- Filter (Giữ nguyên giao diện, chưa xử lý logic sort local) -->
          <div class="sort-filter">
            <select>
              <option>Mới nhất</option>
              <option>Giá thấp đến cao</option>
              <option>Giá cao đến thấp</option>
            </select>
          </div>
        </div>

        <div v-if="loading" class="loading-state">
          <div class="spinner"></div> Đang tải dữ liệu...
        </div>

        <div v-else-if="listings.length > 0" class="product-grid">
          <ProductCard
              v-for="item in listings"
              :key="item.id"
              :product="item"
          />
        </div>

        <div v-else class="empty-state">
          <p>Người bán này chưa có tin đăng nào.</p>
        </div>
      </section>

    </main>
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../utils/api'; // Import API
import Header from '../components/layout/SearchHeader.vue'; // Dùng SearchHeader cho đồng bộ
import Footer from '../components/layout/AppFooter.vue';
import ProductCard from '../components/product/ProductCardSimple.vue';

const route = useRoute();
const router = useRouter();

const loading = ref(false);

const seller = ref({
  id: 0,
  name: '',
  avatar: null,
  joinDate: '',
  rating: 0,
  reviewCount: 0
});

const listings = ref([]);

// Fallback images - Data URI 
const FALLBACK_IMAGE = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200" fill="%23eee"%3E%3Crect width="100%25" height="100%25"/%3E%3Ctext x="50%25" y="50%25" fill="%23999" font-size="14" text-anchor="middle" dy=".3em"%3ENo Image%3C/text%3E%3C/svg%3E';

// Helper xử lý ảnh
const getImageUrl = (url) => {
  if (!url) return FALLBACK_IMAGE;
  if (url.startsWith('http://localhost/')) {
    return url.replace('http://localhost/', 'http://localhost:8000/');
  }
  if (url.startsWith('http')) return url;
  return `http://localhost:8000${url}`;
};

const formatPrice = (price) => new Intl.NumberFormat('vi-VN').format(price) + ' đ';

// Hàm lấy dữ liệu
const fetchShopData = async () => {
  const sellerId = route.params.id;
  if (!sellerId) return;

  loading.value = true;
  try {
    // Gọi API lấy danh sách sản phẩm của Seller
    const response = await api.get(`/products/seller/${sellerId}`);
    
    // API trả về { data: [...] }
    const rawData = response.data.data || response.data || [];
    
    // 1. Map danh sách sản phẩm
    listings.value = rawData.map(item => ({
      id: item.id,
      title: item.name,
      price: formatPrice(item.price_range?.min || 0),
      location: item.location || 'Toàn quốc', // Lấy từ API
      imageUrl: getImageUrl(item.thumbnail),
      // User info để hiển thị trên card (nếu cần)
      seller: item.seller?.name,
      sellerId: item.seller?.id,
      userAvatar: getImageUrl(item.seller?.avatar)
    }));

    // 2. Lấy thông tin Seller từ sản phẩm đầu tiên (nếu có)
    if (rawData.length > 0) {
      const firstItem = rawData[0];
      const createdDate = firstItem.created_at ? new Date(firstItem.created_at) : new Date();
      const formattedDate = createdDate.toLocaleDateString('vi-VN'); // DD/MM/YYYY
      
      seller.value = {
        id: firstItem.seller?.id,
        name: firstItem.seller?.name || firstItem.seller?.full_name || 'Người bán',
        avatar: firstItem.seller?.avatar_url || firstItem.seller?.avatar || null,
        joinDate: formattedDate,
        rating: 0,
        reviewCount: 0
      };
      
      // 3. Lấy đánh giá của seller
      try {
        const reviewRes = await api.get(`/sellers/${sellerId}/reviews`);
        const reviews = reviewRes.data.data || [];
        if (reviews.length > 0) {
          const totalRating = reviews.reduce((sum, r) => sum + (r.rating || 0), 0);
          seller.value.rating = totalRating / reviews.length;
          seller.value.reviewCount = reviews.length;
        }
      } catch (e) {
        console.log('No reviews found');
      }
    } else {
      // Nếu Seller chưa có bài đăng nào
      seller.value.id = sellerId;
      seller.value.name = 'Người dùng #' + sellerId;
    }

  } catch (error) {
    console.error("Lỗi tải thông tin shop:", error);
  } finally {
    loading.value = false;
  }
};

const handleChat = () => {
  if (!seller.value.id) return;
  router.push({
    path: '/chat',
    query: {
      partnerId: seller.value.id,
      name: seller.value.name,
      avatar: seller.value.avatar
    }
  });
};



// Watch ID thay đổi (khi bấm vào seller khác từ trang này)
watch(() => route.params.id, (newId) => {
  if (newId) fetchShopData();
});

onMounted(() => {
  fetchShopData();
});
</script>

<style scoped>
/* CSS CŨ CỦA BẠN (Giữ nguyên) */
.seller-profile-page { background-color: #f4f4f4; min-height: 100vh; padding-bottom: 40px; }
.container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 15px; }
.breadcrumbs { padding: 20px 0; color: #555; font-size: 0.9rem; }
.breadcrumbs a { text-decoration: none; color: #555; }
.seller-header-card { background: white; border-radius: 8px; padding: 24px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 24px; flex-wrap: wrap; gap: 20px; }
.seller-info-left { display: flex; gap: 20px; align-items: center; }
.avatar-wrapper { position: relative; }
.seller-avatar { width: 80px; height: 80px; border-radius: 50%; border: 1px solid #eee; object-fit: cover; }
.seller-avatar-letter { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 32px; }
.status-dot { width: 14px; height: 14px; background-color: #4caf50; border: 2px solid white; border-radius: 50%; position: absolute; bottom: 5px; right: 5px; }
.seller-basic { display: flex; flex-direction: column; gap: 8px; }
.seller-name { font-size: 1.5rem; font-weight: bold; margin: 0; }
.active-time { font-size: 0.85rem; color: #777; }
.seller-actions { display: flex; gap: 10px; margin-top: 5px; }
.btn-chat, .btn-follow { padding: 8px 16px; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; gap: 6px; }
.btn-chat { background-color: #ffd60a; border: none; color: #000; }
.btn-follow { background-color: white; border: 1px solid #ddd; color: #333; }
.seller-stats-right { display: flex; gap: 30px; border-left: 1px solid #eee; padding-left: 30px; }
.stat-item { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.shop-products { background: white; border-radius: 8px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
.section-header h2 { margin: 0; font-size: 1.25rem; color: #1e293b; }
.section-header h2 span { font-weight: 400; color: #64748b; font-size: 1rem; }

/* Sort Filter Dropdown */
.sort-filter {
  position: relative;
}

.sort-filter select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 10px 36px 10px 14px;
  font-size: 14px;
  font-weight: 500;
  color: #475569;
  cursor: pointer;
  outline: none;
  transition: all 0.2s ease;
  min-width: 160px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
}

.sort-filter select:hover {
  border-color: #cbd5e1;
  background-color: #f1f5f9;
}

.sort-filter select:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.sort-filter select option {
  padding: 10px;
  background: white;
  color: #1e293b;
}

.product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
.loading-state { text-align: center; padding: 40px; color: #777; }
.empty-state { text-align: center; padding: 50px; color: #999; font-style: italic; }

@media (max-width: 768px) { 
  .seller-header-card { flex-direction: column; align-items: flex-start; } 
  .seller-stats-right { width: 100%; border-left: none; padding-left: 0; justify-content: space-between; border-top: 1px solid #eee; padding-top: 15px; } 
  .section-header { flex-direction: column; gap: 12px; align-items: flex-start; }
  .sort-filter select { width: 100%; }
}
</style>