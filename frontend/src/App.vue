<script setup>
import { RouterView } from 'vue-router'
import Toast from './components/ui/Toast.vue'
import { useToast } from './utils/useToast'

const { toastMessage, toastType, toastKey, closeToast } = useToast()
</script>

<template>
  <RouterView />
  <Toast 
    :key="toastKey" 
    :message="toastMessage" 
    :type="toastType"
    @close="closeToast"
  />
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: system-ui, -apple-system, sans-serif;
  color: #333;
}
</style>
