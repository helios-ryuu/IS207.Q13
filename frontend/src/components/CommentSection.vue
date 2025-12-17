<template>
  <section class="bottom-card" id="comments">
    <h2>Bình luận</h2>

    <div v-if="comments && comments.length > 0" class="comment-list">

      <div class="comment-item" v-for="comment in comments" :key="comment.id">
        <img :src="comment.avatar" :alt="comment.author" class="comment-avatar">
        <div class="comment-content">
          <strong>{{ comment.author }}</strong>
          <p>{{ comment.text }}</p>
          <div class="comment-footer">
            <a href="#" class="comment-reply">Trả lời</a>
            <span class="comment-time">{{ comment.timestamp }}</span>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="no-comments">
      <p>Chưa có bình luận nào cho sản phẩm này.</p>
    </div>

    <div class="comment-form">
      <textarea placeholder="Viết bình luận của bạn..."></textarea>
      <button>Gửi bình luận</button>
    </div>
  </section>
</template>

<script setup>
defineProps({
  comments: {
    type: Array,
    default: () => []
  }
});
</script>

<style scoped>
.bottom-card {
  background-color: #ffffff;
  padding: 24px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.bottom-card h2 {
  font-size: 1.5rem;
  font-weight: bold;
  margin-top: 0;
  margin-bottom: 20px;
}

.comment-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.comment-item {
  display: flex;
  gap: 15px;
}
.comment-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #eee;
  flex-shrink: 0;
}
.comment-content {
  flex-grow: 1;
}
.comment-content strong {
  font-size: 1rem;
  display: block;
}
.comment-content p {
  margin: 5px 0;
  padding: 12px;
  background-color: #f5f5f5;
  border-radius: 8px;
}
.comment-footer {
  display: flex;
  gap: 15px;
  font-size: 0.85rem;
}
.comment-reply {
  color: #007bff;
  text-decoration: none;
  font-weight: 500;
}
.comment-time {
  color: #777;
}

.no-comments {
  text-align: center;
  color: #777;
  padding: 20px 0;
}

.comment-form {
  margin-top: 30px;
  border-top: 1px solid #f0f0f0;
  padding-top: 20px;
}
.comment-form textarea {
  width: 100%;
  min-height: 80px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  margin-bottom: 10px;
}
.comment-form button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
}
</style>