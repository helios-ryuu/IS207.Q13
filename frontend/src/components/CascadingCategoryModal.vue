<template>
  <div class="modal-overlay" @click="handleClose">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3>Chọn Danh Mục Tin Đăng</h3>
        <button class="close-btn" @click="handleClose">✕</button>
      </div>
      
      <div class="modal-body">
        <div class="breadcrumb" v-if="selectedPath.length > 0">
          <span v-for="(item, index) in selectedPath" :key="index">
            <span class="breadcrumb-item">{{ item }}</span>
            <span v-if="index < selectedPath.length - 1" class="separator">›</span>
          </span>
        </div>

        <div class="category-list">
          <!-- Main Categories -->
          <div v-if="currentLevel === 0" class="category-items">
            <div 
              v-for="cat in mainCategories" 
              :key="cat.name"
              class="category-item"
              @click="selectMainCategory(cat)"
            >
              <span class="category-icon">{{ cat.icon }}</span>
              <span class="category-name">{{ cat.name }}</span>
              <span class="arrow">›</span>
            </div>
          </div>

          <!-- Sub Categories Level 1 -->
          <div v-else-if="currentLevel === 1" class="category-items">
            <button class="back-btn" @click="goBack">
              <span>‹ Quay lại</span>
            </button>
            <div 
              v-for="sub in currentSubCategories" 
              :key="sub.name"
              class="category-item"
              @click="selectSubCategory(sub)"
            >
              <span class="category-icon">{{ sub.icon }}</span>
              <span class="category-name">{{ sub.name }}</span>
              <span class="check">✓</span>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer" v-if="selectedFullPath">
        <button class="confirm-btn" @click="confirmSelection">Xác nhận</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const emit = defineEmits(['close', 'select'])

const currentLevel = ref(0) // 0: main, 1: sub, 2: sub2
const selectedPath = ref([])
const selectedMain = ref(null)
const selectedSub = ref(null)
const selectedFinal = ref(null)

const mainCategories = ref([
  {
    name: 'Đồ điện tử',
    icon: '💻',
    children: [
      { name: 'Điện thoại', icon: '📱' },
      { name: 'Máy tính bảng', icon: '📱' },
      { name: 'Laptop', icon: '💻' },
      { name: 'Máy tính để bàn', icon: '🖥️' },
      { name: 'Máy ảnh, Máy quay', icon: '📷' },
      { name: 'Tivi, Âm thanh', icon: '📺' },
      { name: 'Thiết bị đeo thông minh', icon: '⌚' },
      { name: 'Phụ kiện (Màn hình, Chuột,...)', icon: '🔌' },
      { name: 'Linh kiện (RAM, Ổ cứng,...)', icon: '🔧' }
    ]
  },
  {
    name: 'Xe cộ',
    icon: '🚗',
    children: [
      { name: 'Ô tô', icon: '🚗' },
      { name: 'Xe máy', icon: '🏍️' },
      { name: 'Xe tải, xe ben', icon: '🚚' },
      { name: 'Xe điện', icon: '⚡' },
      { name: 'Xe đạp', icon: '🚲' },
      { name: 'Phụ tùng, đồ chơi xe', icon: '🔧' }
    ]
  },
  {
    name: 'Đồ gia dụng, Nội thất, Cây cảnh',
    icon: '🪑',
    children: [
      { name: 'Bàn ghế', icon: '🪑' },
      { name: 'Tủ, kệ, gia đình', icon: '🗄️' },
      { name: 'Giường, chăn ga gối nệm', icon: '🛏️' },
      { name: 'Bếp, lò, đồ điện nhà bếp', icon: '🍳' },
      { name: 'Dụng cụ nhà bếp', icon: '🔪' },
      { name: 'Quạt', icon: '💨' },
      { name: 'Đèn', icon: '💡' },
      { name: 'Cây cảnh, đồ trang trí', icon: '🌿' },
      { name: 'Thiết bị vệ sinh, nhà tắm', icon: '🚿' },
      { name: 'Nội thất, đồ gia dụng khác', icon: '🏠' }
    ]
  },
  {
    name: 'Thú cưng',
    icon: '🐾',
    children: [
      { name: 'Chó', icon: '🐕' },
      { name: 'Mèo', icon: '🐈' },
      { name: 'Cá cảnh', icon: '🐠' },
      { name: 'Chim cảnh', icon: '🦜' },
      { name: 'Thú cưng khác', icon: '🐾' }
    ]
  },
  {
    name: 'Thời trang, Đồ dùng cá nhân',
    icon: '👔',
    children: [
      { name: 'Quần áo', icon: '👕' },
      { name: 'Đồng hồ', icon: '⌚' },
      { name: 'Giày dép', icon: '👟' },
      { name: 'Túi xách', icon: '👜' },
      { name: 'Nước hoa', icon: '🌸' },
      { name: 'Phụ kiện thời trang', icon: '💍' }
    ]
  },
  {
    name: 'Giải trí, Thể thao, Sở thích',
    icon: '⚽',
    children: [
      { name: 'Nhạc cụ', icon: '🎸' },
      { name: 'Sách', icon: '📚' },
      { name: 'Đồ thể thao, Dã ngoại', icon: '⚽' },
      { name: 'Đồ sưu tầm, đồ cổ', icon: '🖼️' },
      { name: 'Thiết bị chơi game', icon: '🎮' },
      { name: 'Sở thích khác', icon: '🎨' }
    ]
  },
  {
    name: 'Đồ dùng văn phòng, Công nông nghiệp',
    icon: '🖨️',
    children: [
      { name: 'Đồ dùng văn phòng', icon: '🖨️' },
      { name: 'Đồ chuyên dụng, Giống nuôi trồng', icon: '🔧' }
    ]
  },
  {
    name: 'Đồ ăn, Thực phẩm và các loại khác',
    icon: '🍖',
    children: [
      { name: 'Thịt bò', icon: '🥩' },
      { name: 'Thịt heo', icon: '🥓' },
      { name: 'Thịt gà', icon: '🍗' },
      { name: 'Hải sản', icon: '🦐' },
      { name: 'Trứng', icon: '🥚' },
      { name: 'Sữa', icon: '🥛' },
      { name: 'Bánh', icon: '🍰' },
      { name: 'Kẹo', icon: '🍬' },
      { name: 'Nước ngọt', icon: '🥤' },
      { name: 'Khác', icon: '🍴' }
    ]
  },
  {
    name: 'Tủ lạnh, Máy lạnh, Máy giặt',
    icon: '❄️',
    children: [
      { name: 'Tủ lạnh', icon: '🧊' },
      { name: 'Máy lạnh', icon: '❄️' },
      { name: 'Máy giặt', icon: '🧺' }
    ]
  }
])

const currentSubCategories = computed(() => {
  if (selectedMain.value && selectedMain.value.children) {
    return selectedMain.value.children
  }
  return []
})

const selectedFullPath = computed(() => {
  if (selectedSub.value) {
    return selectedPath.value.join(' - ')
  }
  return null
})

const selectMainCategory = (cat) => {
  selectedMain.value = cat
  selectedPath.value = [cat.name]
  currentLevel.value = 1
  selectedSub.value = null
}

const selectSubCategory = (sub) => {
  selectedSub.value = sub
  selectedPath.value = [selectedMain.value.name, sub.name]
}

const goBack = () => {
  currentLevel.value = 0
  selectedMain.value = null
  selectedSub.value = null
  selectedPath.value = []
}

const confirmSelection = () => {
  if (selectedFullPath.value) {
    emit('select', selectedFullPath.value)
    handleClose()
  }
}

const handleClose = () => {
  emit('close')
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from { 
    opacity: 0;
    transform: translateY(30px); 
  }
  to { 
    opacity: 1;
    transform: translateY(0); 
  }
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid #e0e0e0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 20px;
  color: #0f172a;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 28px;
  color: #64748b;
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.modal-body {
  padding: 20px 24px;
  overflow-y: auto;
  flex: 1;
}

.breadcrumb {
  margin-bottom: 16px;
  padding: 12px;
  background: #f8fafc;
  border-radius: 8px;
  font-size: 14px;
  color: #475569;
}

.breadcrumb-item {
  font-weight: 600;
  color: #0f172a;
}

.separator {
  margin: 0 8px;
  color: #cbd5e1;
}

.category-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.category-items {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.back-btn {
  background: #f1f5f9;
  border: none;
  padding: 12px 16px;
  border-radius: 8px;
  font-family: 'Poppins', sans-serif;
  font-size: 15px;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
  margin-bottom: 8px;
}

.back-btn:hover {
  background: #e2e8f0;
  color: #0f172a;
}

.category-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  font-family: 'Poppins', sans-serif;
  font-size: 15px;
  color: #334155;
}

.category-item:hover {
  background: #f8fafc;
  border-color: #448aff;
  transform: translateX(4px);
}

.category-icon {
  font-size: 24px;
  flex-shrink: 0;
}

.category-name {
  flex: 1;
  font-weight: 500;
}

.arrow {
  color: #94a3b8;
  font-size: 20px;
  font-weight: bold;
}

.check {
  color: #10b981;
  font-size: 20px;
  font-weight: bold;
}

.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #e0e0e0;
  display: flex;
  justify-content: flex-end;
}

.confirm-btn {
  background: #d47b15;
  color: white;
  border: none;
  padding: 12px 32px;
  border-radius: 8px;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.2s;
}

.confirm-btn:hover {
  background: #b86a12;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(212, 123, 21, 0.3);
}

/* Scrollbar styling */
.modal-body::-webkit-scrollbar {
  width: 8px;
}

.modal-body::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
