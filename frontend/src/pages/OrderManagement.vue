<template>
  <HeaderOther />

  <div class="order-management-page">
    <div class="container">
      
      <div class="breadcrumb">
        <span class="link" @click="goToHome">VietMarket</span> 
        <span class="divider">></span> 
        <span class="current">Đơn mua</span>
      </div>

      <div class="filter-section">
        <div class="filter-row">
          <span class="label">Lọc theo:</span>
          <div class="dropdown-wrapper">
            <button class="btn-dropdown" @click="isDropdownOpen = !isDropdownOpen">
              <font-awesome-icon icon="filter" /> Danh mục
              <font-awesome-icon icon="chevron-down" />
            </button>
            <div class="dropdown-menu" v-if="isDropdownOpen">
              <div v-for="cat in categories" :key="cat" class="dropdown-item" :class="{ 'active-item': selectedCategories.includes(cat) }" @click="toggleCategory(cat)">
                <span>{{ cat }}</span>
                <font-awesome-icon v-if="selectedCategories.includes(cat)" icon="check" class="check-icon" />
              </div>
            </div>
          </div>
          <div class="tags-container">
            <div v-for="cat in selectedCategories" :key="cat" class="selected-tag">
              {{ cat }} <span class="remove-tag" @click="removeCategory(cat)"><font-awesome-icon icon="times" /></span>
            </div>
            <button v-if="selectedCategories.length > 0" class="clear-all-btn" @click="clearAllCategories">Xóa hết</button>
          </div>
        </div>
      </div>

      <div class="status-tabs">
        <button v-for="tab in tabs" :key="tab.id" class="tab-item" :class="{ active: activeTab === tab.id }" @click="changeTab(tab.id)">
          {{ tab.name }}
        </button>
      </div>

      <div class="order-list">
        <div v-for="order in visibleOrders" :key="order.id" class="order-card">
          <div class="card-header">
            <div class="shop-info">
              <span class="shop-name">{{ order.shopName }}</span>
              <button class="btn-chat" @click="contactSeller(order)">
                <font-awesome-icon icon="comments" /> Chat
              </button>
              <button class="btn-view-shop"><font-awesome-icon icon="store" /> Xem Shop</button>
            </div>
            <div class="order-status">
              <span class="delivery-status"><font-awesome-icon icon="truck" /> {{ order.deliveryStatus }}</span>
              <span class="status-label">{{ order.statusLabel }}</span>
            </div>
          </div>

          <div class="product-row">
            <img :src="order.image" alt="Sản phẩm" class="product-img">
            <div class="product-info">
              <h3 class="product-name">{{ order.productName }}</h3>
              <p class="product-variant">Phân loại: {{ order.variant }}</p>
              <p class="product-category-label">Danh mục: {{ order.category }}</p>
              <div class="seller-wrapper">
                <img :src="order.sellerAvatar" class="seller-avatar">
                <span>{{ order.sellerName }}</span>
              </div>
            </div>
            <div class="product-price">
              <span class="current-price">{{ order.price }}</span>
            </div>
          </div>

          <div class="card-footer">
            <div class="total-section">
              Thành tiền: <span class="total-price">{{ order.totalPrice }}</span>
            </div>
            <div class="action-buttons">
              <template v-if="order.statusId === 'completed'">
                
                <button 
                  v-if="!checkIfRated(order.id)" 
                  class="btn btn-primary" 
                  @click="openRatingModal(order)"
                >
                  Đánh Giá
                </button>
                
                <button 
                  v-else 
                  class="btn btn-disabled" 
                  disabled
                >
                  Đã đánh giá
                </button>

                <button class="btn btn-default">Mua Lại</button>
              </template>
              
              <template v-else-if="order.statusId === 'return'">
                <button class="btn btn-default">Chi tiết hoàn tiền</button>
              </template>
              <template v-else>
                <button class="btn btn-default" @click="contactSeller(order)">Liên hệ người bán</button>
              </template>
            </div>
          </div>
        </div>

        <div v-if="hasMoreOrders" class="load-more-container">
          <button class="btn-load-more" @click="loadMore">Xem thêm đơn hàng <font-awesome-icon icon="chevron-down" /></button>
        </div>
        <div v-if="visibleOrders.length === 0" class="empty-result"><p>Không tìm thấy đơn hàng nào phù hợp.</p></div>
      </div>
    </div>
  </div>

  <div v-if="showRatingModal" class="modal-overlay" @click.self="closeRatingModal">
    <div class="modal-content fade-in">
      <div class="modal-header">
        <h3>Đánh giá sản phẩm</h3>
        <button class="close-btn" @click="closeRatingModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>
      
      <div class="modal-body">
        <div class="product-preview" v-if="ratingData.order">
          <img :src="ratingData.order.image" alt="Product">
          <div class="preview-info">
            <p class="name">{{ ratingData.order.productName }}</p>
            <p class="variant">Phân loại: {{ ratingData.order.variant }}</p>
          </div>
        </div>

        <div class="rating-stars-section">
          <p class="label">Chất lượng sản phẩm:</p>
          <div class="stars-wrapper">
            <span v-for="star in 5" :key="star" class="star-btn" @click="ratingData.score = star">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" :fill="star <= ratingData.score ? '#ffc107' : '#e4e5e9'" width="32px" height="32px">
                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
              </svg>
            </span>
          </div>
          <span class="rating-text">{{ getRatingText(ratingData.score) }}</span>
        </div>

        <div class="rating-comment">
          <textarea v-model="ratingData.comment" placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm này nhé..." rows="4"></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-default" @click="closeRatingModal">Trở lại</button>
        <button class="btn btn-primary" @click="submitRating">Hoàn thành</button>
      </div>
    </div>
  </div>

  <Footer />
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import HeaderOther from '../components/layout/SearchHeader.vue';
import Footer from '../components/layout/AppFooter.vue';

const router = useRouter();
const goToHome = () => router.push('/');

// --- DATA ---
const activeTab = ref('all');
const isDropdownOpen = ref(false);
const selectedCategories = ref([]); 
const visibleCount = ref(5); 

// [MỚI] Danh sách các ID đơn hàng đã đánh giá
const ratedOrderIds = ref([]);

const tabs = [
  { id: 'all', name: 'Tất cả' },
  { id: 'pending', name: 'Chờ xác nhận' },
  { id: 'shipping', name: 'Vận chuyển' },
  { id: 'delivering', name: 'Chờ giao hàng' },
  { id: 'completed', name: 'Hoàn thành' },
  { id: 'cancelled', name: 'Đã hủy' },
  { id: 'return', name: 'Trả hàng / Hoàn tiền' }
];

const categories = [
  'Xe cộ', 'Đồ điện tử', 'Thú cưng', 'Đồ ăn, Thực phẩm và các loại khác',
  'Tủ lạnh, Máy lạnh, Máy giặt', 'Đồ gia dụng, Nội thất, Cây cảnh',
  'Thời trang, Đồ dùng cá nhân', 'Giải trí, Thể thao, Sở thích',
  'Đồ dùng văn phòng, Công nông nghiệp'
];

// [MỚI] Thêm sellerId vào dữ liệu để Chat hoạt động
const orders = ref([
  { id: 1, shopName: 'Cửa Hàng Giá Rẻ', sellerId: 101, productName: 'Máy quạt mini giá tốt', variant: 'Đã sử dụng', category: 'Đồ điện tử', statusId: 'completed', deliveryStatus: 'Giao hàng thành công', statusLabel: 'HOÀN THÀNH', price: '400.000 đ', totalPrice: '400.000 đ', image: '/avatar.jpg', sellerName: 'Phạm Khoa', sellerAvatar: '/avatar.jpg' },
  { id: 2, shopName: 'Xe Máy Sài Gòn', sellerId: 102, productName: 'Honda Vision 2021', variant: 'Mới 90%', category: 'Xe cộ', statusId: 'completed', deliveryStatus: 'Giao hàng thành công', statusLabel: 'HOÀN THÀNH', price: '28.000.000 đ', totalPrice: '28.000.000 đ', image: '/avatar.jpg', sellerName: 'Nguyễn Văn A', sellerAvatar: '/avatar.jpg' },
  { id: 3, shopName: 'Shop Quần Áo', sellerId: 103, productName: 'Áo thun nam', variant: 'Size L', category: 'Thời trang, Đồ dùng cá nhân', statusId: 'delivering', deliveryStatus: 'Shipper đang đến', statusLabel: 'CHỜ GIAO HÀNG', price: '150.000 đ', totalPrice: '150.000 đ', image: '/avatar.jpg', sellerName: 'Le Thi B', sellerAvatar: '/avatar.jpg' },
  { id: 4, shopName: 'Tech Store', sellerId: 104, productName: 'Chuột không dây', variant: 'Màu đen', category: 'Đồ điện tử', statusId: 'return', deliveryStatus: 'Đang xử lý hoàn tiền', statusLabel: 'TRẢ HÀNG/HOÀN TIỀN', price: '90.000 đ', totalPrice: '90.000 đ', image: '/avatar.jpg', sellerName: 'Tech Admin', sellerAvatar: '/avatar.jpg' },
  { id: 5, shopName: 'Pet Shop', sellerId: 105, productName: 'Thức ăn mèo', variant: '1kg', category: 'Thú cưng', statusId: 'pending', deliveryStatus: 'Chờ xác nhận', statusLabel: 'CHỜ XÁC NHẬN', price: '50.000 đ', totalPrice: '50.000 đ', image: '/avatar.jpg', sellerName: 'Pet Lover', sellerAvatar: '/avatar.jpg' },
  { id: 6, shopName: 'Nội Thất Xinh', sellerId: 106, productName: 'Ghế Sofa', variant: 'Xám', category: 'Đồ gia dụng, Nội thất, Cây cảnh', statusId: 'shipping', deliveryStatus: 'Đang vận chuyển', statusLabel: 'VẬN CHUYỂN', price: '1.200.000 đ', totalPrice: '1.200.000 đ', image: '/avatar.jpg', sellerName: 'Decor Home', sellerAvatar: '/avatar.jpg' },
]);

// --- [MỚI] LOGIC LOAD TRẠNG THÁI ĐÁNH GIÁ ---
onMounted(() => {
  const storedRatedIds = localStorage.getItem('rated_order_ids');
  if (storedRatedIds) {
    ratedOrderIds.value = JSON.parse(storedRatedIds);
  }
});

const checkIfRated = (orderId) => {
  return ratedOrderIds.value.includes(orderId);
};

// --- [MỚI] LOGIC CHAT VỚI NGƯỜI BÁN ---
const contactSeller = (order) => {
  router.push({
    path: '/chat',
    query: {
      sellerId: order.sellerId,
      sellerName: order.shopName,
      sellerAvatar: order.sellerAvatar,
      productName: order.productName // Context sản phẩm
    }
  });
};

// --- LOGIC MODAL ĐÁNH GIÁ ---
const showRatingModal = ref(false);
const ratingData = reactive({
  order: null,
  score: 5,
  comment: ''
});

const openRatingModal = (order) => {
  ratingData.order = order;
  ratingData.score = 5;
  ratingData.comment = '';
  showRatingModal.value = true;
};

const closeRatingModal = () => {
  showRatingModal.value = false;
};

// --- [MỚI] LOGIC LƯU ĐÁNH GIÁ VÀ CHUYỂN TRANG ---
const submitRating = () => {
  if (!ratingData.order) return;

  // 1. Tạo review
  const newReview = {
    id: Date.now(),
    userName: 'Bạn',
    userAvatar: '/avatar.jpg',
    rating: ratingData.score,
    comment: ratingData.comment || 'Khách hàng không viết bình luận.',
    date: new Date().toLocaleDateString('vi-VN'),
    productName: ratingData.order.productName,
    variant: ratingData.order.variant
  };

  // 2. Lưu vào reviews của sản phẩm
  const productId = ratingData.order.id; // Demo dùng ID đơn hàng làm ID sản phẩm
  const storageKey = `product_reviews_${productId}`;
  const existingReviews = JSON.parse(localStorage.getItem(storageKey) || '[]');
  existingReviews.unshift(newReview);
  localStorage.setItem(storageKey, JSON.stringify(existingReviews));

  // 3. Đánh dấu đơn hàng là "Đã đánh giá"
  ratedOrderIds.value.push(ratingData.order.id);
  localStorage.setItem('rated_order_ids', JSON.stringify(ratedOrderIds.value));

  alert('Đánh giá thành công! Đang chuyển đến trang sản phẩm...');
  closeRatingModal();
  
  // 4. Chuyển sang trang chi tiết
  router.push(`/product/${productId}`);
};

const getRatingText = (score) => {
  const texts = ['Tệ', 'Không hài lòng', 'Bình thường', 'Hài lòng', 'Tuyệt vời'];
  return texts[score - 1] || '';
};

// --- LOGIC LỌC & PHÂN TRANG (GIỮ NGUYÊN) ---
const allFilteredOrders = computed(() => {
  return orders.value.filter(order => {
    const matchTab = activeTab.value === 'all' || order.statusId === activeTab.value;
    const matchCategory = selectedCategories.value.length === 0 || selectedCategories.value.includes(order.category);
    return matchTab && matchCategory;
  });
});

const visibleOrders = computed(() => {
  return allFilteredOrders.value.slice(0, visibleCount.value);
});

const hasMoreOrders = computed(() => {
  return visibleCount.value < allFilteredOrders.value.length;
});

// --- METHODS ---
const loadMore = () => visibleCount.value += 5;
const resetPagination = () => visibleCount.value = 5;
const changeTab = (tabId) => { activeTab.value = tabId; resetPagination(); };
const toggleCategory = (cat) => {
  const index = selectedCategories.value.indexOf(cat);
  if (index === -1) selectedCategories.value.push(cat);
  else selectedCategories.value.splice(index, 1);
  resetPagination(); 
};
const removeCategory = (cat) => {
  const index = selectedCategories.value.indexOf(cat);
  if (index !== -1) selectedCategories.value.splice(index, 1);
  resetPagination();
};
const clearAllCategories = () => { selectedCategories.value = []; resetPagination(); };
</script>

<style scoped>
/* --- THEME COLORS --- */
:root {
  --primary-blue: #0055aa;
  --primary-yellow: #ffc107;
  --text-dark: #333;
}

.order-management-page {
  background-color: #f5f5f5;
  min-height: 100vh;
  padding-bottom: 3rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
}

/* Breadcrumb */
.breadcrumb {
  padding: 1.2rem 0;
  font-size: 0.95rem;
  color: #555;
}
.breadcrumb .link { color: #0055aa; cursor: pointer; font-weight: 600; }
.breadcrumb .divider { margin: 0 0.5rem; color: #999; }

/* Filter Section */
.filter-section {
  background: #fff;
  padding: 1.2rem;
  border-bottom: 1px solid #f2f2f2;
  border-radius: 8px 8px 0 0;
}
.filter-row { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.label { font-weight: 600; color: #555; }

.dropdown-wrapper { position: relative; }
.btn-dropdown {
  background: #fff; border: 1px solid #ddd; padding: 0.6rem 1.2rem;
  border-radius: 8px; cursor: pointer; font-size: 0.95rem;
  display: flex; align-items: center; gap: 0.5rem; color: #333;
  transition: all 0.2s;
}
.btn-dropdown:hover { background-color: #f0f7ff; border-color: #0055aa; color: #0055aa; }

.dropdown-menu {
  position: absolute; top: 115%; left: 0;
  background: white; border: 1px solid #eee;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  border-radius: 8px; min-width: 280px; z-index: 100;
  padding: 0.5rem 0; max-height: 300px; overflow-y: auto;
}
.dropdown-item {
  padding: 0.8rem 1.2rem; cursor: pointer; font-size: 0.9rem;
  color: #333; display: flex; justify-content: space-between;
}
.dropdown-item:hover { background-color: #f5f5f5; color: #0055aa; }
.dropdown-item.active-item { color: #0055aa; font-weight: 600; background-color: #e6f0fa; }

/* Tags */
.tags-container { display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center; }
.selected-tag {
  display: flex; align-items: center; gap: 0.5rem;
  background-color: #fff; border: 1px solid #0055aa; color: #0055aa;
  padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;
  font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.remove-tag:hover { opacity: 1; color: #d0011b; cursor: pointer; }
.clear-all-btn {
  background: none; border: none; color: #555;
  font-size: 0.9rem; cursor: pointer; text-decoration: underline; margin-left: 0.5rem;
}
.clear-all-btn:hover { color: #0055aa; }

/* Tabs */
.status-tabs {
  background: #fff; display: flex; border-bottom: 1px solid #eee;
  margin-bottom: 1.5rem; overflow-x: auto; white-space: nowrap;
  border-radius: 0 0 8px 8px;
}
.tab-item {
  flex: 1; min-width: fit-content; text-align: center;
  padding: 1.2rem 1.5rem; background: none; border: none;
  border-bottom: 3px solid transparent;
  cursor: pointer; font-size: 1rem; color: #666; font-weight: 500;
  transition: all 0.2s;
}
.tab-item:hover { color: #0055aa; }
.tab-item.active { color: #0055aa; border-bottom-color: #0055aa; font-weight: 700; }

/* Order Card */
.order-card {
  background: #fff; margin-bottom: 1.5rem;
  border-radius: 12px; /* Bo góc 12px */
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  overflow: hidden; border: 1px solid #eee;
}
.card-header {
  padding: 1.2rem 1.5rem; border-bottom: 1px solid #f0f0f0;
  display: flex; justify-content: space-between; align-items: center;
  background-color: #fff;
}
.shop-info { display: flex; align-items: center; gap: 1rem; }
.shop-name { font-weight: 700; color: #333; font-size: 1.05rem; }
.btn-chat, .btn-view-shop {
  padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px;
  cursor: pointer; display: flex; align-items: center; gap: 0.4rem; font-weight: 500;
}
.btn-chat { background: #0055aa; color: #fff; border: none; }
.btn-view-shop { background: #fff; border: 1px solid #ddd; color: #555; }

.order-status { display: flex; align-items: center; gap: 1rem; }
.delivery-status { color: #26aa99; font-size: 0.9rem; font-weight: 500; }
.status-label {
  color: #0055aa; text-transform: uppercase; padding-left: 1rem;
  border-left: 2px solid #eee; font-weight: 700;
}

/* Product Info */
.product-row {
  padding: 1.5rem; display: flex; gap: 1.2rem;
  border-bottom: 1px solid #f9f9f9;
}
.product-img { 
  width: 90px; height: 90px; border: 1px solid #eee; 
  object-fit: cover; border-radius: 6px; 
}
.product-info { flex: 1; }
.product-name { font-size: 1.1rem; font-weight: 600; margin: 0 0 0.4rem; color: #333; }
.product-variant { font-size: 0.9rem; color: #777; margin-bottom: 0.4rem; }
.product-category-label { font-size: 0.85rem; color: #999; font-style: italic; }
.seller-wrapper { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.6rem; font-size: 0.9rem; color: #555; }
.seller-avatar { width: 24px; height: 24px; border-radius: 50%; }
.product-price { font-size: 1.1rem; color: #0055aa; font-weight: 700; }

/* Footer & Buttons */
.card-footer {
  background: #f8fbff; padding: 1.5rem;
  display: flex; flex-direction: column; align-items: flex-end; gap: 1.2rem;
}
.total-price { color: #0055aa; font-size: 1.6rem; font-weight: 700; margin-left: 0.5rem; }
.action-buttons { display: flex; gap: 1rem; }
.btn { 
  min-width: 160px; padding: 0.7rem 0; 
  border-radius: 8px; /* Bo góc 8px */
  cursor: pointer; font-size: 1rem; font-weight: 600; transition: all 0.2s;
}
.btn-primary { 
  background: #ffc107; color: #222; border: 1px solid #ffc107; 
  box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
}
.btn-primary:hover { background: #ffb300; transform: translateY(-1px); }
.btn-default { background: #fff; border: 1px solid #ddd; color: #555; }
.btn-default:hover { background: #fff; border-color: #999; color: #333; }

/* [MỚI] Nút đã đánh giá (Disabled) */
.btn-disabled {
  background: #f0f0f0; color: #999; border: 1px solid #ddd;
  cursor: not-allowed; box-shadow: none;
}

/* LOAD MORE BUTTON */
.load-more-container { text-align: center; margin-top: 1.5rem; padding-bottom: 1rem; }
.btn-load-more {
  background: #fff; border: 1px solid #ddd; padding: 0.8rem 2.5rem;
  border-radius: 25px; color: #555; cursor: pointer; font-size: 0.95rem; font-weight: 600;
  transition: all 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.btn-load-more:hover { background: #f9f9f9; border-color: #0055aa; color: #0055aa; }

.empty-result { 
  padding: 4rem; text-align: center; color: #777; 
  background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

/* === MODAL CSS === */
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.5); z-index: 9999;
  display: flex; justify-content: center; align-items: center;
}
.modal-content {
  background: #fff; width: 600px; max-width: 90%;
  border-radius: 12px; overflow: hidden;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.modal-header {
  padding: 1.2rem 1.5rem; border-bottom: 1px solid #eee;
  display: flex; justify-content: space-between; align-items: center;
}
.modal-header h3 { margin: 0; color: #333; font-size: 1.2rem; }
.close-btn { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #888; display: flex; }

.modal-body { padding: 1.5rem; }
.product-preview {
  display: flex; gap: 1rem; margin-bottom: 1.5rem;
  padding-bottom: 1rem; border-bottom: 1px dashed #eee;
}
.product-preview img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; }
.preview-info .name { font-weight: 600; color: #333; margin: 0 0 0.3rem 0; }
.preview-info .variant { font-size: 0.9rem; color: #777; margin: 0; }

.rating-stars-section { margin-bottom: 1.5rem; text-align: center; }
.rating-stars-section .label { font-weight: 500; color: #555; margin-bottom: 0.8rem; }
.stars-wrapper { 
  display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 0.5rem; 
}
.star-btn { cursor: pointer; transition: transform 0.2s; }
.star-btn:hover { transform: scale(1.1); }

.rating-text { 
  display: block; margin-top: 0.5rem; color: #ffc107; font-weight: 700; font-size: 1.1rem; 
}

.rating-comment textarea {
  width: 100%; padding: 1rem; border: 1px solid #ddd;
  border-radius: 8px; resize: vertical; outline: none;
  font-family: inherit; font-size: 0.95rem;
}
.rating-comment textarea:focus { border-color: #0055aa; }

.modal-footer {
  padding: 1rem 1.5rem; border-top: 1px solid #eee;
  display: flex; justify-content: flex-end; gap: 1rem; background: #f9f9f9;
}
.fade-in { animation: fadeIn 0.2s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>