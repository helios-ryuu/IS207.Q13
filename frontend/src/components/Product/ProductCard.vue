<template>
  <div class="product-card">
    <button class="favorite-btn">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="fill: white; stroke: #888;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
    </button>
    
    <span class="tag">Đã nhận</span>

    <div class="product-image-wrapper">
      <img src="https://via.placeholder.com/200" :alt="product.title" class="product-image">
    </div>

    <div class="product-info">
      <h3 class="product-title">{{ product.title }}</h3>
      <div class="price-section">
        <span class="current-price">{{ product.price }}</span>
        <span class="original-price">{{ product.originalPrice }}</span>
      </div>
      <div class="seller-info">
        <div class="avatar-placeholder"></div>
        <span class="seller-name">{{ product.seller }}</span>
      </div>
      <span class="location">{{ product.location }}</span>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

defineProps({
  product: {
    type: Object,
    required: true
  }
});
</script>

<style scoped>
/* CSS cho ProductCard.vue */
.product-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  position: relative;
  transition: transform 0.2s;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.favorite-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: white;
  border: none;
  padding: 5px;
  border-radius: 50%;
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
  z-index: 10;
}

.tag {
  position: absolute;
  top: 10px;
  left: 0;
  background-color: #ff5722; /* Màu cam đậm */
  color: white;
  padding: 4px 8px;
  font-size: 12px;
  border-radius: 0 4px 4px 0;
  z-index: 10;
}

.product-image-wrapper {
  overflow: hidden;
  height: 200px; /* Chiều cao cố định cho ảnh */
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info {
  padding: 10px;
}

.product-title {
  font-size: 16px;
  font-weight: 500;
  color: #333;
  margin-bottom: 5px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.price-section {
  margin-bottom: 5px;
}

.current-price {
  font-size: 18px;
  font-weight: bold;
  color: #ff4500; /* Màu giá tiền */
  margin-right: 8px;
}

.original-price {
  font-size: 14px;
  color: #888;
  text-decoration: line-through;
}

.seller-info {
  display: flex;
  align-items: center;
  margin-bottom: 5px;
}

.avatar-placeholder {
  width: 24px;
  height: 24px;
  background-color: #e9e9e9;
  border-radius: 50%;
  margin-right: 8px;
}

.seller-name, .location {
  font-size: 13px;
  color: #666;
}
</style>