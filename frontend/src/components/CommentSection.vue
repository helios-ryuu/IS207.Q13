<template>
  <section class="bottom-card" id="reviews">
    <div class="review-header">
      <h2>Đánh giá từ khách hàng</h2>
      <div v-if="stats" class="rating-summary">
        <span class="average-score">{{ stats.average || 0 }}</span>
        <div class="stars">
          <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= Math.round(stats.average) }">★</span>
        </div>
        <span class="total-reviews">({{ reviews.length }} đánh giá)</span>
      </div>
    </div>

    <div v-if="reviews && reviews.length > 0" class="review-list">
      <div class="review-item" v-for="review in reviews" :key="review.id">
        <img :src="getAvatar(review.user.avatar)" :alt="review.user.full_name" class="reviewer-avatar" @error="handleAvatarError">
        <div class="review-content">
          <div class="review-top">
            <strong>{{ review.user.full_name }}</strong>
            <span class="review-date">{{ formatDate(review.created_at) }}</span>
          </div>
          <div class="user-rating">
             <span v-for="n in 5" :key="n" class="star-small" :class="{ filled: n <= review.rating }">★</span>
          </div>
          <p class="review-text">{{ review.content }}</p>
        </div>
      </div>
    </div>

    <div v-else class="no-reviews">
      <p>Chưa có đánh giá nào cho sản phẩm này.</p>
    </div>

    <!-- KHÔNG HIỂN THỊ FORM Ở ĐÂY - CHỈ CHO PHÉP ĐÁNH GIÁ KHI ĐÃ MUA HÀNG TRONG TRANG QUẢN LÝ ĐƠN HÀNG -->
    <div class="review-note">
      <p>* Chỉ những khách hàng đã mua sản phẩm này mới có thể viết đánh giá.</p>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  reviews: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({ average: 0, count: 0 })
  }
});

const getAvatar = (url) => {
  if (!url) return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="%23ddd"%3E%3Crect width="100%25" height="100%25"/%3E%3Ctext x="50%25" y="50%25" fill="%23888" font-size="12" text-anchor="middle" dy=".3em"%3EUser%3C/text%3E%3C/svg%3E';
  if (url.startsWith('http')) return url;
  return `http://localhost:8000${url}`;
};

const handleAvatarError = (e) => {
  if (!e.target.src.startsWith('data:')) {
     e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="%23ddd"%3E%3Crect width="100%25" height="100%25"/%3E%3Ctext x="50%25" y="50%25" fill="%23888" font-size="12" text-anchor="middle" dy=".3em"%3EUser%3C/text%3E%3C/svg%3E';
  }
};

const formatDate = (dateString) => {
  if(!dateString) return '';
  return new Date(dateString).toLocaleDateString('vi-VN');
};
</script>

<style scoped>
.bottom-card { background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
.review-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
.review-header h2 { margin: 0; font-size: 1.4rem; color: #333; }

.rating-summary { display: flex; align-items: center; gap: 10px; }
.average-score { font-size: 1.8rem; font-weight: 700; color: #ffc107; }
.stars { color: #e0e0e0; font-size: 1.2rem; }
.star.filled, .star-small.filled { color: #ffc107; }
.total-reviews { color: #777; }

.review-list { display: flex; flex-direction: column; gap: 20px; }
.review-item { display: flex; gap: 16px; border-bottom: 1px solid #f9f9f9; padding-bottom: 20px; }
.reviewer-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 1px solid #eee; }
.review-content { flex: 1; }
.review-top { display: flex; justify-content: space-between; margin-bottom: 4px; }
.review-top strong { color: #333; }
.review-date { font-size: 0.85rem; color: #999; }
.user-rating { color: #e0e0e0; font-size: 0.9rem; margin-bottom: 8px; }
.review-text { color: #444; line-height: 1.5; background: #f8f9fa; padding: 12px; border-radius: 8px; margin: 0; }

.no-reviews { text-align: center; color: #777; padding: 30px; font-style: italic; }
.review-note { margin-top: 20px; font-size: 0.9rem; color: #888; font-style: italic; text-align: center; }
</style>