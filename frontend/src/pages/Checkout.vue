<template>
  <div class="checkout-page">
    <Header />
    <main class="container">
      <div class="breadcrumbs">
        <span class="brand">VietMarket</span>
        <span class="separator">></span>
        <span class="link" @click="$router.push('/cart')">Giỏ hàng</span>
        <span class="separator">></span>
        <span class="current">Thanh toán</span>
      </div>

      <div class="checkout-content">
        <div class="checkout-form">
          <div class="form-section">
            <h2>Địa chỉ giao hàng</h2>
            <div class="form-group">
              <label>Họ và tên <span class="required">*</span></label>
              <input 
                v-model="shippingInfo.fullName" 
                type="text" 
                placeholder="Nhập họ và tên"
                class="form-control"
              />
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label>Số điện thoại <span class="required">*</span></label>
                <input 
                  v-model="shippingInfo.phone" 
                  type="tel" 
                  placeholder="Nhập số điện thoại"
                  class="form-control"
                />
              </div>
              
              <div class="form-group">
                <label>Email</label>
                <input 
                  v-model="shippingInfo.email" 
                  type="email" 
                  placeholder="Nhập email"
                  class="form-control"
                />
              </div>
            </div>

            <div class="form-group">
              <label>Địa chỉ <span class="required">*</span></label>
              <input 
                v-model="shippingInfo.address" 
                type="text" 
                placeholder="Số nhà, tên đường"
                class="form-control"
              />
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Tỉnh/Thành phố <span class="required">*</span></label>
                <select v-model="shippingInfo.province" class="form-control">
                  <option value="">Chọn Tỉnh/Thành phố</option>
                  <option value="Hồ Chí Minh">TP. Hồ Chí Minh</option>
                  <option value="Hà Nội">Hà Nội</option>
                  <option value="Đà Nẵng">Đà Nẵng</option>
                  <option value="Cần Thơ">Cần Thơ</option>
                </select>
              </div>

              <div class="form-group">
                <label>Quận/Huyện <span class="required">*</span></label>
                <select v-model="shippingInfo.district" class="form-control">
                  <option value="">Chọn Quận/Huyện</option>
                  <option value="Quận 1">Quận 1</option>
                  <option value="Quận 2">Quận 2</option>
                  <option value="Quận 3">Quận 3</option>
                </select>
              </div>

              <div class="form-group">
                <label>Phường/Xã <span class="required">*</span></label>
                <select v-model="shippingInfo.ward" class="form-control">
                  <option value="">Chọn Phường/Xã</option>
                  <option value="Phường Bến Nghé">Phường Bến Nghé</option>
                  <option value="Phường Bến Thành">Phường Bến Thành</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label>Ghi chú đơn hàng</label>
              <textarea 
                v-model="shippingInfo.note" 
                placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn"
                class="form-control"
                rows="3"
              ></textarea>
            </div>
          </div>

          <div class="form-section">
            <h2>Phương thức thanh toán</h2>
            <div class="payment-methods">
              <label class="payment-option">
                <input 
                  type="radio" 
                  v-model="paymentMethod" 
                  value="cod" 
                  name="payment"
                />
                <div class="payment-content">
                  <div class="payment-icon">💵</div>
                  <div class="payment-details">
                    <strong>Thanh toán khi nhận hàng (COD)</strong>
                    <p>Thanh toán bằng tiền mặt khi nhận hàng</p>
                  </div>
                </div>
              </label>

              <label class="payment-option">
                <input 
                  type="radio" 
                  v-model="paymentMethod" 
                  value="transfer" 
                  name="payment"
                />
                <div class="payment-content">
                  <div class="payment-icon">🏦</div>
                  <div class="payment-details">
                    <strong>Chuyển khoản ngân hàng</strong>
                    <p>Chuyển khoản qua ngân hàng hoặc ví điện tử</p>
                  </div>
                </div>
              </label>

              <label class="payment-option">
                <input 
                  type="radio" 
                  v-model="paymentMethod" 
                  value="card" 
                  name="payment"
                />
                <div class="payment-content">
                  <div class="payment-icon">💳</div>
                  <div class="payment-details">
                    <strong>Thẻ tín dụng/Ghi nợ</strong>
                    <p>Visa, Mastercard, JCB</p>
                  </div>
                </div>
              </label>
            </div>
          </div>
        </div>

        <div class="order-summary-section">
          <div class="summary-card">
            <h2>Đơn hàng của bạn</h2>
            
            <div class="order-items">
              <div v-if="checkoutItems.length === 0" class="empty-msg">Đang tải sản phẩm...</div>
              <div v-else v-for="item in checkoutItems" :key="item.id" class="order-item">
                <img :src="getImageUrl(item.image)" :alt="item.name" class="order-item-image" @error="handleImageError" />
                <div class="order-item-info">
                  <h4>{{ item.name }}</h4>
                  <p>{{ item.seller?.name || 'Shop VietMarket' }}</p>
                  <div class="order-item-price">
                    <span class="quantity">x{{ item.quantity }}</span>
                    <span class="price">{{ formatPrice(calculateItemTotal(item)) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row">
              <span>Tạm tính ({{ totalQuantity }} sản phẩm):</span>
              <span class="amount">{{ formatPrice(subtotal) }}</span>
            </div>

            <div class="summary-row">
              <span>Phí vận chuyển:</span>
              <span class="amount">{{ shippingFee === 0 ? 'Miễn phí' : formatPrice(shippingFee) }}</span>
            </div>

            <div class="summary-row discount" v-if="discount > 0">
              <span>Giảm giá:</span>
              <span class="amount">-{{ formatPrice(discount) }}</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row total">
              <span>Tổng cộng:</span>
              <span class="amount total-amount">{{ formatPrice(total) }}</span>
            </div>

            <button 
              class="btn-place-order" 
              @click="handlePlaceOrder"
              :disabled="!isFormValid || isProcessing || checkoutItems.length === 0"
            >
              <span v-if="isProcessing">Đang xử lý...</span>
              <span v-else>Đặt hàng</span>
            </button>

            <button class="btn-back-to-cart" @click="$router.push('/cart')">
              Quay lại giỏ hàng
            </button>
          </div>
        </div>
      </div>
    </main>
    <Footer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useCart } from '../stores/cart'; // Đảm bảo đường dẫn đúng
import api from '../utils/api';
import { getImageUrl } from '../utils/imageUrl';
import { useToast } from '../utils/useToast';
import Header from '../components/layout/SearchHeader.vue';
import Footer from '../components/layout/AppFooter.vue';

const router = useRouter();
const route = useRoute();
const { cartItems, refreshCart, clearCart } = useCart();
const { showSuccess, showError } = useToast();

const isProcessing = ref(false);

// Shipping Info Form
const shippingInfo = ref({
  fullName: '',
  phone: '',
  email: '',
  address: '',
  province: '',
  district: '',
  ward: '',
  note: ''
});

const paymentMethod = ref('cod');

// === 1. LOGIC LẤY SẢN PHẨM CHECKOUT ===

// Refresh cart khi vào trang để đảm bảo dữ liệu mới nhất
onMounted(() => {
  refreshCart();
});

// Computed để lọc sản phẩm dựa trên URL param ?items=1,2,3
const checkoutItems = computed(() => {
  if (!cartItems.value || cartItems.value.length === 0) return [];

  // Nếu có param items trên URL
  if (route.query.items) {
    // Chuyển chuỗi "1,2,3" thành mảng số [1, 2, 3]
    const selectedIds = route.query.items.split(',').map(id => parseInt(id));
    
    // Lọc cartItems có id nằm trong danh sách đã chọn
    return cartItems.value.filter(item => selectedIds.includes(item.id));
  }
  
  // Nếu không có param items, trả về rỗng (hoặc toàn bộ cart tùy logic bạn muốn)
  return []; 
});

// Theo dõi checkoutItems, nếu rỗng sau khi tải xong thì báo lỗi/redirect
watch(checkoutItems, (newItems) => {
  if (cartItems.value.length > 0 && newItems.length === 0) {
    // alert('Không tìm thấy sản phẩm thanh toán. Vui lòng chọn lại từ giỏ hàng.');
    // router.push('/cart');
  }
});


// === 2. LOGIC TÍNH TIỀN (QUAN TRỌNG) ===

// Helper: Chuyển đổi giá an toàn (Xử lý cả số và chuỗi)
const parsePrice = (price) => {
  if (typeof price === 'number') return price;
  if (typeof price === 'string') {
    // Xóa tất cả ký tự không phải số
    return parseFloat(price.replace(/[^0-9]/g, '')) || 0;
  }
  return 0;
};

// Helper: Format hiển thị tiền
const formatPrice = (price) => {
  const num = typeof price === 'number' ? price : parsePrice(price);
  return new Intl.NumberFormat('vi-VN').format(num) + ' đ';
};

// Tổng số lượng
const totalQuantity = computed(() => {
  return checkoutItems.value.reduce((sum, item) => sum + item.quantity, 0);
});

// Tổng tiền hàng
const subtotal = computed(() => {
  return checkoutItems.value.reduce((total, item) => {
    return total + (parsePrice(item.price) * item.quantity);
  }, 0);
});

// Phí vận chuyển
const shippingFee = computed(() => {
  if (subtotal.value === 0) return 0;
  return subtotal.value > 500000 ? 0 : 30000;
});

const discount = ref(0);

// Tổng thanh toán cuối cùng
const total = computed(() => {
  return subtotal.value + shippingFee.value - discount.value;
});

// Tính tiền từng món (để hiển thị)
const calculateItemTotal = (item) => {
  return parsePrice(item.price) * item.quantity;
};

const handleImageError = (e) => {
  e.target.src = "https://via.placeholder.com/80?text=No+Img";
};

// Validate form
const isFormValid = computed(() => {
  return shippingInfo.value.fullName && 
         shippingInfo.value.phone && 
         shippingInfo.value.address &&
         shippingInfo.value.province &&
         shippingInfo.value.district &&
         shippingInfo.value.ward &&
         paymentMethod.value;
});


// === 3. XỬ LÝ ĐẶT HÀNG (GỌI API) ===
const handlePlaceOrder = async () => {
  if (!isFormValid.value) {
    showError('Vui lòng điền đầy đủ thông tin giao hàng!');
    return;
  }

  isProcessing.value = true;

  try {
    const paymentMethodMap = {
      'cod': 'cash',
      'transfer': 'bank_transfer',
      'card': 'credit_card'
    };

    // Chuẩn bị payload gửi lên server
    const orderPayload = {
      receiver_name: shippingInfo.value.fullName,
      phone_number: shippingInfo.value.phone,
      street_address: shippingInfo.value.address,
      province: shippingInfo.value.province,
      district: shippingInfo.value.district,
      ward: shippingInfo.value.ward,
      payment_method: paymentMethodMap[paymentMethod.value] || 'cash',
      notes: shippingInfo.value.note || '',
      
      // Quan trọng: Gửi danh sách sản phẩm để Backend biết đơn hàng gồm gì
      // (Tùy backend của bạn có yêu cầu field này không, nhưng thường là có nếu API tạo đơn độc lập với Cart)
      items: checkoutItems.value.map(item => ({
          product_id: item.id,
          quantity: item.quantity,
          price: parsePrice(item.price),
          variant_id: item.variant_id // Nếu có variant
      }))
    };

    console.log('Sending Order:', orderPayload);

    // Gọi API
    const response = await api.post('/orders', orderPayload);
    
    // Nếu thành công
    showSuccess('Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.');
    
    // Xóa giỏ hàng (Refresh lại từ server để đồng bộ)
    await refreshCart(); 

    // Chuyển hướng
    router.push('/purchase-orders');

  } catch (error) {
    console.error('Lỗi đặt hàng:', error);
    const msg = error.response?.data?.message || 'Có lỗi xảy ra, vui lòng thử lại.';
    showError(msg);
  } finally {
    isProcessing.value = false;
  }
};
</script>

<style scoped>
/* CSS giữ nguyên như cũ */
.checkout-page {
  background-color: #f8f9fa;
  min-height: 100vh;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px 15px;
}

.breadcrumbs {
  font-size: 14px;
  color: #007bff;
  margin-bottom: 20px;
  font-weight: 600;
}

.breadcrumbs .separator {
  margin: 0 5px;
  color: #777;
}

.breadcrumbs .link {
  cursor: pointer;
  transition: color 0.3s;
}

.breadcrumbs .link:hover {
  color: #0056b3;
  text-decoration: underline;
}

.breadcrumbs .current {
  color: #333;
}

.checkout-content {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 20px;
  align-items: start;
}

@media (max-width: 992px) {
  .checkout-content {
    grid-template-columns: 1fr;
  }
}

/* Form Sections */
.checkout-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-section {
  background: white;
  padding: 24px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.form-section h2 {
  font-size: 1.5rem;
  margin: 0 0 20px 0;
  color: #333;
}

.form-group {
  margin-bottom: 20px;
  flex: 1;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

.required {
  color: #ff4444;
}

.form-control {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-control:focus {
  outline: none;
  border-color: #007bff;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

textarea.form-control {
  resize: vertical;
  font-family: inherit;
}

/* Payment Methods */
.payment-methods {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.payment-option {
  display: flex;
  align-items: flex-start;
  padding: 15px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
}

.payment-option:hover {
  border-color: #007bff;
  background: #f8f9ff;
}

.payment-option input[type="radio"] {
  margin-top: 4px;
  margin-right: 15px;
  width: 20px;
  height: 20px;
  cursor: pointer;
}

.payment-option input[type="radio"]:checked + .payment-content {
  color: #007bff;
}

.payment-content {
  display: flex;
  gap: 15px;
  flex: 1;
}

.payment-icon {
  font-size: 2rem;
  line-height: 1;
}

.payment-details {
  flex: 1;
}

.payment-details strong {
  display: block;
  margin-bottom: 5px;
  font-size: 1rem;
}

.payment-details p {
  margin: 0;
  font-size: 0.9rem;
  color: #666;
}

/* Order Summary */
.order-summary-section {
  position: sticky;
  top: 20px;
}

.summary-card {
  background: white;
  padding: 24px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.summary-card h2 {
  font-size: 1.5rem;
  margin: 0 0 20px 0;
  color: #333;
}

.order-items {
  max-height: 300px;
  overflow-y: auto;
  margin-bottom: 20px;
}

.empty-msg {
  text-align: center;
  color: #888;
  font-style: italic;
  padding: 20px;
}

.order-item {
  display: flex;
  gap: 15px;
  padding: 15px 0;
  border-bottom: 1px solid #f0f0f0;
}

.order-item:last-child {
  border-bottom: none;
}

.order-item-image {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid #eee;
}

.order-item-info {
  flex: 1;
}

.order-item-info h4 {
  margin: 0 0 5px 0;
  font-size: 0.95rem;
  color: #333;
  line-height: 1.3;
}

.order-item-info p {
  margin: 0 0 8px 0;
  font-size: 0.85rem;
  color: #666;
}

.order-item-price {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.order-item-price .quantity {
  color: #666;
  font-size: 0.9rem;
  background: #f0f0f0;
  padding: 2px 8px;
  border-radius: 4px;
}

.order-item-price .price {
  font-weight: 700;
  color: #d70000;
}

.summary-divider {
  height: 1px;
  background: #e0e0e0;
  margin: 15px 0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 1rem;
  color: #555;
}

.summary-row.discount {
  color: #28a745;
}

.summary-row.total {
  font-size: 1.2rem;
  font-weight: 700;
  color: #333;
  margin-top: 10px;
}

.amount {
  font-weight: 600;
  color: #333;
}

.total-amount {
  font-size: 1.5rem;
  color: #d70000;
}

.btn-place-order {
  width: 100%;
  background: #ff6600;
  color: white;
  border: none;
  padding: 15px;
  border-radius: 6px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.3s;
  margin-top: 20px;
}

.btn-place-order:hover:not(:disabled) {
  background: #e55a00;
}

.btn-place-order:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.btn-back-to-cart {
  width: 100%;
  background: white;
  color: #007bff;
  border: 2px solid #007bff;
  padding: 12px;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 10px;
}

.btn-back-to-cart:hover {
  background: #007bff;
  color: white;
}
</style>