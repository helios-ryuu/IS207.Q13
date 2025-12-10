<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../utils/useAuth'
import { useToast } from '../utils/useToast'
import api from '../utils/api'
import Header from '../components/Header-HomePage.vue'
import Footer from '../components/Footer.vue'

const router = useRouter()
const { user, updateUser } = useAuth()
const { showToast } = useToast()

// Form fields
const name = ref('')
const bio = ref('')
const phone = ref('')
const email = ref('')
const location = ref('')
const website = ref('')
const facebook = ref('')
const instagram = ref('')
const twitter = ref('')

// Avatar & Cover
const avatarPreview = ref(null)
const coverPreview = ref(null)
const avatarFile = ref(null)
const coverFile = ref(null)

// Loading & validation
const isSaving = ref(false)
const errors = ref({})

onMounted(() => {
  if (user?.value) {
    name.value = user.value.full_name || user.value.name || ''
    email.value = user.value.email || ''
    phone.value = user.value.phone_number || ''
    location.value = user.value.address || ''
    bio.value = user.value.bio || ''
    website.value = user.value.website || ''
    facebook.value = user.value.facebook || ''
    instagram.value = user.value.instagram || ''
    twitter.value = user.value.twitter || ''
    
    // Set avatar preview if exists
    if (user.value.avatar_url) {
      avatarPreview.value = user.value.avatar_url
    }
  }
})

const handleAvatarChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      errors.value.avatar = 'Ảnh đại diện không được vượt quá 5MB'
      return
    }
    avatarFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      avatarPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
    errors.value.avatar = null
  }
}

const handleCoverChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    if (file.size > 10 * 1024 * 1024) {
      errors.value.cover = 'Ảnh bìa không được vượt quá 10MB'
      return
    }
    coverFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      coverPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
    errors.value.cover = null
  }
}

const removeAvatar = () => {
  avatarPreview.value = null
  avatarFile.value = null
}

const removeCover = () => {
  coverPreview.value = null
  coverFile.value = null
}

const validateForm = () => {
  errors.value = {}
  
  if (!name.value.trim()) {
    errors.value.name = 'Tên không được để trống'
  }
  
  if (!email.value.trim()) {
    errors.value.email = 'Email không được để trống'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    errors.value.email = 'Email không hợp lệ'
  }
  
  if (phone.value && !/^[0-9]{10,11}$/.test(phone.value)) {
    errors.value.phone = 'Số điện thoại không hợp lệ (10-11 chữ số)'
  }
  
  if (website.value && !/^https?:\/\/.+/.test(website.value)) {
    errors.value.website = 'Website phải bắt đầu bằng http:// hoặc https://'
  }
  
  return Object.keys(errors.value).length === 0
}

const handleSave = async () => {
  if (!validateForm()) {
    return
  }
  
  isSaving.value = true
  errors.value = {}
  
  try {
    // 1. Update profile info (map to backend field names)
    const profileData = {
      full_name: name.value,
      phone_number: phone.value,
      address: location.value,
      bio: bio.value,
      website: website.value,
      facebook: facebook.value,
      instagram: instagram.value,
      twitter: twitter.value,
    }
    
    const profileResponse = await api.put('/user/profile', profileData)
    
    // 2. Upload avatar if changed
    let newAvatarUrl = null
    if (avatarFile.value) {
      const avatarFormData = new FormData()
      avatarFormData.append('avatar', avatarFile.value)
      
      const avatarResponse = await api.post('/user/change-avatar', avatarFormData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      // Get new avatar URL from response
      if (avatarResponse.data?.avatar_url) {
        newAvatarUrl = avatarResponse.data.avatar_url
      }
    }
    
    // 3. Upload cover if changed (not supported yet)
    // TODO: Implement cover photo feature
    
    // Update local user data
    if (profileResponse.data?.data) {
      const updatedUserData = { ...profileResponse.data.data }
      // Add new avatar_url if uploaded
      if (newAvatarUrl) {
        updatedUserData.avatar_url = newAvatarUrl
      }
      updateUser(updatedUserData)
    }
    
    showToast('Cập nhật hồ sơ thành công!', 'success')
    
    // Redirect to profile
    setTimeout(() => {
      router.push('/profile/social')
    }, 1000)
  } catch (error) {
    console.error('Failed to update profile:', error)
    
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
      showToast('Vui lòng kiểm tra lại thông tin', 'error')
    } else {
      showToast(error.response?.data?.message || 'Có lỗi xảy ra khi cập nhật hồ sơ', 'error')
    }
  } finally {
    isSaving.value = false
  }
}

const handleCancel = () => {
  router.push('/profile/social')
}
</script>

<template>
  <div>
    <Header />

    <div class="edit-profile-page container">
      <div class="page-header">
        <button class="back-btn" @click="handleCancel">
          <span class="icon">←</span>
          <span>Quay lại</span>
        </button>
        <h1 class="page-title">Chỉnh sửa hồ sơ</h1>
      </div>

      <div class="content-grid">
        <!-- Left: Form -->
        <div class="form-section">
          <div class="card">
            <h2 class="section-title">Thông tin cá nhân</h2>
            
            <div class="form-group">
              <label class="label">Tên hiển thị <span class="required">*</span></label>
              <input 
                v-model="name" 
                type="text" 
                class="input"
                :class="{ error: errors.name }"
                placeholder="Nhập tên của bạn"
              />
              <span v-if="errors.name" class="error-msg">{{ errors.name }}</span>
            </div>

            <div class="form-group">
              <label class="label">Email <span class="required">*</span></label>
              <input 
                v-model="email" 
                type="email" 
                class="input"
                :class="{ error: errors.email }"
                placeholder="email@example.com"
              />
              <span v-if="errors.email" class="error-msg">{{ errors.email }}</span>
            </div>

            <div class="form-group">
              <label class="label">Số điện thoại</label>
              <input 
                v-model="phone" 
                type="tel" 
                class="input"
                :class="{ error: errors.phone }"
                placeholder="0123456789"
              />
              <span v-if="errors.phone" class="error-msg">{{ errors.phone }}</span>
            </div>

            <div class="form-group">
              <label class="label">Địa chỉ</label>
              <input 
                v-model="location" 
                type="text" 
                class="input"
                placeholder="TP.HCM, Việt Nam"
              />
            </div>

            <div class="form-group">
              <label class="label">Giới thiệu bản thân</label>
              <textarea 
                v-model="bio" 
                class="textarea"
                rows="4"
                placeholder="Viết một vài dòng về bản thân bạn..."
              ></textarea>
              <div class="char-count">{{ bio.length }}/500</div>
            </div>
          </div>

          <div class="card">
            <h2 class="section-title">Liên kết mạng xã hội</h2>
            
            <div class="form-group">
              <label class="label">
                <span class="social-icon">🌐</span>
                Website
              </label>
              <input 
                v-model="website" 
                type="url" 
                class="input"
                :class="{ error: errors.website }"
                placeholder="https://yourwebsite.com"
              />
              <span v-if="errors.website" class="error-msg">{{ errors.website }}</span>
            </div>

            <div class="form-group">
              <label class="label">
                <span class="social-icon">📘</span>
                Facebook
              </label>
              <input 
                v-model="facebook" 
                type="text" 
                class="input"
                placeholder="facebook.com/username"
              />
            </div>

            <div class="form-group">
              <label class="label">
                <span class="social-icon">📷</span>
                Instagram
              </label>
              <input 
                v-model="instagram" 
                type="text" 
                class="input"
                placeholder="@username"
              />
            </div>

            <div class="form-group">
              <label class="label">
                <span class="social-icon">🐦</span>
                Twitter
              </label>
              <input 
                v-model="twitter" 
                type="text" 
                class="input"
                placeholder="@username"
              />
            </div>
          </div>

          <div class="actions">
            <button class="btn btn-outline" @click="handleCancel" :disabled="isSaving">
              Hủy
            </button>
            <button class="btn btn-primary" @click="handleSave" :disabled="isSaving">
              <span v-if="isSaving" class="spinner"></span>
              <span>{{ isSaving ? 'Đang lưu...' : 'Lưu thay đổi' }}</span>
            </button>
          </div>
        </div>

        <!-- Right: Preview & Images -->
        <div class="preview-section">
          <div class="card">
            <h2 class="section-title">Ảnh bìa</h2>
            <div class="cover-upload">
              <input 
                type="file" 
                accept="image/*" 
                style="display: none" 
                ref="coverInput"
                @change="handleCoverChange"
              />
              <div 
                v-if="!coverPreview" 
                class="upload-placeholder"
                @click="$refs.coverInput.click()"
              >
                <span class="icon">📷</span>
                <span class="text">Tải ảnh bìa lên</span>
                <span class="hint">Tối đa 10MB</span>
              </div>
              <div v-else class="image-preview">
                <img :src="coverPreview" alt="Cover" />
                <button class="remove-btn" @click="removeCover">✕</button>
              </div>
            </div>
            <span v-if="errors.cover" class="error-msg">{{ errors.cover }}</span>
          </div>

          <div class="card">
            <h2 class="section-title">Ảnh đại diện</h2>
            <div class="avatar-upload">
              <input 
                type="file" 
                accept="image/*" 
                style="display: none" 
                ref="avatarInput"
                @change="handleAvatarChange"
              />
              <div 
                v-if="!avatarPreview" 
                class="upload-placeholder round"
                @click="$refs.avatarInput.click()"
              >
                <span class="icon">👤</span>
                <span class="text">Tải ảnh lên</span>
                <span class="hint">Tối đa 5MB</span>
              </div>
              <div v-else class="image-preview round">
                <img :src="avatarPreview" alt="Avatar" />
                <button class="remove-btn" @click="removeAvatar">✕</button>
              </div>
            </div>
            <span v-if="errors.avatar" class="error-msg">{{ errors.avatar }}</span>
          </div>

          <div class="card info-card">
            <div class="info-icon">💡</div>
            <h3 class="info-title">Mẹo chỉnh sửa hồ sơ</h3>
            <ul class="info-list">
              <li>Sử dụng ảnh đại diện rõ ràng để tăng độ tin cậy</li>
              <li>Điền đầy đủ thông tin liên hệ để người mua dễ dàng liên lạc</li>
              <li>Viết giới thiệu ngắn gọn nhưng ấn tượng</li>
              <li>Liên kết mạng xã hội giúp xây dựng uy tín</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.edit-profile-page {
  background: #f8fafc;
  min-height: 100vh;
}

/* Header */
.page-header {
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.25rem;
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 600;
  color: #475569;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.back-btn:hover {
  border-color: #3b82f6;
  color: #3b82f6;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.15);
}

.back-btn .icon {
  font-size: 18px;
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  color: #0f172a;
}

/* Grid Layout */
.content-grid {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
}

/* Cards */
.card {
  background: white;
  padding: 2rem;
  border-radius: 20px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
  margin-bottom: 1.5rem;
  border: 2px solid transparent;
  transition: all 0.3s ease;
}

.card:hover {
  border-color: #3b82f6;
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
}

.section-title {
  font-size: 20px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Form Groups */
.form-group {
  margin-bottom: 1.5rem;
}

.label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 0.5rem;
  font-size: 14px;
}

.required {
  color: #ef4444;
}

.social-icon {
  font-size: 18px;
}

.input,
.textarea {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 15px;
  font-family: inherit;
  transition: all 0.3s ease;
  background: #f8fafc;
}

.input:focus,
.textarea:focus {
  outline: none;
  border-color: #3b82f6;
  background: white;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.input.error,
.textarea.error {
  border-color: #ef4444;
}

.textarea {
  resize: vertical;
  min-height: 100px;
}

.char-count {
  text-align: right;
  font-size: 12px;
  color: #94a3b8;
  margin-top: 0.25rem;
}

.error-msg {
  display: block;
  color: #ef4444;
  font-size: 13px;
  margin-top: 0.5rem;
  font-weight: 500;
}

/* Image Uploads */
.cover-upload,
.avatar-upload {
  margin-bottom: 0.5rem;
}

.upload-placeholder {
  border: 3px dashed #cbd5e1;
  border-radius: 16px;
  padding: 3rem 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #f8fafc;
}

.upload-placeholder:hover {
  border-color: #3b82f6;
  background: rgba(59, 130, 246, 0.05);
}

.upload-placeholder.round {
  border-radius: 50%;
  width: 200px;
  height: 200px;
  padding: 2rem;
  margin: 0 auto;
}

.upload-placeholder .icon {
  font-size: 48px;
  opacity: 0.5;
}

.upload-placeholder .text {
  font-weight: 600;
  color: #475569;
  font-size: 15px;
}

.upload-placeholder .hint {
  font-size: 13px;
  color: #94a3b8;
}

.image-preview {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
}

.image-preview.round {
  border-radius: 50%;
  width: 200px;
  height: 200px;
  margin: 0 auto;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.remove-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 36px;
  height: 36px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.remove-btn:hover {
  background: #ef4444;
  transform: scale(1.1);
}

/* Info Card */
.info-card {
  background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
  color: white;
  border: none;
}

.info-icon {
  font-size: 48px;
  margin-bottom: 1rem;
  text-align: center;
}

.info-title {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 1rem;
  text-align: center;
}

.info-list {
  list-style: none;
  padding: 0;
}

.info-list li {
  padding: 0.75rem 0;
  padding-left: 1.5rem;
  position: relative;
  line-height: 1.5;
  font-size: 14px;
}

.info-list li::before {
  content: '✓';
  position: absolute;
  left: 0;
  font-weight: bold;
  color: #86efac;
}

/* Actions */
.actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

.btn {
  padding: 1rem 2rem;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  font-weight: 700;
  font-size: 15px;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-outline {
  background: white;
  border: 2px solid #e2e8f0;
  color: #475569;
}

.btn-outline:hover:not(:disabled) {
  border-color: #3b82f6;
  color: #3b82f6;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.15);
}

.btn-primary {
  background: #3b82f6;
  color: white;
  box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(59, 130, 246, 0.4);
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 1024px) {
  .content-grid {
    grid-template-columns: 1fr;
  }

  .preview-section {
    order: -1;
  }
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .actions {
    flex-direction: column;
    width: 100%;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }
}
</style>
