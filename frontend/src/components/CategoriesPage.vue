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

      <p v-if="error" class="error-banner">{{ error }}</p>
      
      <div v-if="isLoading && categories.length === 0" class="loading-state">
        <i class="bi bi-arrow-repeat spin"></i>
        <p>Loading categories...</p>
      </div>

      <div v-else-if="categories.length === 0" class="empty-state">
        <i class="bi bi-folder-x"></i>
        <p>No categories yet. Click "Add Category" to get started.</p>
      </div>
      
      <div v-else class="categories-grid">
        <div v-for="category in categories" :key="category.id" class="category-card">
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
          <div class="category-actions">
            <button @click="openEditModal(category)" class="action-btn edit" title="Edit">
              <i class="bi bi-pencil"></i>
            </button>
            <button @click="deleteCategory(category.id)" class="action-btn delete" title="Delete">
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

<style scoped>
.error-banner {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  margin-bottom: 1.5rem;
  font-size: 0.875rem;
}

.loading-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.loading-state i {
  font-size: 2rem;
  margin-bottom: 1rem;
  display: block;
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.empty-state i {
  font-size: 2.5rem;
  color: #9ca3af;
  margin-bottom: 0.75rem;
  display: block;
}

.empty-state p {
  font-size: 0.875rem;
}

.category-card {
  position: relative;
  padding: 1.5rem;
}

.category-actions {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: flex;
  gap: 0.5rem;
  opacity: 0;
  transition: opacity 0.2s;
}

.category-card:hover .category-actions {
  opacity: 1;
}

.action-btn {
  width: 2rem;
  height: 2rem;
  border-radius: 0.375rem;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  font-size: 0.875rem;
}

.action-btn.edit {
  background: #eff6ff;
  color: #2563eb;
}

.action-btn.edit:hover {
  background: #dbeafe;
}

.action-btn.delete {
  background: #fef2f2;
  color: #dc2626;
}

.action-btn.delete:hover {
  background: #fee2e2;
}
</style>
