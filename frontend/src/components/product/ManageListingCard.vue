<template>
  <div class="manage-card-wrapper">
    <!-- Router link bao quanh phần nội dung chính -->
    <router-link
      :to="{ name: 'ProductDetail', params: { id: product.id } }"
      class="manage-card-link"
    >
      <div class="card-image">
        <img :src="product.imageUrl" :alt="product.title">
        <span class="badge-priority" v-if="product.isPriority">Tin ưu tiên</span>
        <div class="image-count">
          1 <font-awesome-icon icon="image" />
        </div>
      </div>

      <div class="card-info">
        <h3 class="card-title">{{ product.title }}</h3>
        <p class="card-condition">{{ product.condition }}</p>
        <p class="card-price">{{ product.price }}</p>

        <div class="card-meta">
          <span class="location">
            <font-awesome-icon icon="map-marker-alt" /> {{ product.location }}
          </span>
          <span class="date">
            <font-awesome-icon icon="clock" /> {{ formatDate(product.createdAt) }}
          </span>
        </div>
        
        <!-- Status Badge -->
        <span class="status-badge" :class="'status-' + product.status">
          {{ getStatusLabel(product.status) }}
        </span>
      </div>
    </router-link>

    <!-- ACTIONS COLUMN (Nằm ngoài router-link để tránh click nhầm) -->
    <div class="card-actions">
      <button class="btn-action btn-edit" @click.stop.prevent="$emit('edit', product.id)">
        <font-awesome-icon icon="edit" /> Sửa tin
      </button>
      
      <button class="btn-action btn-delete" @click.stop.prevent="$emit('delete', product.id)">
        <font-awesome-icon icon="trash" /> Xóa tin
      </button>
      
      <button v-if="product.status === 'hidden'" class="btn-action btn-show" @click.stop.prevent="$emit('toggle-hidden', product.id)">
        <font-awesome-icon icon="eye" /> Hiện tin
      </button>
      
      <button v-else class="btn-action btn-hide" @click.stop.prevent="$emit('toggle-hidden', product.id)">
        <font-awesome-icon icon="eye-slash" /> Ẩn tin
      </button>
    </div>
  </div>
</template>

<script setup>
import { getImageUrl } from '../../utils/imageUrl'

defineProps({
  product: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['delete', 'edit', 'toggle-hidden']);

const getStatusLabel = (status) => {
  const labels = {
    'active': 'Đang hiển thị',
    'expired': 'Hết hạn',
    'rejected': 'Bị từ chối',
    'hidden': 'Đã ẩn',
    'pending': 'Chờ duyệt'
  };
  return labels[status] || status;
};

const formatDate = (dateString) => {
  if(!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('vi-VN');
};
</script>

<style scoped>
.manage-card-wrapper {
  display: flex;
  background: #fff;
  border: 1px solid #eee;
  border-radius: 8px;
  overflow: hidden;
  padding: 12px;
  gap: 15px;
  transition: box-shadow 0.2s;
  /* position: relative; */
}

.manage-card-wrapper:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.manage-card-link {
  display: flex;
  text-decoration: none;
  color: inherit;
  flex-grow: 1;
  gap: 15px;
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
  border: 1px solid #f0f0f0;
}

.badge-priority {
  position: absolute; bottom: 0; left: 0;
  background-color: #ffd60a; color: #333;
  font-size: 10px; font-weight: bold;
  padding: 2px 6px;
  border-top-right-radius: 4px; border-bottom-left-radius: 6px;
}

.image-count {
  position: absolute; bottom: 5px; right: 5px;
  background: rgba(0,0,0,0.6); color: white;
  font-size: 10px; padding: 1px 5px; border-radius: 10px;
}

/* Cột thông tin */
.card-info {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.card-title {
  font-size: 16px; font-weight: 600; margin: 0 0 4px 0;
  color: #333;
  display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; 
  -webkit-box-orient: vertical; overflow: hidden;
}

.card-price {
  font-size: 16px; font-weight: bold; color: #d70000; margin: 4px 0;
}

.card-meta {
  display: flex; gap: 15px; font-size: 12px; color: #777;
}

.status-badge {
  display: inline-block;
  font-size: 12px; padding: 2px 8px; border-radius: 4px;
  background: #eee; color: #555; width: fit-content;
  margin-top: 5px;
}
.status-active { background: #e6fffa; color: #047857; }
.status-hidden { background: #f3f4f6; color: #6b7280; }
.status-rejected { background: #fef2f2; color: #dc2626; }

/* CỘT ACTIONS (NÚT BẤM) */
.card-actions {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 8px;
  border-left: 1px solid #eee;
  padding-left: 15px;
  min-width: 120px;
}

.btn-action {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  background: white;
  cursor: pointer;
  font-size: 13px;
  font-weight: 500;
  display: flex; align-items: center; justify-content: center; gap: 6px;
  transition: all 0.2s;
}

.btn-edit:hover { border-color: #007bff; color: #007bff; background: #f0f8ff; }
.btn-delete:hover { border-color: #dc2626; color: #dc2626; background: #fef2f2; }
.btn-hide:hover { border-color: #f59e0b; color: #f59e0b; background: #fffbeb; }

@media (max-width: 576px) {
  .manage-card-wrapper { flex-direction: column; }
  .card-actions { 
    flex-direction: row; border-left: none; padding-left: 0; padding-top: 10px; border-top: 1px solid #eee; 
  }
  .btn-action { flex: 1; }
}
</style>