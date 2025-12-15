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
            <img :src="seller.avatar" :alt="seller.name" class="seller-avatar">
            <span class="status-dot online"></span>
          </div>
          <div class="seller-basic">
            <h1 class="seller-name">{{ seller.name }}</h1>
            <div class="seller-meta">
              <span class="active-time">Hoạt động gần đây</span>
            </div>
            <div class="seller-actions">
              <button class="btn-chat" @click="handleChat">
                <font-awesome-icon icon="comment" /> Chat ngay
              </button>
              <!-- Nút theo dõi (Logic này cần API User Follow, tạm thời để giao diện) -->
              <button class="btn-follow" @click="handleFollow">
                <span v-if="isFollowing">Đang theo dõi</span>
                <span v-else>+ Theo dõi</span>
              </button>
            </div>
          </div>
        </div>

        <div class="seller-stats-right">
          <div class="stat-item">
            <strong>{{ seller.rating || 5.0 }} <span style="color:#ffc107">★</span></strong>
            <span>Đánh giá</span>
          </div>
          <div class="stat-item">
            <strong>{{ seller.followerCount || 0 }}</strong>
            <span>Người theo dõi</span>
          </div>
          <div class="stat-item">
            <strong>{{ listings.length }}</strong>
            <span>Tin đang bán</span>
          </div>
          <div class="stat-item">
            <strong>{{ seller.joinDate || 'Mới tham gia' }}</strong>
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
const isFollowing = ref(false);

const seller = ref({
  id: 0,
  name: '',
  avatar: 'https://via.placeholder.com/100?text=U',
  rating: 5.0,
  followerCount: 0,
  joinDate: ''
});

const listings = ref([]);

// Helper xử lý ảnh
const getImageUrl = (url) => {
  if (!url) return 'https://via.placeholder.com/200?text=No+Image';
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
      location: 'Toàn quốc',
      imageUrl: getImageUrl(item.thumbnail),
      // User info để hiển thị trên card (nếu cần)
      seller: item.seller?.name,
      sellerId: item.seller?.id,
      userAvatar: getImageUrl(item.seller?.avatar)
    }));

    // 2. Lấy thông tin Seller từ sản phẩm đầu tiên (nếu có)
    if (rawData.length > 0) {
      const firstItem = rawData[0];
      seller.value = {
        id: firstItem.seller?.id,
        name: firstItem.seller?.name || 'Người dùng',
        avatar: getImageUrl(firstItem.seller?.avatar),
        rating: 4.8, // Mock
        followerCount: 10, // Mock
        joinDate: '2023' // Mock
      };
    } else {
      // Nếu Seller chưa có bài đăng nào -> Cần gọi API GET User info riêng
      // Nhưng hiện tại chưa có API đó, nên tạm thời để thông tin trống hoặc mock
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

const handleFollow = () => {
  isFollowing.value = !isFollowing.value;
  // TODO: Gọi API Follow
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
.product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
.loading-state { text-align: center; padding: 40px; color: #777; }
.empty-state { text-align: center; padding: 50px; color: #999; font-style: italic; }
@media (max-width: 768px) { .seller-header-card { flex-direction: column; align-items: flex-start; } .seller-stats-right { width: 100%; border-left: none; padding-left: 0; justify-content: space-between; border-top: 1px solid #eee; padding-top: 15px; } }
</style>