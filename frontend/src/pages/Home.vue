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

// --- COMPONENT IMPORTS ---
import Header from '../components/Header-HomePage.vue';
import CategoryGrid from '../components/CategoryGrid.vue';
import ProductCard from '../components/Product/ProductCard_NoReceive.vue';
import SearchFilterBar from '../components/SearchFilterBar.vue';
import Footer from '../components/Footer.vue';

// --- "DATABASE" GIẢ LẬP (KHÔI PHỤC DỮ LIỆU ĐẸP) ---
const mockDB = [
  {
    id: 1,
    title: 'Áo thun nam tay ngắn hè 2024',
    price: '150.000 đ',
    originalPrice: '250.000 đ',
    seller: 'Shop Thời Trang',
    location: 'Quận 1, TP. HCM',
    imageUrl: 'https://via.placeholder.com/200/eeeeee/cccccc?text=Image+1',
    username: 'Shop Thời Trang'
  },
  {
    id: 2,
    title: 'Tai nghe Bluetooth không dây X15',
    price: '320.000 đ',
    originalPrice: '',
    seller: 'Điện Tử Xanh',
    location: 'Q. Cầu Giấy, Hà Nội',
    imageUrl: 'https://via.placeholder.com/200/eeeeee/cccccc?text=Image+2',
    username: 'Điện Tử Xanh'
  },
  {
    id: 3,
    title: 'Bàn phím cơ DareU EK87',
    price: '790.000 đ',
    originalPrice: '990.000 đ',
    seller: 'PC Gear',
    location: 'Quận 3, TP. HCM',
    imageUrl: 'https://via.placeholder.com/200/eeeeee/cccccc?text=Image+3',
    username: 'PC Gear'
  },
  {
    id: 4,
    title: 'Sofa giường đa năng thông minh',
    price: '2.800.000 đ',
    originalPrice: '',
    seller: 'Nội Thất Giá Kho',
    location: 'Q. Bình Thạnh, TP. HCM',
    imageUrl: 'https://via.placeholder.com/200/eeeeee/cccccc?text=Image+4',
    username: 'Nội Thất Giá Kho'
  }
];

// Thêm 26 sản phẩm mẫu nữa để test "Xem thêm"
for (let i = 5; i <= 30; i++) {
  mockDB.push({
    id: i,
    title: `Sản phẩm mẫu ${i}`,
    price: `${(i * 100 + 50)}.000 đ`,
    originalPrice: `${(i * 100 + 150)}.000 đ`,
    seller: 'Shop VietMarket',
    location: `Quận ${i % 12 + 1}, TP. HCM`,
    imageUrl: `https://via.placeholder.com/200/${Math.floor(Math.random()*16777215).toString(16)}/FFFFFF?text=Product+${i}`,
    username: 'Shop VietMarket'
  });
}
// --- KẾT THÚC DATABASE GIẢ LẬP ---

// --- PAGE STATE ---
const activeTab = ref('for-you');
const products = ref([]);
const loading = ref(false);
const pageToLoad = ref(1);
const hasMoreProducts = ref(true);

// --- HÀM LẤY DỮ LIỆU (ĐÃ SỬA) ---
const fetchProducts = () => { // Bỏ async
  if (loading.value) return;
  loading.value = true;

  try {
    const isInitialLoad = (pageToLoad.value === 1);
    const limit = isInitialLoad ? 12 : 8; // Lần đầu 12, sau đó 8
    const offset = isInitialLoad ? 0 : 12 + (pageToLoad.value - 2) * 8;

    // === NƠI BẠN GỌI API THẬT ===
    // (Trong API thật, bạn sẽ dùng await axios.get(...) ở đây)
    // const response = await axios.get(`/api/products?tab=${activeTab.value}&page=${pageToLoad.value}&limit=${limit}`);
    // const newData = response.data.products;

    // === PHẦN ĐÃ SỬA: BỔ SUNG LẠI DỮ LIỆU GIẢ LẬP ===
    const newData = mockDB.slice(offset, offset + limit);
    // ==========================================

    // Nối dữ liệu mới vào danh sách hiện tại
    products.value.push(...newData);

    // Kiểm tra xem còn sản phẩm để tải nữa không
    if (newData.length < limit || products.value.length === mockDB.length) {
      hasMoreProducts.value = false;
    }

  } catch (error) {
    console.error('Lỗi khi tải sản phẩm:', error);
  } finally {
    loading.value = false;
  }
};

// Gọi hàm fetchProducts() khi component được mount
onMounted(() => {
  fetchProducts(); // Tự động tải 12 SP đầu tiên
});

// --- HÀM KHI CHUYỂN TAB ---
const setActiveTab = (tabName) => {
  activeTab.value = tabName;
  products.value = []; // Xóa sản phẩm cũ
  pageToLoad.value = 1; // Reset về trang 1
  hasMoreProducts.value = true; // Reset nút "Xem thêm"
  fetchProducts(); // Tải 12 SP đầu tiên cho tab mới
};

// --- HÀM MỚI KHI NHẤN "XEM THÊM" ---
const loadMore = () => {
  pageToLoad.value++; // Tăng số trang
  fetchProducts(); // Tải 8 sản phẩm tiếp theo
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