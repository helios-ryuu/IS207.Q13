<template>
  <div class="seller-profile-page">
    <Header />

    <main class="container">
      <nav class="breadcrumbs">
        Trang chủ / Người bán / <strong>{{ seller.name || 'Đang tải...' }}</strong>
      </nav>

      <div class="seller-header-card" v-if="seller">
        <div class="seller-info-left">
          <div class="avatar-wrapper">
            <img :src="seller.avatar" :alt="seller.name" class="seller-avatar">
            <span class="status-dot online"></span>
          </div>
          <div class="seller-basic">
            <h1 class="seller-name">{{ seller.name }}</h1>
            <div class="seller-meta">
              <span class="active-time">Hoạt động 5 phút trước</span>
            </div>
            <div class="seller-actions">
              <button class="btn-chat" @click="handleChat">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                Chat ngay
              </button>
              <button class="btn-follow" @click="handleFollow">
                <span v-if="isFollowing">Đang theo dõi</span>
                <span v-else>+ Theo dõi</span>
              </button>
            </div>
          </div>
        </div>

        <div class="seller-stats-right">
          <div class="stat-item">
            <strong>{{ seller.rating }} <span style="color:#ffc107">★</span></strong>
            <span>Đánh giá</span>
          </div>
          <div class="stat-item">
            <strong>{{ seller.followerCount }}</strong>
            <span>Người theo dõi</span>
          </div>
          <div class="stat-item">
            <strong>{{ seller.responseRate }}%</strong>
            <span>Phản hồi chat</span>
          </div>
          <div class="stat-item">
            <strong>{{ seller.joinDate }}</strong>
            <span>Ngày tham gia</span>
          </div>
        </div>
      </div>

      <section class="shop-products">
        <div class="section-header">
          <h2>Tin đang đăng <span>({{ listings.length }})</span></h2>

          <div class="sort-filter">
            <select>
              <option>Mới nhất</option>
              <option>Giá thấp đến cao</option>
              <option>Giá cao đến thấp</option>
            </select>
          </div>
        </div>

        <div class="product-grid">
          <ProductCard
              v-for="item in listings"
              :key="item.id"
              :product="item"
          />
        </div>

        <div v-if="loading" class="loading-state">Đang tải dữ liệu...</div>
      </section>

    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
// Import các components
import Header from '../components/layout/HomeHeader.vue';
import Footer from '../components/layout/AppFooter.vue';
import ProductCard from '../components/product/ProductCardSimple.vue';

const route = useRoute();
const router = useRouter();

const loading = ref(false);
const isFollowing = ref(false);

// Dữ liệu Người bán
const seller = ref({
  id: 0,
  name: '',
  avatar: '',
  rating: 0,
  followerCount: 0,
  responseRate: 0,
  joinDate: ''
});

// Dữ liệu Tin đăng (Listings) - Đã đổi tên biến
const listings = ref([]);

// Hàm xử lý Chat
const handleChat = () => {
  router.push({
    path: '/chat',
    query: {
      sellerId: seller.value.id,
      sellerName: seller.value.name,
      sellerAvatar: seller.value.avatar
    }
  });
};

const handleFollow = () => {
  isFollowing.value = !isFollowing.value;
};

// Hàm lấy dữ liệu
const fetchShopData = async () => {
  loading.value = true;
  const sellerIdParam = route.params.id;

  try {
    await new Promise(r => setTimeout(r, 500));

    // 1. Set thông tin Người bán
    seller.value = {
      id: sellerIdParam || 123,
      name: 'Huy Nguyễn', // Tên cá nhân thay vì "Store"
      avatar: 'https://via.placeholder.com/100/FFD60A/333333?text=H',
      rating: 4.8,
      followerCount: 152,
      responseRate: 95,
      joinDate: '20/10/2023'
    };

    // 2. Set danh sách Tin đăng
    listings.value = Array.from({ length: 12 }, (_, i) => ({
      id: i + 1,
      title: `Tin đăng số ${i + 1} - Cần pass lại`, // Đổi tiêu đề cho giống tin đăng
      price: `${(i + 1) * 150}.000 đ`,
      location: 'Hà Nội',
      imageUrl: `https://via.placeholder.com/200/${Math.floor(Math.random()*16777215).toString(16)}/FFFFFF?text=Tin+${i+1}`,
      username: seller.value.name
    }));

  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchShopData();
});
</script>

<style scoped>
.seller-profile-page {
  background-color: #f4f4f4;
  min-height: 100vh;
  padding-bottom: 40px;
}
.container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
}

.breadcrumbs {
  padding: 20px 0;
  color: #555;
  font-size: 0.9rem;
}

/* --- SELLER HEADER CARD --- */
.seller-header-card {
  background: white;
  border-radius: 8px;
  padding: 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 20px;
}

/* Left side: Avatar & Info */
.seller-info-left {
  display: flex;
  gap: 20px;
  align-items: center;
}
.avatar-wrapper {
  position: relative;
}
.seller-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 1px solid #eee;
  object-fit: cover;
}
.status-dot {
  width: 14px;
  height: 14px;
  background-color: #4caf50; /* Green for online */
  border: 2px solid white;
  border-radius: 50%;
  position: absolute;
  bottom: 5px;
  right: 5px;
}

.seller-basic {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.seller-name {
  font-size: 1.5rem;
  font-weight: bold;
  margin: 0;
}
.active-time {
  font-size: 0.85rem;
  color: #777;
}
.seller-actions {
  display: flex;
  gap: 10px;
  margin-top: 5px;
}
.btn-chat, .btn-follow {
  padding: 8px 16px;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 6px;
}
.btn-chat {
  background-color: #ffd60a; /* Vàng thương hiệu */
  border: none;
  color: #000;
}
.btn-follow {
  background-color: white;
  border: 1px solid #ddd;
  color: #333;
}
.btn-follow:hover {
  background-color: #f9f9f9;
}

/* Right side: Stats */
.seller-stats-right {
  display: flex;
  gap: 30px;
  border-left: 1px solid #eee;
  padding-left: 30px;
}
.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}
.stat-item strong {
  font-size: 1.1rem;
  color: #333;
}
.stat-item span {
  font-size: 0.85rem;
  color: #777;
}

/* --- LISTING LIST SECTION --- */
.shop-products {
  background: white;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  border-bottom: 1px solid #eee;
  padding-bottom: 15px;
}
.section-header h2 {
  font-size: 1.25rem;
  font-weight: bold;
  margin: 0;
}
.section-header span {
  color: #777;
  font-weight: normal;
}
.sort-filter select {
  padding: 6px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  outline: none;
}

/* Grid Layout */
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 15px;
}

.loading-state {
  text-align: center;
  padding: 40px;
  color: #777;
}

/* Responsive */
@media (max-width: 768px) {
  .seller-header-card {
    flex-direction: column;
    align-items: flex-start;
  }
  .seller-stats-right {
    width: 100%;
    border-left: none;
    padding-left: 0;
    justify-content: space-between;
    border-top: 1px solid #eee;
    padding-top: 15px;
  }
}
</style>