<template>
  <div v-if="message" :class="['toast-container', type]">
    {{ message }}
    <button @click="$emit('close')">x</button>
  </div>
</template>

<script setup>
//import { defineProps, defineEmits } from 'vue';
import { defineProps } from 'vue';

defineProps({
  message: String,
  type: String,
  key: [String, Number]
});

defineEmits(['close']);
</script>

<style scoped>

.toast-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  padding: 15px;
  background-color: #333;
  color: white;
  border-radius: 5px;
  z-index: 1000;
}
</style>