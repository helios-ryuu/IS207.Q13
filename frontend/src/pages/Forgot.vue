<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const email = ref('')
const error = ref('')
const loading = ref(false)

function submit(e){
  e?.preventDefault()
  error.value = ''
  if(!email.value){
    error.value = 'Vui lòng nhập email.'
    return
  }
  // Simulate sending reset link
  loading.value = true
  setTimeout(()=>{
    loading.value = false
    // redirect to login with a small notice (could be toast)
    router.push('/login')
  }, 900)
}
</script>

<template>
  <div class="forgot-page">
    <div class="forgot-card">
      <div class="brand">
        <h1>Quên mật khẩu</h1>
        <p>Nhập email của bạn để nhận liên kết đặt lại mật khẩu.</p>
      </div>

      <form class="form" @submit="submit">
        <label class="field">
          <span>Email</span>
          <input v-model="email" type="email" placeholder="you@example.com" />
        </label>

        <div class="actions">
          <button class="btn primary" :disabled="loading">{{ loading ? 'Đang gửi...' : 'Gửi liên kết' }}</button>
        </div>

        <p v-if="error" class="error">{{ error }}</p>
        <p class="note">Quay lại <router-link to="/login">Đăng nhập</router-link></p>
      </form>
    </div>
  </div>
</template>

<style scoped>
.forgot-page{ min-height: calc(100vh - 0px); display:flex; align-items:center; justify-content:center; background: linear-gradient(180deg,#f6f9ff 0%, #ffffff 100%); padding: 32px }
.forgot-card{ width:100%; max-width:420px; background:#fff; border-radius:12px; box-shadow:0 8px 20px rgba(37,44,97,0.08); padding:28px; box-sizing:border-box }
.brand h1{ font-size:22px; margin-bottom:6px }
.brand p{ color:#666; font-size:13px; margin-bottom:18px }
.form .field{ display:block; margin-bottom:12px }
.form .field span{ display:block; font-size:13px; margin-bottom:6px; color:#444 }
.form input{ width:100%; padding:10px 12px; border:1px solid #e6e9f2; border-radius:8px; font-size:14px; outline:none }
.actions{ margin-top:12px }
.btn{ width:100%; padding:10px 14px; border-radius:8px; border:none; cursor:pointer }
.btn.primary{ background:#6366f1; color:#fff; font-weight:600 }
.note{ font-size:13px; color:#666; margin-top:12px }
.error{ color:#b91c1c; margin-top:10px; font-size:13px }

@media (max-width:480px){ .forgot-card{ padding:18px } }
</style>
