<template>
  <MainLayout>
<div class="page-container">
      <div class="page-header">
        <h1 class="page-title">Categories</h1>
        <button @click="openAddModal" class="primary-btn">
          <i class="bi bi-plus-circle"></i>
          Add Category
        </button>
      </div>

      <p v-if="error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 text-sm">{{ error }}</p>
      
<div v-if="isLoading && categories.length === 0" class="text-center p-12 text-gray-500">
        <i class="bi bi-arrow-repeat text-2xl mb-4 block animate-spin"></i>
        <p>Loading categories...</p>
      </div>

      <div v-else-if="categories.length === 0" class="text-center p-12 text-gray-500">
        <i class="bi bi-folder-x text-4xl text-gray-400 mb-3 block"></i>
        <p class="text-sm">No categories yet. Click "Add Category" to get started.</p>
      </div>
      
      <div v-else class="categories-grid">
        <div v-for="category in categories" :key="category.id" class="category-card relative p-6 group">
          <div :class="['category-icon', category.color]">
            <i :class="category.icon"></i>
          </div>
          <div class="category-info">
            <h3>{{ category.name }}</h3>
            <p v-if="category.budget_limit" class="category-amount">
              ₱{{ (category.spent || 0).toFixed(2) }} / ₱{{ parseFloat(category.budget_limit).toFixed(2) }}
            </p>
            <p v-else class="category-amount">
              ₱{{ (category.spent || 0).toFixed(2) }} spent
            </p>
            <div v-if="category.budget_limit" class="progress-bar">
              <div 
                class="progress-fill" 
                :style="{ 
                  width: Math.min((category.spent || 0) / parseFloat(category.budget_limit) * 100, 100) + '%',
                  backgroundColor: (category.spent || 0) / parseFloat(category.budget_limit) > 0.8 ? '#ef4444' : '#8169f1'
                }"
              ></div>
            </div>
          </div>
          <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <button @click="openEditModal(category)" class="w-8 h-8 rounded-md border-none cursor-pointer flex items-center justify-center text-sm bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all duration-200" title="Edit">
              <i class="bi bi-pencil"></i>
            </button>
            <button @click="deleteCategory(category.id)" class="w-8 h-8 rounded-md border-none cursor-pointer flex items-center justify-center text-sm bg-red-50 text-red-600 hover:bg-red-100 transition-all duration-200" title="Delete">
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

onMounted(() => {
  loadCategories();
});
</script>

