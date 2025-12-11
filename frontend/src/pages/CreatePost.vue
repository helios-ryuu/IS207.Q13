<script setup>
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../utils/useAuth'
import HeaderOther from '../components/layout/SearchHeader.vue'
import Footer from '../components/layout/AppFooter.vue'
import CascadingCategoryModal from '../components/modals/CascadingCategoryModal.vue'

const router = useRouter()
const { user } = useAuth()

// Form state - KHÔNG DÙNG PINIA
const category = ref('')
const isCategoryModalOpen = ref(false)
const condition = ref('')
const accessoryType = ref('')
const warranty = ref('')
const origin = ref('')
const freeGift = ref(false)
const price = ref('')
const title = ref('')
const description = ref('')
const sellerType = ref('personal')
const location = ref('')

// Category-specific fields
const categoryData = ref({})

// File upload state
const photos = ref([])
const video = ref(null)
const photoInput = ref(null)
const videoInput = ref(null)

// Watch category changes to reset category-specific data
watch(category, (newCategory) => {
  // Reset category data and initialize select fields with empty string
  const mainCategory = newCategory ? newCategory.split(' - ')[0] : ''
  const fields = categoryFields[mainCategory]?.fields || []
  const newData = {}
  fields.forEach(field => {
    if (field.type === 'select') {
      newData[field.key] = '' // Initialize select fields with empty string to show placeholder
    }
  })
  categoryData.value = newData
  condition.value = ''
  price.value = ''
})

// Category field configurations
const categoryFields = {
  'Đồ điện tử': {
    fields: [
      { key: 'productType', label: 'Loại sản phẩm', type: 'select', required: true, placeholder: 'Chọn loại sản phẩm', options: [
        { value: 'phone', label: 'Điện thoại' },
        { value: 'laptop', label: 'Laptop' },
        { value: 'tablet', label: 'Máy tính bảng' },
        { value: 'camera', label: 'Máy ảnh' },
        { value: 'tv', label: 'Tivi' },
        { value: 'accessory', label: 'Phụ kiện' },
      ]},
      { key: 'brand', label: 'Thương hiệu', type: 'text', required: true, placeholder: 'Thương hiệu. VD: Apple, Samsung' },
      { key: 'warranty', label: 'Bảo hành', type: 'select', required: false, placeholder: 'Chọn bảo hành', options: [
        { value: 'yes', label: 'Còn bảo hành' },
        { value: 'no', label: 'Hết bảo hành' },
      ]},
      { key: 'origin', label: 'Xuất xứ', type: 'text', required: false, placeholder: 'Xuất xứ. VD: Chính hãng, Xách tay' },
      { key: 'accessories', label: 'Phụ kiện đi kèm', type: 'textarea', required: false, placeholder: 'Phụ kiện. VD: Sạc, tai nghe, hộp.. .' },
    ]
  },
  'Bất động sản': {
    fields: [
      { key: 'propertyType', label: 'Loại hình', type: 'select', required: true, placeholder: 'Chọn loại hình', options: [
        { value: 'apartment', label: 'Căn hộ/Chung cư' },
        { value: 'house', label: 'Nhà riêng' },
        { value: 'land', label: 'Đất' },
        { value: 'office', label: 'Văn phòng, Mặt bằng' },
      ]},
      { key: 'area', label: 'Diện tích (m²)', type: 'number', required: true, placeholder: 'Diện tích. VD: 50' },
      { key: 'bedrooms', label: 'Số phòng ngủ', type: 'number', required: false, placeholder: 'Số phòng ngủ. VD: 2' },
      { key: 'bathrooms', label: 'Số phòng tắm', type: 'number', required: false, placeholder: 'Số phòng tắm. VD: 2' },
      { key: 'legalDoc', label: 'Giấy tờ pháp lý', type: 'select', required: false, placeholder: 'Chọn giấy tờ', options: [
        { value: 'book', label: 'Sổ đỏ/Sổ hồng' },
        { value: 'contract', label: 'Hợp đồng mua bán' },
        { value: 'waiting', label: 'Đang chờ sổ' },
      ]},
      { key: 'furniture', label: 'Nội thất', type: 'select', required: false, placeholder: 'Chọn nội thất', options: [
        { value: 'full', label: 'Đầy đủ' },
        { value: 'partial', label: 'Một phần' },
        { value: 'none', label: 'Không có' },
      ]},
    ]
  },
  'Xe cộ': {
    fields: [
      { key: 'vehicleType', label: 'Loại xe', type: 'select', required: true, placeholder: 'Chọn loại xe', options: [
        { value: 'car', label: 'Ô tô' },
        { value: 'motorcycle', label: 'Xe máy' },
        { value: 'bicycle', label: 'Xe đạp' },
        { value: 'other', label: 'Xe khác' },
      ]},
      { key: 'brand', label: 'Hãng xe', type: 'text', required: true, placeholder: 'Hãng xe. VD: Honda, Toyota' },
      { key: 'year', label: 'Năm sản xuất', type: 'number', required: true, placeholder: 'Năm sản xuất. VD: 2020' },
      { key: 'mileage', label: 'Số km đã đi', type: 'number', required: false, placeholder: 'Số km đã đi. VD: 50000' },
      { key: 'origin', label: 'Xuất xứ', type: 'select', required: false, placeholder: 'Chọn xuất xứ', options: [
        { value: 'domestic', label: 'Trong nước' },
        { value: 'imported', label: 'Nhập khẩu' },
      ]},
      { key: 'transmission', label: 'Hộp số', type: 'select', required: false, placeholder: 'Chọn hộp số', options: [
        { value: 'manual', label: 'Số sàn' },
        { value: 'automatic', label: 'Số tự động' },
      ]},
    ]
  },
  'Thời trang': {
    fields: [
      { key: 'productType', label: 'Loại sản phẩm', type: 'select', required: true, placeholder: 'Chọn loại sản phẩm', options: [
        { value: 'clothing', label: 'Quần áo' },
        { value: 'shoes', label: 'Giày dép' },
        { value: 'bag', label: 'Túi xách' },
        { value: 'accessory', label: 'Phụ kiện' },
      ]},
      { key: 'brand', label: 'Thương hiệu', type: 'text', required: false, placeholder: 'Thương hiệu. VD: Zara, H&M' },
      { key: 'size', label: 'Size', type: 'text', required: false, placeholder: 'Size. VD: M, L, XL, 38, 39' },
      { key: 'color', label: 'Màu sắc', type: 'text', required: false, placeholder: 'Màu sắc. VD: Đen, Trắng' },
      { key: 'material', label: 'Chất liệu', type: 'text', required: false, placeholder: 'Chất liệu. VD: Cotton, Da, Vải' },
    ]
  },
  'Đồ gia dụng': {
    fields: [
      { key: 'productType', label: 'Loại sản phẩm', type: 'select', required: true, placeholder: 'Chọn loại sản phẩm', options: [
        { value: 'furniture', label: 'Nội thất' },
        { value: 'appliance', label: 'Thiết bị điện' },
        { value: 'kitchenware', label: 'Đồ bếp' },
        { value: 'decoration', label: 'Đồ trang trí' },
      ]},
      { key: 'brand', label: 'Thương hiệu', type: 'text', required: false, placeholder: 'Thương hiệu. VD: Panasonic, Electrolux' },
      { key: 'material', label: 'Chất liệu', type: 'text', required: false, placeholder: 'Chất liệu. VD: Gỗ, Kim loại, Nhựa' },
      { key: 'warranty', label: 'Bảo hành', type: 'select', required: false, placeholder: 'Chọn bảo hành', options: [
        { value: 'yes', label: 'Còn bảo hành' },
        { value: 'no', label: 'Hết bảo hành' },
      ]},
    ]
  },
  'Mẹ và Bé': {
    fields: [
      { key: 'productType', label: 'Loại sản phẩm', type: 'select', required: true, placeholder: 'Chọn loại sản phẩm', options: [
        { value: 'clothes', label: 'Quần áo trẻ em' },
        { value: 'toys', label: 'Đồ chơi' },
        { value: 'gear', label: 'Đồ dùng mẹ và bé' },
        { value: 'food', label: 'Thực phẩm' },
      ]},
      { key: 'ageRange', label: 'Độ tuổi phù hợp', type: 'text', required: false, placeholder: 'Độ tuổi phù hợp. VD: 0-6 tháng, 1-3 tuổi' },
      { key: 'brand', label: 'Thương hiệu', type: 'text', required: false, placeholder: 'Thương hiệu. VD: Huggies, Pampers' },
    ]
  },
  'Thú cưng': {
    fields: [
      { key: 'petType', label: 'Loại thú cưng', type: 'select', required: true, placeholder: 'Chọn loại thú cưng', options: [
        { value: 'dog', label: 'Chó' },
        { value: 'cat', label: 'Mèo' },
        { value: 'bird', label: 'Chim' },
        { value: 'other', label: 'Khác' },
      ]},
      { key: 'breed', label: 'Giống', type: 'text', required: false, placeholder: 'Giống. VD: Alaska, Corgi' },
      { key: 'age', label: 'Tuổi', type: 'text', required: false, placeholder: 'Tuổi. VD: 2 tháng, 1 năm' },
      { key: 'gender', label: 'Giới tính', type: 'select', required: false, placeholder: 'Chọn giới tính', options: [
        { value: 'male', label: 'Đực' },
        { value: 'female', label: 'Cái' },
      ]},
      { key: 'vaccinated', label: 'Tiêm phòng', type: 'select', required: false, placeholder: 'Chọn tiêm phòng', options: [
        { value: 'yes', label: 'Đã tiêm đủ' },
        { value: 'partial', label: 'Tiêm một phần' },
        { value: 'no', label: 'Chưa tiêm' },
      ]},
    ]
  },
}

// Get current category fields
const getCurrentCategoryFields = () => {
  if (!category.value) return []
  // Extract main category from "Đồ điện tử - Điện thoại" format
  const mainCategory = category.value.split(' - ')[0]
  return categoryFields[mainCategory]?.fields || []
}

// Handlers
const handlePhotoUpload = (event) => {
  const files = Array.from(event.target. files)
  if (photos.value.length + files.length <= 6) {
    photos.value. push(...files. slice(0, 6 - photos.value.length))
  } else {
    alert('Bạn chỉ có thể đăng tối đa 6 hình ảnh')
  }
}

const removePhoto = (index) => {
  photos.value.splice(index, 1)
}

const handleVideoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    video.value = file
  }
}

const removeVideo = () => {
  video.value = null
  if (videoInput.value) {
    videoInput.value.value = ''
  }
}

const triggerPhotoUpload = () => {
  photoInput.value?. click()
}

const triggerVideoUpload = () => {
  videoInput.value?.click()
}

const handlePreview = () => {
  console.log('Preview post', getFormData())
}

const handleSaveDraft = () => {
  console.log('Save draft', getFormData())
  alert('Đã lưu nháp!')
}

const handleCategorySelect = (selectedCategory) => {
  category. value = selectedCategory
  isCategoryModalOpen.value = false
}

const openCategoryModal = () => {
  isCategoryModalOpen.value = true
}

const getFormData = () => {
  return {
    category: category.value,
    condition: condition.value,
    accessoryType: accessoryType.value,
    warranty: warranty.value,
    origin: origin.value,
    freeGift: freeGift.value,
    price: price.value,
    title: title.value,
    description: description.value,
    sellerType: sellerType.value,
    location: location.value,
    photos: photos.value,
    video: video.value,
    categoryData: categoryData.value
  }
}

const validateForm = () => {
  if (!category.value) {
    alert('Vui lòng chọn danh mục!')
    return false
  }
  if (!condition.value) {
    alert('Vui lòng chọn tình trạng!')
    return false
  }
  if (! price.value) {
    alert('Vui lòng nhập giá!')
    return false
  }
  if (!title.value) {
    alert('Vui lòng nhập tiêu đề!')
    return false
  }
  if (!description.value) {
    alert('Vui lòng nhập mô tả!')
    return false
  }
  if (!location.value) {
    alert('Vui lòng chọn địa chỉ!')
    return false
  }
  
  const fields = getCurrentCategoryFields()
  for (const field of fields) {
    if (field.required && !categoryData.value[field.key]) {
      alert(`Vui lòng điền ${field.label}!`)
      return false
    }
  }
  
  return true
}

const handleSubmit = () => {
  if (!validateForm()) {
    return
  }
  
  const formData = getFormData()
  console.log('Submit post', formData)
  
  alert('Đăng tin thành công!')
  router.push('/home')
}
</script>

<template>
  <div class="create-post-page">
    <HeaderOther />

    <!-- Main Content -->
    <div class="content-wrapper">
      <div class="main-content">
        <!-- Left Panel: Upload Section -->
        <div class="upload-section">
          <h2 class="section-title">Hình ảnh và Video sản phẩm</h2>
          <p class="upload-hint">
            Xem thêm về <a href="#" class="link-blue">Quy định đăng tin của VietMarket</a>
          </p>
          
          <!-- Photo Upload Box -->
          <div class="upload-box" @click="triggerPhotoUpload">
            <input 
              ref="photoInput" 
              type="file" 
              accept="image/*" 
              multiple 
              style="display: none" 
              @change="handlePhotoUpload"
            />
            <div class="upload-icon">📷</div>
            <p class="upload-text">ĐĂNG TỪ 01 ĐẾN 06 HÌNH</p>
            <div class="info-tag">
              <span class="info-icon">ℹ️</span>
              <span>Hình ảnh hợp lệ</span>
            </div>
          </div>

          <!-- Photo Preview -->
          <div v-if="photos.length > 0" class="photo-preview-grid">
            <div v-for="(photo, index) in photos" :key="index" class="photo-preview-item">
              <img :src="URL.createObjectURL(photo)" :alt="`Photo ${index + 1}`" />
              <button class="remove-btn" @click. stop="removePhoto(index)">✕</button>
            </div>
          </div>
          
          <!-- Video Upload Box -->
          <div class="upload-box" @click="triggerVideoUpload">
            <input 
              ref="videoInput" 
              type="file" 
              accept="video/*" 
              style="display: none" 
              @change="handleVideoUpload"
            />
            <div class="upload-icon">🎥</div>
            <p class="upload-text-bold">Đăng video để bán nhanh hơn</p>
            <div class="info-tag">
              <span class="info-icon">ℹ️</span>
              <span>Video hợp lệ</span>
            </div>
          </div>

          <!-- Video Preview -->
          <div v-if="video" class="video-preview">
            <video :src="URL.createObjectURL(video)" controls style="width: 100%; border-radius: 5px;"></video>
            <button class="remove-btn" @click. stop="removeVideo">✕ Xóa video</button>
          </div>
        </div>

        <!-- Right Panel: Form Section -->
        <div class="form-section">
          <!-- Category Dropdown -->
          <div class="category-box" @click="openCategoryModal">
            <div class="category-header">
              <span class="category-label-small">Danh Mục Tin Đăng <span class="required">*</span></span>
            </div>
            <div class="category-value">
              <span>{{ category || 'Chọn danh mục.. .' }}</span>
              <span class="dropdown-arrow">▼</span>
            </div>
          </div>

          <!-- Common Details Section -->
          <div v-if="category" class="details-section">
            <h3 class="details-title">Thông tin chung</h3>
            
            <div class="input-wrapper">
              <select v-model="condition" class="form-select" required>
                <option value="" disabled selected>Tình trạng *</option>
                <option value="new">Mới 100%</option>
                <option value="likenew">Như mới</option>
                <option value="used">Đã sử dụng</option>
              </select>
            </div>
            
            <div class="input-wrapper">
              <input v-model="price" type="number" placeholder="Giá bán (VNĐ) *" class="form-input" required />
            </div>
          </div>

          <!-- Category-Specific Details Section -->
          <div v-if="category && getCurrentCategoryFields().length > 0" class="details-section">
            <h3 class="details-title">Thông tin chi tiết - {{ category }}</h3>
            
            <div v-for="field in getCurrentCategoryFields()" :key="field. key" class="input-wrapper">
              <!-- Text Input -->
              <input 
                v-if="field.type === 'text'" 
                v-model="categoryData[field. key]"
                type="text" 
                :placeholder="field.placeholder || (field.label + (field.required ? ' *' : ''))"
                class="form-input"
                :required="field.required"
              />
              
              <!-- Number Input -->
              <input 
                v-else-if="field.type === 'number'" 
                v-model="categoryData[field.key]"
                type="number" 
                :placeholder="field.placeholder || (field.label + (field.required ? ' *' : ''))"
                class="form-input"
                :required="field.required"
              />
              
              <!-- Select Input -->
              <select 
                v-else-if="field.type === 'select'" 
                v-model="categoryData[field.key]"
                class="form-select"
                :required="field.required"
              >
                <option value="" disabled selected>{{ field.placeholder || (field.label + (field.required ? ' *' : '')) }}</option>
                <option v-for="option in field.options" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
              
              <!-- Textarea Input -->
              <textarea 
                v-else-if="field.type === 'textarea'" 
                v-model="categoryData[field.key]"
                :placeholder="field.placeholder || field.label"
                class="form-textarea-small"
                rows="3"
                :required="field.required"
              ></textarea>
            </div>
          </div>

          <!-- Title and Description Section -->
          <div v-if="category" class="title-description-section">
            <h3 class="details-title">Tiêu đề tin đăng và Mô tả chi tiết</h3>
            
            <div class="textarea-wrapper">
              <input v-model="title" type="text" placeholder="Tiêu đề tin đăng *" class="form-input" maxlength="100" required />
              <span class="char-count">{{ title.length }}/100 kí tự</span>
            </div>
            
            <div class="textarea-wrapper">
              <textarea 
                v-model="description" 
                placeholder="Mô tả chi tiết *&#10;• Tình trạng sản phẩm&#10;• Bảo hành nếu có&#10;• Phụ kiện đi kèm&#10;• Lý do bán&#10;• Chấp nhận thanh toán/vận chuyển qua VietMarket" 
                class="form-textarea" 
                maxlength="3000"
                required
              ></textarea>
              <span class="char-count">{{ description.length }}/3000 kí tự</span>
            </div>
          </div>

          <!-- Seller Info Section -->
          <div v-if="category" class="seller-section">
            <h3 class="details-title">Thông tin người bán</h3>
            <p class="seller-label">Bạn là <span class="required">*</span></p>
            
            <div class="seller-type-btns">
              <button 
                :class="['seller-btn', { active: sellerType === 'personal' }]"
                @click="sellerType = 'personal'"
                type="button"
              >
                Cá nhân
              </button>
              <button 
                :class="['seller-btn', { active: sellerType === 'business' }]"
                @click="sellerType = 'business'"
                type="button"
              >
                Bán chuyên
              </button>
            </div>
            
            <div class="input-wrapper">
              <select v-model="location" class="form-select" required>
                <option value="" disabled>Địa chỉ *</option>
                <option value="hcm">TP. Hồ Chí Minh</option>
                <option value="hanoi">Hà Nội</option>
                <option value="danang">Đà Nẵng</option>
                <option value="cantho">Cần Thơ</option>
                <option value="haiphong">Hải Phòng</option>
                <option value="other">Tỉnh/Thành phố khác</option>
              </select>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Fixed Bottom Action Buttons -->
    <div class="action-buttons-fixed">
      <div class="action-buttons">
        <button class="btn-secondary" @click="handlePreview" type="button">Xem trước</button>
        <button class="btn-secondary" @click="handleSaveDraft" type="button">Lưu nháp</button>
        <button class="btn-primary" @click="handleSubmit" type="button">Đăng tin</button>
      </div>
    </div>

    <Footer />

    <CascadingCategoryModal
      v-if="isCategoryModalOpen"
      @close="isCategoryModalOpen = false"
      @select="handleCategorySelect"
    />
  </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.create-post-page {
  min-height: 100vh;
  background-color: #f4f4f4;
  display: flex;
  flex-direction: column;
  padding-bottom: 80px;
}

/* Main Content */
.content-wrapper {
  width: 100%;
  max-width: 1440px;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  margin: 0 auto;
}

.main-content {
  width: 100%;
  max-width: 1000px;
  background: white;
  padding: 44px 36px;
  display: flex;
  gap: 50px;
}

/* Upload Section */
.upload-section {
  width: 329px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.section-title {
  font-family: inherit;
  font-weight: 700;
  font-size: 20px;
  color: black;
  margin-bottom: 8px;
}

.upload-hint {
  font-family: inherit;
  font-size: 14px;
  color: black;
  margin-bottom: 12px;
}

.link-blue {
  color: #1889d9;
  text-decoration: underline;
}

.upload-box {
  width: 100%;
  height: 200px;
  background: rgba(217, 217, 217, 0.5);
  border: 3px dashed #448aff;
  border-radius: 5px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  position: relative;
  margin-bottom: 16px;
  transition: all 0.3s;
}

.upload-box:hover {
  background: rgba(68, 138, 255, 0.1);
  border-color: #2196F3;
}

.upload-icon {
  font-size: 50px;
  margin-bottom: 12px;
}

.upload-text {
  font-family: inherit;
  font-size: 10px;
  color: rgba(97, 93, 93, 0.5);
  text-align: center;
}

.upload-text-bold {
  font-family: inherit;
  font-weight: 600;
  font-size: 10px;
  color: black;
  text-align: center;
}

.info-tag {
  position: absolute;
  bottom: 10px;
  right: 10px;
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 10px;
  color: black;
}

.info-icon {
  font-size: 12px;
}

.photo-preview-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  margin-bottom: 16px;
}

.photo-preview-item {
  position: relative;
  width: 100%;
  padding-top: 100%;
  background: #f0f0f0;
  border-radius: 5px;
  overflow: hidden;
}

.photo-preview-item img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.photo-preview-item .remove-btn {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 24px;
  height: 24px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.photo-preview-item .remove-btn:hover {
  background: #f44336;
  transform: scale(1.1);
}

.video-preview {
  position: relative;
  margin-bottom: 16px;
}

.video-preview .remove-btn {
  margin-top: 8px;
  width: 100%;
  padding: 8px;
  background: #f44336;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-family: inherit;
  font-size: 12px;
  transition: all 0.2s;
}

.video-preview .remove-btn:hover {
  background: #d32f2f;
}

.form-section {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.category-box {
  border: 1px solid #b1acac;
  border-radius: 5px;
  padding: 12px 16px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  cursor: pointer;
  transition: all 0. 2s;
}

.category-box:hover {
  border-color: #448aff;
  background: #f8fafc;
}

.category-header {
  display: flex;
  align-items: center;
}

.category-label-small {
  font-family: inherit;
  font-weight: 600;
  font-size: 11px;
  color: #666;
}

.category-value {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-family: inherit;
  font-weight: 600;
  font-size: 15px;
  color: black;
}

.dropdown-arrow {
  font-size: 12px;
  color: #666;
}

.required {
  color: #f43f3f;
}

.details-section,
.title-description-section,
.seller-section {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.details-title {
  font-family: inherit;
  font-weight: 700;
  font-size: 18px;
  color: black;
  margin-bottom: 8px;
}

.input-wrapper {
  position: relative;
  display: flex;
  flex-direction: column;
}

.form-input,
.form-select {
  width: 100%;
  height: 44px;
  border: 1px solid rgba(139, 137, 137, 0.5);
  border-radius: 5px;
  padding: 0 18px;
  font-family: inherit;
  font-size: 15px;
  color: rgba(97, 93, 93, 0.7);
  outline: none;
  transition: all 0.2s;
}

.form-input:focus,
.form-select:focus {
  border-color: #448aff;
  color: black;
  box-shadow: 0 0 0 3px rgba(68, 138, 255, 0.1);
}

.form-textarea-small {
  width: 100%;
  min-height: 80px;
  border: 1px solid rgba(139, 137, 137, 0.5);
  border-radius: 5px;
  padding: 12px 18px;
  font-family: inherit;
  font-size: 15px;
  color: rgba(97, 93, 93, 0.7);
  resize: vertical;
  outline: none;
  transition: all 0.2s;
}

.form-textarea-small:focus {
  border-color: #448aff;
  color: black;
  box-shadow: 0 0 0 3px rgba(68, 138, 255, 0.1);
}

.textarea-wrapper {
  position: relative;
  display: flex;
  flex-direction: column;
}

.form-textarea {
  width: 100%;
  min-height: 130px;
  border: 1px solid rgba(139, 137, 137, 0.5);
  border-radius: 5px;
  padding: 18px;
  font-family: inherit;
  font-size: 15px;
  color: rgba(97, 93, 93, 0.7);
  resize: vertical;
  outline: none;
  transition: all 0.2s;
}

.form-textarea:focus {
  border-color: #448aff;
  color: black;
  box-shadow: 0 0 0 3px rgba(68, 138, 255, 0.1);
}

.char-count {
  margin-top: 4px;
  font-family: inherit;
  font-size: 11px;
  color: #999;
  align-self: flex-end;
}

.seller-label {
  font-family: inherit;
  font-size: 15px;
  color: rgba(97, 93, 93, 0.7);
}

.seller-type-btns {
  display: flex;
  gap: 12px;
}

.seller-btn {
  flex: 1;
  height: 40px;
  border-radius: 5px;
  border: 1px solid #ddd;
  font-family: inherit;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s;
  background: white;
}

.seller-btn.active {
  background: linear-gradient(90deg, rgba(239, 148, 2, 0.2), rgba(35, 145, 235, 0.2));
  border-color: #448aff;
  color: #448aff;
  font-weight: 600;
}

.seller-btn:not(.active):hover {
  border-color: #999;
  background: #f5f5f5;
}

/* Fixed Action Buttons */
.action-buttons-fixed {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: white;
  box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  display: flex;
  justify-content: center;
  padding: 12px 20px;
}

.action-buttons {
  display: flex;
  justify-content: center;
  gap: 20px;
}

.btn-secondary,
.btn-primary {
  min-width: 120px;
  padding: 12px 24px;
  border-radius: 5px;
  font-family: inherit;
  font-weight: 600;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary {
  background: white;
  border: 1px solid #8b8989;
  color: rgba(0, 0, 0, 0.8);
}

.btn-secondary:hover {
  background: #f5f5f5;
  border-color: #666;
}

.btn-primary {
  background: #d47b15;
  border: none;
  color: white;
}

.btn-primary:hover {
  background: #b86a12;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(212, 123, 21, 0.3);
}

/* Responsive */
@media (max-width: 1024px) {
  .main-content {
    flex-direction: column;
    gap: 30px;
  }
  
  .upload-section {
    width: 100%;
  }
  
  .photo-preview-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 768px) {
  .action-buttons {
    flex-wrap: wrap;
    width: 100%;
  }
  
  .btn-secondary,
  .btn-primary {
    flex: 1;
    min-width: 100px;
  }
  
  .photo-preview-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
</style>
