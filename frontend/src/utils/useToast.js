import { ref } from 'vue'

const toastMessage = ref('')
const toastType = ref('')
const toastKey = ref(0)

export function useToast() {
  const showToast = (message, type = 'info', duration = 10000) => {
    toastMessage.value = message
    toastType.value = type
    toastKey.value++

    if (duration > 0) {
      setTimeout(() => {
        closeToast()
      }, duration)
    }
  }

  const closeToast = () => {
    toastMessage.value = ''
    toastType.value = ''
  }

  const showSuccess = (message, duration = 10000) => {
    showToast(message, 'success', duration)
  }

  const showError = (message, duration = 10000) => {
    showToast(message, 'error', duration)
  }

  const showWarning = (message, duration = 10000) => {
    showToast(message, 'warning', duration)
  }

  const showInfo = (message, duration = 10000) => {
    showToast(message, 'info', duration)
  }

  return {
    toastMessage,
    toastType,
    toastKey,
    showToast,
    closeToast,
    showSuccess,
    showError,
    showWarning,
    showInfo,
  }
}