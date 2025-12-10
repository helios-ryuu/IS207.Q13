<template>
  <div class="home-page">
    <Header />
    <main class="container">

      <SearchFilterBar />
      <CategoryGrid />

      <section class="product-listings">

        <div class="tabs">
          <button
              :class="{ active: activeTab === 'for-you' }"
              @click="setActiveTab('for-you')">
            Dành cho bạn
          </button>
          <button
              :class="{ active: activeTab === 'newest' }"
              @click="setActiveTab('newest')">
            Mới nhất
          </button>
          <button
              :class="{ active: activeTab === 'trending' }"
              @click="setActiveTab('trending')">
            Xu hướng
          </button>
        </div>
        <div class="product-grid">
          <ProductCard
              v-for="product in products"
              :key="product.id"
              :product="product"
          />
        </div>

        <div v-if="products.length === 0 && loading" class="empty-state">
          Đang tải sản phẩm...
        </div>

        <div v-if="hasMoreProducts" class="see-more-container">
          <button class="see-more-btn" @click="loadMore" :disabled="loading">
            {{ loading ? 'Đang tải...' : 'Xem thêm' }}
          </button>
        </div>
      </section>

      <section class="about-us">
        <h2>VietMarket - Cho đồ cũ một đời mới, cho bạn một trải nghiệm hay</h2>
        <p>Chúng tôi tin rằng giá trị không nằm ở việc "mới" hay "cũ". Nó nằm ở công năng, ở kỷ niệm, và ở hành trình tiếp theo của món đồ đó.
          <br>
          VietMarket không chỉ là một nền tảng mua bán. Chúng tôi là một cộng đồng, nơi bạn có thể trao đi chiếc xe đạp cũ đã cùng bạn tới trường, tìm lại cuốn sách hiếm tưởng đã mất, hay bắt đầu một công việc mới từ một tin đăng.
          <br>
          Ra đời với sự thấu hiểu thói quen tiêu dùng của người Việt, VietMarket biến mỗi giao dịch thành một cuộc gặp gỡ. Đó là nơi bạn "pass" lại đam mê cho một người đồng điệu, giải phóng không gian sống, và góp phần tạo nên một vòng tuần hoàn ý nghĩa cho đồ vật.
          <br>
          Chúng tôi kết nối hàng triệu người Việt mỗi ngày — từ thành thị đến nông thôn — tạo nên một khu chợ số vừa quen thuộc như tiếng rao ngoài ngõ, vừa hiện đại và an toàn tuyệt đối</p>
      </section>

    </main>
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../utils/api';

// --- COMPONENT IMPORTS ---
import Header from '../components/Header-HomePage.vue';
import CategoryGrid from '../components/CategoryGrid.vue';
import ProductCard from '../components/Product/ProductCard_NoReceive.vue';
import SearchFilterBar from '../components/SearchFilterBar.vue';
import Footer from '../components/Footer.vue';

// --- PAGE STATE ---
const activeTab = ref('for-you');
const products = ref([]);
const loading = ref(false);
const pageToLoad = ref(1);
const hasMoreProducts = ref(true);
const totalProducts = ref(0);

// Format price helper
const formatPrice = (price) => {
  if (!price) return '';
  return new Intl.NumberFormat('vi-VN').format(price) + ' đ';
};

// Map API response to component format
const mapProduct = (item) => ({
  id: item.id,
  title: item.name,
  price: formatPrice(item.price_range?.min || item.variants?.[0]?.price),
  originalPrice: '',
  seller: item.seller?.full_name || item.seller?.name || 'Shop VietMarket',
  location: 'TP. HCM',
  imageUrl: item.image || item.variants?.[0]?.images?.[0]?.image_url || 'https://via.placeholder.com/200/eeeeee/cccccc?text=No+Image',
  username: item.seller?.username || 'seller'
});

// --- FETCH PRODUCTS FROM API ---
const fetchProducts = async () => {
  if (loading.value) return;
  loading.value = true;

  try {
    const limit = pageToLoad.value === 1 ? 12 : 8;
    const response = await api.get('/products', {
      params: {
        page: pageToLoad.value,
        per_page: limit,
      }
    });

    const apiData = response.data.data || response.data;
    const newProducts = Array.isArray(apiData) ? apiData : apiData.data || [];
    
    // Map and append products
    const mappedProducts = newProducts.map(mapProduct);
    products.value.push(...mappedProducts);

    // Check if more products available
    const meta = response.data.meta || response.data;
    totalProducts.value = meta.total || newProducts.length;
    hasMoreProducts.value = products.value.length < totalProducts.value;

  } catch (error) {
    console.error('Lỗi khi tải sản phẩm:', error);
    hasMoreProducts.value = false;
  } finally {
    loading.value = false;
  }
};

// Load on mount
onMounted(() => {
  fetchProducts();
});

// --- TAB CHANGE ---
const setActiveTab = (tabName) => {
  activeTab.value = tabName;
  products.value = [];
  pageToLoad.value = 1;
  hasMoreProducts.value = true;
  fetchProducts();
};

// --- LOAD MORE ---
const loadMore = () => {
  pageToLoad.value++;
  fetchProducts();
};
</script>

<style scoped>
/* (Toàn bộ CSS của bạn giữ nguyên, không thay đổi) */
.home-page {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: #f4f4f4;
}
.container {
  width: 100%;
  max-width: 1200px;
  margin: 20px auto;
  padding: 0 15px;
  flex-grow: 1;
  position: relative;
  z-index: 2;
}
.product-listings,
.about-us {
  background-color: #ffffff;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.tabs {
  display: flex;
  gap: 10px;
  border-bottom: 2px solid #eee;
  margin-bottom: 20px;
}
.tabs button {
  padding: 10px 20px;
  border: none;
  background-color: transparent;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  color: #777;
  border-bottom: 3px solid transparent;
}
.tabs button.active {
  color: #007bff;
  border-bottom-color: #007bff;
}
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 20px;
}
.empty-state {
  text-align: center;
  padding: 50px;
  color: #777;
  font-style: italic;
}
.see-more-container {
  text-align: center;
  margin-top: 30px;
}
.see-more-btn {
  padding: 12px 40px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s;
}
.see-more-btn:hover {
  background-color: #0056b3;
}
.see-more-btn:disabled {
  background-color: #999;
  cursor: not-allowed;
}
.about-us h2 {
  font-size: 1.2rem;
  font-weight: bold;
}
.about-us p {
  font-size: 0.9rem;
  line-height: 1.6;
  color: #555;
}
</style>