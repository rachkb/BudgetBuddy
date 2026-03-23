<template>
  <SidebarNav />
  <div class="flex-1 ml-72 transition-all duration-300 min-h-screen bg-gray-50" :class="{ 'ml-20': isCollapsed }">
    <slot></slot>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import SidebarNav from './SidebarNav.vue';

const isCollapsed = ref(false);

// state changes
onMounted(() => {
  const savedState = localStorage.getItem('sidebar-collapsed');
  if (savedState !== null) {
    isCollapsed.value = savedState === 'true';
  }
  
  // sidebar toggle events
  window.addEventListener('sidebar-toggle', (event) => {
    isCollapsed.value = event.detail.isCollapsed;
  });
});
</script>
