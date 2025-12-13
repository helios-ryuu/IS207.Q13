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
        <!-- Left Column: Shipping & Payment Info -->
        <div class="checkout-form">
          <!-- Shipping Address -->
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

          <!-- Payment Method -->
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

        <!-- Right Column: Order Summary -->
        <div class="order-summary-section">
          <div class="summary-card">
            <h2>Đơn hàng của bạn</h2>
            
            <div class="order-items">
              <div v-for="item in checkoutItems" :key="item.id" class="order-item">
                <img :src="getImageUrl(item.image)" :alt="item.name" class="order-item-image" />
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
              :disabled="!isFormValid || isProcessing"
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
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useCart } from '../stores/cart';
import api from '../utils/api';
import { getImageUrl } from '../utils/imageUrl';
import Header from '../components/layout/SearchHeader.vue';
import Footer from '../components/layout/AppFooter.vue';

const router = useRouter();
const route = useRoute();
const { cartItems, clearCart } = useCart();

// Get selected items from cart
const selectedItemIds = ref([]);
const checkoutItems = ref([]);
const isProcessing = ref(false);

// Shipping Info
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

// Payment Method
const paymentMethod = ref('cod');

// Load checkout items
onMounted(() => {
  // Get selected items from query params or use all items
  const selectedIds = route.query.items ? route.query.items.split(',').map(Number) : cartItems.value.map(item => item.id);
  selectedItemIds.value = selectedIds;
  
  checkoutItems.value = cartItems.value.filter(item => selectedIds.includes(item.id));
  
  // If no items to checkout, redirect to cart
  if (checkoutItems.value.length === 0) {
    router.push('/cart');
  }
});

// Computed properties
const totalQuantity = computed(() => {
  return checkoutItems.value.reduce((sum, item) => sum + item.quantity, 0);
});

const subtotal = computed(() => {
  return checkoutItems.value.reduce((total, item) => {
    const price = parseFloat(item.price.replace(/[^0-9]/g, '')) || 0;
    return total + (price * item.quantity);
  }, 0);
});

const shippingFee = computed(() => {
  return subtotal.value > 500000 ? 0 : 30000;
});

const discount = ref(0);

const total = computed(() => {
  return subtotal.value + shippingFee.value - discount.value;
});

const isFormValid = computed(() => {
  return shippingInfo.value.fullName && 
         shippingInfo.value.phone && 
         shippingInfo.value.address &&
         shippingInfo.value.province &&
         shippingInfo.value.district &&
         shippingInfo.value.ward &&
         paymentMethod.value;
});

// Format price
const formatPrice = (price) => {
  let numPrice = price;
  if (typeof price === 'string') {
    numPrice = parseFloat(price.replace(/[^0-9]/g, '')) || 0;
  }
  return new Intl.NumberFormat('vi-VN').format(numPrice) + ' đ';
};

// Calculate item total
const calculateItemTotal = (item) => {
  const price = typeof item.price === 'string'
    ? parseFloat(item.price.replace(/[^0-9]/g, '')) || 0
    : item.price || 0;
  return price * item.quantity;
};

// Handle place order
const handlePlaceOrder = async () => {
  if (!isFormValid.value) {
    alert('Vui lòng điền đầy đủ thông tin giao hàng!');
    return;
  }

  isProcessing.value = true;

  try {
    // Step 1: Create shipping address
    const addressData = {
      receiver_name: shippingInfo.value.fullName,
      phone_number: shippingInfo.value.phone,
      street_address: shippingInfo.value.address,
      ward: shippingInfo.value.ward,
      district: shippingInfo.value.district,
      province: shippingInfo.value.province,
      is_default: false
    };

    console.log('Creating shipping address:', addressData);
    await api.post('/addresses', addressData);
    
    // Get the newly created address (it will be the most recent one)
    const addressesResponse = await api.get('/addresses');
    console.log('Addresses response:', addressesResponse.data);
    const addresses = addressesResponse.data.data;
    const addressId = addresses[0]?.id; // Get the first address (most recent)
    
    if (!addressId) {
      throw new Error('Không thể lấy địa chỉ giao hàng');
    }

    // Step 2: Map payment method to backend format
    const paymentMethodMap = {
      'cod': 'cash',
      'transfer': 'bank_transfer',
      'card': 'credit_card'
    };

    // Step 3: Create order (backend will use cart from database)
    const orderData = {
      address_id: addressId,
      payment_method: paymentMethodMap[paymentMethod.value] || 'cash',
      notes: shippingInfo.value.note || null
    };

    const orderResponse = await api.post('/orders', orderData);
    
    console.log('Order created:', orderResponse.data);

    // Cart is automatically cleared by backend after order creation

    // Show success message
    alert('Đặt hàng thành công! Đơn hàng của bạn đang chờ xác nhận.');

    // Redirect to purchase orders page
    router.push('/purchase-orders');
  } catch (error) {
    console.error('Error placing order:', error);
    console.error('Error response:', error.response);
    console.error('Error data:', error.response?.data);
    const errorMessage = error.response?.data?.message || error.message || 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại!';
    alert(`Lỗi: ${errorMessage}`);
  } finally {
    isProcessing.value = false;
  }
};
</script>

<style scoped>
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
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 6px;
}

.order-item-info {
  flex: 1;
}

.order-item-info h4 {
  margin: 0 0 5px 0;
  font-size: 1rem;
  color: #333;
}

.order-item-info p {
  margin: 0 0 10px 0;
  font-size: 0.9rem;
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
