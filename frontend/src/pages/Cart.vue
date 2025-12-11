<template>
  <div class="cart-page">
    <Header />
    <main class="container">
      <div class="breadcrumbs">
        <span class="brand">VietMarket</span>
        <span class="separator">></span>
        <span class="current">Giỏ hàng của bạn</span>
      </div>

      <!-- Giỏ hàng trống -->
      <div v-if="cartItems.length === 0" class="empty-cart">
        <div class="empty-icon">🛒</div>
        <h2>Giỏ hàng của bạn đang trống</h2>
        <p>Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm!</p>
        <button class="btn-continue-shopping" @click="$router.push('/products')">
          Tiếp tục mua sắm
        </button>
      </div>

      <!-- Giỏ hàng có sản phẩm -->
      <div v-else class="cart-content">
        <div class="cart-items">
          <div v-for="item in cartItems" :key="item.id" class="cart-item">
            <div class="item-checkbox">
              <input 
                type="checkbox" 
                :checked="selectedItems.includes(item.id)"
                @change="toggleSelectItem(item.id)"
              />
            </div>
            
            <img :src="item.image" :alt="item.name" class="item-image" @click="goToProduct(item.id)">
            
            <div class="item-info">
              <h3 class="item-name" @click="goToProduct(item.id)">{{ item.name }}</h3>
              <div class="item-seller">
                <span>Người bán: {{ item.seller?.name || 'Shop VietMarket' }}</span>
              </div>
              <div class="item-price">{{ formatPrice(item.price) }}</div>
            </div>
            
            <div class="item-actions">
              <div class="quantity-control">
                <button @click="decreaseQuantity(item.id)" class="qty-btn">-</button>
                <input 
                  type="number" 
                  :value="item.quantity" 
                  @input="updateItemQuantity(item.id, $event.target.value)"
                  min="1"
                  class="qty-input"
                />
                <button @click="increaseQuantity(item.id)" class="qty-btn">+</button>
              </div>
              
              <div class="item-subtotal">
                {{ formatPrice(calculateItemTotal(item)) }}
              </div>
              
              <button @click="removeItem(item.id)" class="btn-remove" title="Xóa">
                <font-awesome-icon icon="trash" />
              </button>
            </div>
          </div>
        </div>

        <div class="cart-summary">
          <div class="summary-card">
            <h2>Tóm tắt đơn hàng</h2>
            
            <div class="summary-row">
              <span>Tạm tính ({{ selectedItemsCount }} sản phẩm):</span>
              <span class="amount">{{ formatPrice(selectedSubtotal) }}</span>
            </div>
            
            <div class="summary-row">
              <span>Phí vận chuyển:</span>
              <span class="amount">{{ shippingFee === 0 ? 'Miễn phí' : formatPrice(shippingFee) }}</span>
            </div>
            
            <div class="summary-divider"></div>
            
            <div class="summary-row total">
              <span>Tổng cộng:</span>
              <span class="amount total-amount">{{ formatPrice(selectedTotal) }}</span>
            </div>
            
            <button 
              class="btn-checkout" 
              :disabled="selectedItems.length === 0"
              @click="proceedToCheckout"
            >
              Tiến hành thanh toán
            </button>
            
            <button class="btn-continue-shopping-outline" @click="$router.push('/products')">
              Tiếp tục mua sắm
            </button>
          </div>
          
          <div class="promo-section">
            <h3>Mã giảm giá</h3>
            <div class="promo-input-group">
              <input 
                type="text" 
                placeholder="Nhập mã giảm giá"
                v-model="promoCode"
                class="promo-input"
              />
              <button class="btn-apply-promo" @click="applyPromo">Áp dụng</button>
            </div>
          </div>
        </div>
      </div>
    </main>
    <Footer />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCart } from '../stores/cart';
import Header from '../components/layout/SearchHeader.vue';
import Footer from '../components/layout/AppFooter.vue';

const router = useRouter();
const { cartItems, removeFromCart, updateQuantity, cartTotal } = useCart();

// State cho checkbox
const selectedItems = ref([...cartItems.value.map(item => item.id)]);

// State cho mã giảm giá
const promoCode = ref('');
const discount = ref(0);

// Toggle chọn sản phẩm
const toggleSelectItem = (itemId) => {
  const index = selectedItems.value.indexOf(itemId);
  if (index > -1) {
    selectedItems.value.splice(index, 1);
  } else {
    selectedItems.value.push(itemId);
  }
};

// Tính số lượng sản phẩm được chọn
const selectedItemsCount = computed(() => {
  return cartItems.value
    .filter(item => selectedItems.value.includes(item.id))
    .reduce((sum, item) => sum + item.quantity, 0);
});

// Tính tổng tiền sản phẩm được chọn
const selectedSubtotal = computed(() => {
  return cartItems.value
    .filter(item => selectedItems.value.includes(item.id))
    .reduce((total, item) => {
      const price = parseFloat(item.price.replace(/[^0-9]/g, '')) || 0;
      return total + (price * item.quantity);
    }, 0);
});

// Phí vận chuyển
const shippingFee = computed(() => {
  return selectedSubtotal.value > 500000 ? 0 : 30000;
});

// Tổng cộng
const selectedTotal = computed(() => {
  return selectedSubtotal.value + shippingFee.value - discount.value;
});

// Format giá tiền
const formatPrice = (price) => {
  let numPrice = price;
  if (typeof price === 'string') {
    numPrice = parseFloat(price.replace(/[^0-9]/g, '')) || 0;
  }
  return new Intl.NumberFormat('vi-VN').format(numPrice) + ' đ';
};

// Tính tổng tiền của một item
const calculateItemTotal = (item) => {
  const price = parseFloat(item.price.replace(/[^0-9]/g, '')) || 0;
  return (price * item.quantity);
};

// Điều chỉnh số lượng
const increaseQuantity = (itemId) => {
  const item = cartItems.value.find(i => i.id === itemId);
  if (item) {
    updateQuantity(itemId, item.quantity + 1);
  }
};

const decreaseQuantity = (itemId) => {
  const item = cartItems.value.find(i => i.id === itemId);
  if (item && item.quantity > 1) {
    updateQuantity(itemId, item.quantity - 1);
  }
};

const updateItemQuantity = (itemId, value) => {
  const quantity = parseInt(value) || 1;
  if (quantity > 0) {
    updateQuantity(itemId, quantity);
  }
};

// Xóa sản phẩm
const removeItem = (itemId) => {
  if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
    removeFromCart(itemId);
    // Xóa khỏi danh sách selected
    const index = selectedItems.value.indexOf(itemId);
    if (index > -1) {
      selectedItems.value.splice(index, 1);
    }
  }
};

// Áp dụng mã giảm giá
const applyPromo = () => {
  if (promoCode.value.toUpperCase() === 'WELCOME10') {
    discount.value = selectedSubtotal.value * 0.1;
    alert('Áp dụng mã giảm giá thành công! Giảm 10%');
  } else if (promoCode.value) {
    alert('Mã giảm giá không hợp lệ!');
  }
};

// Chuyển đến trang chi tiết sản phẩm
const goToProduct = (productId) => {
  router.push(`/product/${productId}`);
};

// Tiến hành thanh toán
const proceedToCheckout = () => {
  if (selectedItems.value.length === 0) {
    alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
    return;
  }
  // TODO: Implement checkout logic
  alert('Chức năng thanh toán đang được phát triển!');
};
</script>

<style scoped>
.cart-page {
  background-color: #f4f4f4;
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

.breadcrumbs .current {
  color: #333;
}

/* Empty Cart */
.empty-cart {
  text-align: center;
  background: white;
  padding: 60px 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.empty-icon {
  font-size: 5rem;
  margin-bottom: 20px;
}

.empty-cart h2 {
  font-size: 1.5rem;
  margin-bottom: 10px;
  color: #333;
}

.empty-cart p {
  color: #666;
  margin-bottom: 30px;
}

.btn-continue-shopping {
  background: #007bff;
  color: white;
  border: none;
  padding: 12px 30px;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-continue-shopping:hover {
  background: #0056b3;
}

/* Cart Content */
.cart-content {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 20px;
  align-items: start;
}

@media (max-width: 992px) {
  .cart-content {
    grid-template-columns: 1fr;
  }
}

/* Cart Items */
.cart-items {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.cart-item {
  display: grid;
  grid-template-columns: auto 120px 1fr auto;
  gap: 15px;
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  align-items: center;
}

@media (max-width: 768px) {
  .cart-item {
    grid-template-columns: auto 80px 1fr;
    gap: 10px;
  }
}

.item-checkbox input {
  width: 20px;
  height: 20px;
  cursor: pointer;
}

.item-image {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  transition: transform 0.3s;
}

.item-image:hover {
  transform: scale(1.05);
}

@media (max-width: 768px) {
  .item-image {
    width: 80px;
    height: 80px;
  }
}

.item-info {
  flex: 1;
}

.item-name {
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0 0 10px 0;
  color: #333;
  cursor: pointer;
  transition: color 0.3s;
}

.item-name:hover {
  color: #007bff;
}

.item-seller {
  font-size: 0.9rem;
  color: #666;
  margin-bottom: 10px;
}

.item-price {
  font-size: 1.2rem;
  font-weight: 700;
  color: #d70000;
}

.item-actions {
  display: flex;
  flex-direction: column;
  gap: 15px;
  align-items: flex-end;
}

@media (max-width: 768px) {
  .item-actions {
    grid-column: 2 / -1;
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
  }
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 5px;
  border: 1px solid #ddd;
  border-radius: 6px;
  overflow: hidden;
}

.qty-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: #f0f0f0;
  cursor: pointer;
  font-size: 1.2rem;
  transition: background 0.3s;
}

.qty-btn:hover {
  background: #e0e0e0;
}

.qty-input {
  width: 50px;
  height: 32px;
  text-align: center;
  border: none;
  font-size: 1rem;
  font-weight: 600;
}

.qty-input::-webkit-inner-spin-button,
.qty-input::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.item-subtotal {
  font-size: 1.2rem;
  font-weight: 700;
  color: #d70000;
  min-width: 120px;
  text-align: right;
}

.btn-remove {
  background: #ff4444;
  color: white;
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-remove:hover {
  background: #cc0000;
}

/* Cart Summary */
.cart-summary {
  position: sticky;
  top: 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
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

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 15px;
  font-size: 1rem;
  color: #555;
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

.summary-divider {
  height: 1px;
  background: #e0e0e0;
  margin: 15px 0;
}

.btn-checkout {
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

.btn-checkout:hover:not(:disabled) {
  background: #e55a00;
}

.btn-checkout:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.btn-continue-shopping-outline {
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

.btn-continue-shopping-outline:hover {
  background: #007bff;
  color: white;
}

/* Promo Section */
.promo-section {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.promo-section h3 {
  font-size: 1.1rem;
  margin: 0 0 15px 0;
  color: #333;
}

.promo-input-group {
  display: flex;
  gap: 10px;
}

.promo-input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 0.95rem;
}

.btn-apply-promo {
  background: #28a745;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-apply-promo:hover {
  background: #218838;
}
</style>
