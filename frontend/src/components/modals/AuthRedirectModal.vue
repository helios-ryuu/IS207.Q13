<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      
      <header class="modal-header">
        <h3>Yêu cầu đăng nhập</h3>
        <button class="close-btn" @click="$emit('close')">
          <font-awesome-icon icon="times" />
        </button>
      </header>
      
      <div class="modal-body">
        <p>Bạn cần đăng nhập để sử dụng chức năng này.</p>
        <div class="button-group">
          <button class="auth-action-btn register-btn" @click="goToRegister">Đăng ký ngay</button>
          <button class="auth-action-btn login-btn" @click="goToLogin">Đăng nhập ngay</button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
const emit = defineEmits(['close']);
const router = useRouter();

// Chuyển đến trang /login và đóng modal
const goToLogin = () => {
  router.push('/login');
  emit('close');
};

// Chuyển đến trang /register và đóng modal
const goToRegister = () => {
  router.push('/register');
  emit('close');
};
</script>

<style scoped>
/* (Sử dụng CSS tương tự LoginModal) */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9998;
}
.modal-content {
  width: 90%;
  max-width: 400px;
  background: white;
  border-radius: 12px;
  overflow: hidden;
}
.modal-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
}
.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  text-align: center;
  flex-grow: 1;
}
.close-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: #888;
  position: absolute;
  right: 1.5rem;
}

/* Body */
.modal-body {
  padding: 1.5rem;
  text-align: center;
}
.modal-body p {
  font-size: 1rem;
  color: #333;
  margin-bottom: 1.5rem;
}
.button-group {
  display: flex;
  gap: 1rem;
  justify-content: center;
}
.auth-action-btn {
  padding: 0.75rem 1rem;
  font-size: 0.9rem;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  border: none;
  flex: 1;
}
.login-btn {
  background-color: #f5a623;
  color: black;
  border: 1px solid #f5a623;
}
.register-btn {
  background-color: white;
  color: #333;
  border: 1px solid #ccc;
}
</style>