<template>
  <MainLayout>
    <div class="p-8 max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
        <button @click="openAddModal" class="flex items-center gap-2 px-6 py-3 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
          <i class="bi bi-plus-circle"></i>
          Add Category
        </button>
      </div>

      <p v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-6">{{ error }}</p>
      
      <div v-if="isLoading && categories.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-500">
        <i class="bi bi-arrow-repeat text-4xl mb-4 animate-spin"></i>
        <p>Loading categories...</p>
      </div>

      <div v-else-if="categories.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-500">
        <i class="bi bi-folder-x text-4xl mb-4"></i>
        <p>No categories yet. Click "Add Category" to get started.</p>
      </div>
      
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="category in categories" :key="category.id" class="bg-white p-6 rounded-xl shadow-sm flex items-center gap-4">
          <div :class="['w-12 h-12 rounded-lg flex items-center justify-center text-xl text-white', getColorClass(category.color)]">
            <i :class="category.icon"></i>
          </div>
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ category.name }}</h3>
            <p v-if="category.budget_limit" class="text-sm text-gray-600 mb-2">
              ₱{{ (category.spent || 0).toFixed(2) }} / ₱{{ parseFloat(category.budget_limit).toFixed(2) }}
            </p>
            <p v-else class="text-sm text-gray-600 mb-2">
              ₱{{ (category.spent || 0).toFixed(2) }} spent
            </p>
            <div v-if="category.budget_limit" class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
              <div 
                class="h-full transition-all duration-300" 
                :class="(category.spent || 0) / parseFloat(category.budget_limit) > 0.8 ? 'bg-red-500' : 'bg-purple-500'"
                :style="{ 
                  width: Math.min((category.spent || 0) / parseFloat(category.budget_limit) * 100, 100) + '%'
                }"
              ></div>
            </div>
          </div>
          <div class="flex gap-2">
            <button @click="openEditModal(category)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-md transition-colors" title="Edit">
              <i class="bi bi-pencil"></i>
            </button>
            <button @click="deleteCategory(category.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Delete">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <CategoryModal
      :show="showModal"
      :isEditing="!!editingCategory"
      :form="categoryForm"
      :iconOptions="iconOptions"
      :colorOptions="colorOptions"
      :error="error"
      :isLoading="isLoading"
      @close="closeModal"
      @submit="handleSubmit"
    />
  </MainLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import MainLayout from './MainLayout.vue';
import CategoryModal from './CategoryModal.vue';
import { useCategories } from '@/composables/useCategories';

const {
  categories,
  isLoading,
  error,
  showModal,
  editingCategory,
  categoryForm,
  iconOptions,
  colorOptions,
  loadCategories,
  deleteCategory,
  openAddModal,
  openEditModal,
  closeModal,
  handleSubmit
} = useCategories();

const getColorClass = (color) => {
  const colorMap = {
    'primary': 'bg-purple-500',
    'secondary': 'bg-gray-500',
    'success': 'bg-green-500',
    'danger': 'bg-red-500',
    'warning': 'bg-yellow-500',
    'info': 'bg-blue-500',
    'dark': 'bg-gray-800',
    'light': 'bg-gray-200'
  };
  return colorMap[color] || 'bg-gray-500';
};

onMounted(() => {
  loadCategories();
});
</script>
