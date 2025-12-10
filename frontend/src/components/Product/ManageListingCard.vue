<template>
  <router-link
    :to="{ name: 'ProductDetail', params: { id: product.id } }"
    class="manage-card-link"
  >
    <div class="manage-card">
      <div class="card-image">
        <img :src="product.imageUrl" :alt="product.title">
        <span class="badge-priority">Tin ưu tiên</span>
        <div class="image-count">
          1 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
        </div>
      </div>

      <div class="card-info">
        <h3 class="card-title">{{ product.title }}</h3>
        <p class="card-condition">{{ product.condition }}</p>
        <p class="card-price">{{ product.price }}</p>

        <div class="card-meta">
          <span class="location">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            {{ product.location }}
          </span>
          <span class="status-badge" :class="'status-' + product.status">{{ getStatusLabel(product.status) }}</span>
        </div>

        <div class="user-row">
          <img :src="product.userAvatar" alt="User" class="user-avatar">
          <span class="user-name">{{ product.userName }}</span>
        </div>
      </div>
    </div>
  </router-link>
</template>

<script setup>
defineProps({
  product: {
    type: Object,
    required: true
  }
});

const getStatusLabel = (status) => {
  const labels = {
    'active': 'Đang hiển thị',
    'expired': 'Hết hạn',
    'rejected': 'Bị từ chối',
    'payment': 'Cần thanh toán',
    'draft': 'Tin nháp',
    'pending': 'Chờ duyệt',
    'hidden': 'Đã ẩn'
  };
  return labels[status] || status;
};
</script>

<style scoped>
.manage-card-link {
  text-decoration: none;
  color: inherit;
  display: block;
}
.manage-card {
  display: flex;
  background: #fff;
  border: 1px solid #eee;
  border-radius: 8px;
  overflow: hidden;
  padding: 12px;
  gap: 15px;
  transition: box-shadow 0.2s;
  cursor: pointer;
}

.manage-card:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Cột ảnh */
.card-image {
  position: relative;
  width: 120px;
  height: 120px;
  flex-shrink: 0;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 6px;
}

.badge-priority {
  position: absolute;
  bottom: 0;
  left: 0;
  background-color: #888; /* Màu xám như hình */
  color: white;
  font-size: 10px;
  padding: 2px 6px;
  border-top-right-radius: 4px;
  border-bottom-left-radius: 6px;
}

.image-count {
  position: absolute;
  bottom: 5px;
  right: 5px;
  background: rgba(0,0,0,0.5);
  color: white;
  font-size: 10px;
  padding: 1px 4px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  gap: 2px;
}

/* Cột thông tin */
.card-info {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.card-title {
  font-size: 16px;
  font-weight: bold;
  margin: 0 0 4px 0;
  color: #333;
}

.card-condition {
  font-size: 12px;
  color: #777;
  margin: 0 0 4px 0;
}

.card-price {
  font-size: 16px;
  font-weight: bold;
  color: #d70000;
  margin: 0 0 8px 0;
}

.card-meta {
  font-size: 12px;
  color: #777;
  margin-bottom: 8px;
}

.user-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.user-avatar {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  object-fit: cover;
}

.user-name {
  font-size: 13px;
  font-weight: 600;
  color: #333;
}
</style>