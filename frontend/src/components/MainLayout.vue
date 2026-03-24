<template>
  <SidebarNav />
  <div class="min-h-screen bg-gray-50 transition-all duration-300" :style="{ 'margin-left': isCollapsed ? '5rem' : '18rem' }">
    <slot></slot>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import SidebarNav from './SidebarNav.vue';

const isCollapsed = ref(false);

// load sidebar state from localStorage on mount
const savedState = localStorage.getItem('sidebar-collapsed');
if (savedState !== null) {
  isCollapsed.value = savedState === 'true';
}

// listen for sidebar toggle events
window.addEventListener('sidebar-toggle', (event) => {
  isCollapsed.value = event.detail.isCollapsed;
  // also update localStorage to keep in sync
  localStorage.setItem('sidebar-collapsed', isCollapsed.value);
});
</script>
