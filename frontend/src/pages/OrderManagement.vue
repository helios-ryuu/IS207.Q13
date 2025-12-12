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
              <div 
                v-for="cat in categories" 
                :key="cat" 
                class="dropdown-item"
                :class="{ 'active-item': selectedCategories.includes(cat) }"
                @click="toggleCategory(cat)"
              >
                <span>{{ cat }}</span>
                <font-awesome-icon v-if="selectedCategories.includes(cat)" icon="check" class="check-icon" />
              </div>
            </div>
          </div>

          <div class="tags-container">
            <div v-for="cat in selectedCategories" :key="cat" class="selected-tag">
              {{ cat }}
              <span class="remove-tag" @click="removeCategory(cat)">
                <font-awesome-icon icon="times" />
              </span>
            </div>
            <button 
              v-if="selectedCategories.length > 0" 
              class="clear-all-btn"
              @click="clearAllCategories"
            >
              Xóa hết
            </button>
          </div>

        </div>
      </div>

      <div class="status-tabs">
        <button 
          v-for="tab in tabs" 
          :key="tab.id" 
          class="tab-item" 
          :class="{ active: activeTab === tab.id }"
          @click="changeTab(tab.id)"
        >
          {{ tab.name }}
        </button>
      </div>

      <div class="order-list">
        
        <div v-for="order in visibleOrders" :key="order.id" class="order-card">
          
          <div class="card-header">
            <div class="shop-info">
              <span class="shop-name">{{ order.shopName }}</span>
              <button class="btn-chat"><font-awesome-icon icon="comments" /> Chat</button>
              <button class="btn-view-shop"><font-awesome-icon icon="store" /> Xem Shop</button>
            </div>
            <div class="order-status">
              <span class="delivery-status">
                <font-awesome-icon icon="truck" /> {{ order.deliveryStatus }}
              </span>
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
              <template v-if="order.statusId === 'pending'">
                <button class="btn btn-danger" @click="cancelOrder(order.id)" style="border:1px solid #dc3545; color:#dc3545; margin-right:8px;">Hủy Đơn</button>
                <button class="btn btn-default">Liên hệ người bán</button>
              </template>

              <template v-else-if="order.statusId === 'completed'">
                <button class="btn btn-primary">Đánh Giá</button>
                <button class="btn btn-default">Mua Lại</button>
              </template>

              <template v-else-if="order.statusId === 'return'">
                <button class="btn btn-default">Chi tiết hoàn tiền</button>
              </template>

              <template v-else>
                <button class="btn btn-default">Liên hệ người bán</button>
              </template>
            </div>
          </div>
        </div>

        <div v-if="hasMoreOrders" class="load-more-container">
          <button class="btn-load-more" @click="loadMore">
            Xem thêm đơn hàng <font-awesome-icon icon="chevron-down" />
          </button>
        </div>

        <div v-if="visibleOrders.length === 0 && !isLoading" class="empty-result">
          <p>Không tìm thấy đơn hàng nào phù hợp.</p>
        </div>

        <div v-if="isLoading" class="empty-result">
          <p>Đang tải dữ liệu...</p>
        </div>

      </div>
    </div>
  </div>

  <Footer />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import HeaderOther from '../components/layout/SearchHeader.vue';
import Footer from '../components/layout/AppFooter.vue';
import api from '../utils/api';
import { getImageUrl } from '../utils/imageUrl';

const router = useRouter();
const goToHome = () => router.push('/');

// --- DATA ---
const orders = ref([]); 
const isLoading = ref(false);
const activeTab = ref('all');
const isDropdownOpen = ref(false);
const selectedCategories = ref([]); 
const visibleCount = ref(5);

const tabs = [
  { id: 'all', name: 'Tất cả' },
  { id: 'pending', name: 'Chờ xác nhận' },
  { id: 'shipping', name: 'Vận chuyển' },
  { id: 'completed', name: 'Hoàn thành' },
  { id: 'cancelled', name: 'Đã hủy' }
];

const categories = [
  'Xe cộ', 'Đồ điện tử', 'Thú cưng', 'Thời trang', 'Đồ gia dụng'
];

// --- API CALLS ---
// 1. Lấy danh sách đơn hàng
const fetchOrders = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('/orders');
    console.log("Dữ liệu API:", response.data);

    // Xử lý "Búp bê Nga" (lấy mảng dữ liệu thực)
    const ordersData = Array.isArray(response.data.data) 
      ? response.data.data 
      : (response.data.data?.data || []); 

    // Map dữ liệu
    orders.value = ordersData.map(order => {
      // SỬA LỖI 1: Dùng 'items' thay vì 'order_details'
      // Kiểm tra kỹ xem backend trả về key nào (dựa theo ảnh của bạn là 'items')
      const firstItem = (order.items && order.items.length > 0) ? order.items[0] : {}; 
      
      // SỬA LỖI 2: Xử lý giá tiền
      // Nếu backend trả về chuỗi "3,500,000 VND" thì dùng luôn, không format lại
      let displayPrice = order.total_amount;
      if (typeof displayPrice === 'number') {
          displayPrice = formatPrice(displayPrice);
      }

      // SỬA LỖI 3: Xử lý tên sản phẩm bị null
      // Nếu không có tên SP, dùng tên biến thể (variant) hoặc chữ "Sản phẩm"
      const displayName = firstItem.product_name || firstItem.variant || 'Sản phẩm đơn hàng';

      return {
        id: order.id,
        shopName: 'VietMarket Shop', 
        productName: displayName,
        variant: firstItem.variant || '',
        category: 'Tổng hợp',
        
        statusId: mapBackendStatus(order.status),
        statusLabel: getStatusLabel(order.status),
        deliveryStatus: getDeliveryStatusText(order.status),
        
        price: displayPrice,      // Dùng giá đã xử lý ở trên
        totalPrice: displayPrice, // Dùng giá đã xử lý ở trên
        
        // Lấy ảnh (ưu tiên ảnh variant -> ảnh mặc định)
        image: getImageUrl(firstItem.image || 'default.jpg'),
        sellerAvatar: '/avatar.jpg'
      };
    });
  } catch (error) {
    console.error("Lỗi tải đơn hàng:", error);
  } finally {
    isLoading.value = false;
  }
};

const cancelOrder = async (orderId) => {
  if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) return;
  
  try {
    await api.post(`/orders/${orderId}/cancel`);
    alert('Đã hủy đơn hàng thành công');
    await fetchOrders(); 
  } catch (error) {
    console.error("Lỗi hủy đơn:", error);
    alert(error.response?.data?.message || 'Không thể hủy đơn hàng này');
  }
};

// --- HELPER FUNCTIONS ---
const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const mapBackendStatus = (status) => {
  switch(status) {
    case 'pending': return 'pending';
    case 'confirmed': 
    case 'processing':
    case 'shipped': return 'shipping';
    case 'delivered': return 'completed';
    case 'cancelled': return 'cancelled';
    case 'refunded': return 'return';
    default: return 'all';
  }
};

const getStatusLabel = (status) => {
  const map = {
    pending: 'CHỜ XÁC NHẬN',
    confirmed: 'ĐÃ XÁC NHẬN',
    processing: 'ĐANG XỬ LÝ',
    shipped: 'ĐANG GIAO',
    delivered: 'HOÀN THÀNH',
    cancelled: 'ĐÃ HỦY',
    refunded: 'ĐÃ HOÀN TIỀN'
  };
  return map[status] || status;
};

const getDeliveryStatusText = (status) => {
  const map = {
    pending: 'Người bán đang xác nhận',
    confirmed: 'Đơn hàng đã được xác nhận',
    processing: 'Người bán đang chuẩn bị hàng',
    shipped: 'Shipper đang giao hàng đến bạn',
    delivered: 'Giao hàng thành công',
    cancelled: 'Đơn hàng đã bị hủy',
    refunded: 'Đã hoàn tiền thành công'
  };
  return map[status] || '';
};

// --- FILTER & PAGINATION LOGIC ---
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
const clearAllCategories = () => {
  selectedCategories.value = [];
  resetPagination();
};

onMounted(() => {
  fetchOrders();
});
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
</style>