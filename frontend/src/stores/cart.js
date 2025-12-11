import { ref, computed } from 'vue';

// Quản lý state giỏ hàng
const cartItems = ref([]);

// Load giỏ hàng từ localStorage khi khởi động
const loadCart = () => {
  try {
    const saved = localStorage.getItem('cart');
    if (saved) {
      cartItems.value = JSON.parse(saved);
    }
  } catch (error) {
    console.error('Error loading cart:', error);
  }
};

// Lưu giỏ hàng vào localStorage
const saveCart = () => {
  try {
    localStorage.setItem('cart', JSON.stringify(cartItems.value));
  } catch (error) {
    console.error('Error saving cart:', error);
  }
};

// Composable để sử dụng trong component
export const useCart = () => {
  // Khởi tạo giỏ hàng từ localStorage
  if (cartItems.value.length === 0) {
    loadCart();
  }

  // Tính tổng số sản phẩm trong giỏ
  const cartCount = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.quantity, 0);
  });

  // Tính tổng tiền
  const cartTotal = computed(() => {
    return cartItems.value.reduce((total, item) => {
      const price = parseFloat(item.price.replace(/[^0-9]/g, '')) || 0;
      return total + (price * item.quantity);
    }, 0);
  });

  // Thêm sản phẩm vào giỏ
  const addToCart = (product) => {
    const existingItem = cartItems.value.find(item => item.id === product.id);
    
    if (existingItem) {
      // Nếu sản phẩm đã có trong giỏ, tăng số lượng
      existingItem.quantity += 1;
    } else {
      // Nếu chưa có, thêm mới
      cartItems.value.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.images?.[0] || product.imageUrl || '',
        seller: product.seller,
        quantity: 1
      });
    }
    
    saveCart();
    return true;
  };

  // Xóa sản phẩm khỏi giỏ
  const removeFromCart = (productId) => {
    const index = cartItems.value.findIndex(item => item.id === productId);
    if (index !== -1) {
      cartItems.value.splice(index, 1);
      saveCart();
    }
  };

  // Cập nhật số lượng sản phẩm
  const updateQuantity = (productId, quantity) => {
    const item = cartItems.value.find(item => item.id === productId);
    if (item) {
      if (quantity <= 0) {
        removeFromCart(productId);
      } else {
        item.quantity = quantity;
        saveCart();
      }
    }
  };

  // Xóa toàn bộ giỏ hàng
  const clearCart = () => {
    cartItems.value = [];
    saveCart();
  };

  // Kiểm tra sản phẩm có trong giỏ không
  const isInCart = (productId) => {
    return cartItems.value.some(item => item.id === productId);
  };

  return {
    cartItems,
    cartCount,
    cartTotal,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    isInCart
  };
};
