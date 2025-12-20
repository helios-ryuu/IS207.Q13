import { ref, computed } from 'vue';
import api from '../utils/api';
import { getImageUrl } from '../utils/imageUrl';

// Quản lý state giỏ hàng
const cartItems = ref([]);
const isLoading = ref(false);

// Check if user is logged in
const isLoggedIn = () => !!localStorage.getItem('app_token');

// Load giỏ hàng từ API (production) hoặc localStorage (fallback)
const loadCart = async () => {
  if (!isLoggedIn()) {
    cartItems.value = [];
    return;
  }

  isLoading.value = true;
  try {
    const response = await api.get('/cart');
    cartItems.value = response.data.data.map(item => ({
      id: item.product_id,
      variant_id: item.variant_id,
      name: item.product_name,
      price: typeof item.price === 'string' ? parseFloat(item.price.replace(/[^0-9]/g, '')) : item.price,
      image: getImageUrl(item.image),
      quantity: item.quantity,
      color: item.color,
      size: item.size,
      seller: item.seller || null
    }));
  } catch (error) {
    console.error('Error loading cart from API:', error);
    // Fallback to localStorage for development
    try {
      const saved = localStorage.getItem('cart');
      if (saved) {
        cartItems.value = JSON.parse(saved);
      }
    } catch (e) {
      console.error('Error loading cart from localStorage:', e);
    }
  } finally {
    isLoading.value = false;
  }
};

// Composable để sử dụng trong component
export const useCart = () => {
  // Khởi tạo giỏ hàng
  if (cartItems.value.length === 0 && isLoggedIn()) {
    loadCart();
  }

  // Tính tổng số sản phẩm trong giỏ
  const cartCount = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.quantity, 0);
  });

  // Tính tổng tiền
  const cartTotal = computed(() => {
    return cartItems.value.reduce((total, item) => {
      // Vì price đã là số rồi nên cứ thế nhân thôi
      return total + (Number(item.price) * item.quantity);
    }, 0);
  });

  // Thêm sản phẩm vào giỏ
  const addToCart = async (product) => {
    if (!isLoggedIn()) {
      return { success: false, message: 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!' };
    }

    try {
      await api.post('/cart/items', {
        variant_id: product.variant_id || product.id,
        quantity: 1
      });
      // Reload cart from API
      await loadCart();
      return { success: true, message: 'Đã thêm vào giỏ hàng!' };
    } catch (error) {
      console.error('Error adding to cart:', error);
      return { success: false, message: error.response?.data?.message || 'Không thể thêm vào giỏ hàng' };
    }
  };

  // Xóa sản phẩm khỏi giỏ
  const removeFromCart = async (productId) => {
    if (!isLoggedIn()) return;

    const item = cartItems.value.find(i => i.id === productId);
    if (!item) return;

    try {
      await api.delete(`/cart/items/${item.variant_id}`);
      await loadCart();
    } catch (error) {
      console.error('Error removing from cart:', error);
      // Fallback: remove locally
      const index = cartItems.value.findIndex(i => i.id === productId);
      if (index !== -1) {
        cartItems.value.splice(index, 1);
      }
    }
  };

  // Cập nhật số lượng sản phẩm - Optimistic update để tránh reload
  const updateQuantity = async (productId, quantity) => {
    if (!isLoggedIn()) return;

    const item = cartItems.value.find(i => i.id === productId);
    if (!item) return;

    if (quantity <= 0) {
      await removeFromCart(productId);
      return;
    }

    // Optimistic update: cập nhật local trước
    const oldQuantity = item.quantity;
    item.quantity = quantity;

    try {
      await api.put(`/cart/items/${item.variant_id}`, { quantity });
      // Không cần reload lại cart, đã update local rồi
    } catch (error) {
      console.error('Error updating quantity:', error);
      // Rollback nếu API fail
      item.quantity = oldQuantity;
    }
  };

  // Xóa toàn bộ giỏ hàng
  const clearCart = async () => {
    if (!isLoggedIn()) return;

    try {
      await api.delete('/cart');
      cartItems.value = [];
    } catch (error) {
      console.error('Error clearing cart:', error);
      cartItems.value = [];
    }
  };

  // Kiểm tra sản phẩm có trong giỏ không
  const isInCart = (productId) => {
    return cartItems.value.some(item => item.id === productId);
  };

  // Refresh cart từ server
  const refreshCart = () => loadCart();

  return {
    cartItems,
    cartCount,
    cartTotal,
    isLoading,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    isInCart,
    refreshCart
  };
};
