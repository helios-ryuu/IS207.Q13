<template>
  <div class="sub-view">
    <header class="sub-view-header">
      <button class="back-btn" @click="$emit('close')">
        <font-awesome-icon icon="arrow-left" />
      </button>
      <h3>{{ title }}</h3>
    </header>

    <div class="search-bar">
      <font-awesome-icon icon="search" class="search-icon" />
      <input type="text" :placeholder="`Nhập tìm ${title.toLowerCase()}`" v-model="searchTerm">
    </div>

    <div class="options-list">
      <ul>
        <li v-if="showAllOption" @click="handleSelect(null)">
          <span class="radio-label">Tất cả</span>
          <span class="radio-custom" :class="{ active: localValue === null }"></span>
        </li>
        <li v-if="title === 'Chọn tỉnh thành'" @click="handleSelect('Toàn quốc')">
          <span class="radio-label">Toàn quốc</span>
          <span class="radio-custom" :class="{ active: localValue === 'Toàn quốc' }"></span>
        </li>
        
        <li v-for="option in filteredOptions" :key="option.value" @click="handleSelect(option.value)">
          <span class="radio-label">{{ option.label }}</span>
          <span class="radio-custom" :class="{ active: localValue === option.value }"></span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  title: String,
  options: Array, // [ { value: 'HN', label: 'Hà Nội' }, ... ]
  currentValue: String,
  showAllOption: { // Prop để quyết định có hiện nút "Tất cả" không
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['close', 'select']);

const searchTerm = ref('');
const localValue = ref(props.currentValue);

// Lọc danh sách dựa trên thanh tìm kiếm
const filteredOptions = computed(() => {
  if (!searchTerm.value) {
    return props.options;
  }
  return props.options.filter(option => 
    option.label.toLowerCase().includes(searchTerm.value.toLowerCase())
  );
});

// Khi bấm chọn, cập nhật giá trị và gửi event
const handleSelect = (value) => {
  localValue.value = value;
  emit('select', value);
};
</script>

<style scoped>
/* Giao diện cho component con */
.sub-view {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
}
.sub-view-header {
  display: flex;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #eee;
}
.back-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  color: #555;
  cursor: pointer;
  margin-right: 1.5rem;
}
.sub-view-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

/* Thanh tìm kiếm */
.search-bar {
  position: relative;
  padding: 1rem 1.5rem;
}
.search-bar .search-icon {
  position: absolute;
  left: 2.5rem;
  top: 50%;
  transform: translateY(-50%);
  color: #888;
}
.search-bar input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem; /* Padding trái cho icon */
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #f5f5f5;
  outline: none;
}
.search-bar input:focus {
  border-color: #f5a623;
  background: white;
}

/* Danh sách Lựa chọn */
.options-list {
  overflow-y: auto;
  flex-grow: 1;
}
.options-list ul {
  list-style: none;
  margin: 0;
  padding: 0;
}
.options-list li {
  display: flex;
  align-items: center;
  padding: 1rem 1.5rem;
  cursor: pointer;
  border-bottom: 1px solid #f5f5f5;
}
.options-list li:hover {
  background: #f9f9f9;
}
.radio-label {
  flex-grow: 1;
  font-size: 1rem;
}
/* Radio button tùy chỉnh */
.radio-custom {
  width: 22px;
  height: 22px;
  border: 2px solid #ccc;
  border-radius: 50%;
  display: inline-block;
  position: relative;
}
.radio-custom::after {
  content: '';
  width: 12px;
  height: 12px;
  background: white;
  border-radius: 50%;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) scale(0);
  transition: transform 0.2s ease-in-out;
}
.radio-custom.active {
  background-color: #007bff;
  border-color: #007bff;
}
.radio-custom.active::after {
  transform: translate(-50%, -50%) scale(1);
}
</style>