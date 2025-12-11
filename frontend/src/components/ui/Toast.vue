<template>
  <Transition name="toast">
    <div v-if="message" :class="['toast', type]">
      <div class="toast-icon">
        <span v-if="type === 'success'">✓</span>
        <span v-else-if="type === 'error'">!</span>
        <span v-else-if="type === 'warning'">⚠</span>
        <span v-else>i</span>
      </div>
      <span class="toast-message">{{ message }}</span>
      <button class="toast-close" @click="$emit('close')">×</button>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  message: String,
  type: String
})

defineEmits(['close'])
</script>

<style scoped>
.toast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 18px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  z-index: 9999;
  max-width: 400px;
}

.toast-icon {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  flex-shrink: 0;
}

.toast.success .toast-icon {
  background: #dcfce7;
  color: #16a34a;
}

.toast.error .toast-icon {
  background: #fee2e2;
  color: #dc2626;
}

.toast.warning .toast-icon {
  background: #fef3c7;
  color: #d97706;
}

.toast.info .toast-icon {
  background: #dbeafe;
  color: #2563eb;
}

.toast-message {
  flex: 1;
  font-size: 14px;
  font-weight: 500;
  color: #1e293b;
  line-height: 1.4;
}

.toast-close {
  background: none;
  border: none;
  font-size: 20px;
  color: #94a3b8;
  cursor: pointer;
  padding: 0;
  line-height: 1;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
}

.toast-close:hover {
  background: #f1f5f9;
  color: #475569;
}

/* Smooth transition */
.toast-enter-active {
  animation: slideIn 0.3s ease-out;
}

.toast-leave-active {
  animation: slideOut 0.25s ease-in;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(100px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideOut {
  from {
    opacity: 1;
    transform: translateX(0);
  }
  to {
    opacity: 0;
    transform: translateX(100px);
  }
}
</style>