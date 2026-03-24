<template>
  <MainLayout>
    <div class="p-8 max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
        <button @click="openCreateModal" class="flex items-center gap-2 px-6 py-3 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
          <i class="bi bi-plus-circle"></i>
          Add Category
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex items-center justify-center py-16">
        <div class="text-center text-gray-400">
          <i class="bi bi-arrow-repeat text-3xl mb-2 block animate-spin"></i>
          <p class="text-sm">Loading categories...</p>
        </div>
      </div>

      <!-- Two-Column Layout -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Expense Categories -->
        <div>
          <div class="flex items-center gap-2 mb-4">
            <div class="w-3 h-3 rounded-full bg-red-500"></div>
            <h2 class="text-lg font-semibold text-gray-900">Expense Categories</h2>
            <span class="text-sm text-gray-500">({{ expenseCategories.length }})</span>
          </div>
          <div v-if="expenseCategories.length > 0" class="flex flex-col gap-4">
            <div
              v-for="category in expenseCategories"
              :key="category.id"
              @click="openEditModal(category)"
              class="bg-white p-5 rounded-xl shadow-sm flex items-center gap-4 cursor-pointer hover:shadow-md transition-shadow"
            >
              <div
                class="w-11 h-11 rounded-lg flex items-center justify-center text-lg text-white"
                :style="{ backgroundColor: getColorHex(category.color) }"
              >
                <i :class="category.icon || 'bi bi-tag'"></i>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 truncate">{{ category.name }}</h3>
                <p v-if="category.budget_limit" class="text-sm text-gray-500">Budget: ₱{{ Number(category.budget_limit).toLocaleString() }}</p>
                <p v-else class="text-xs text-gray-400">No budget set</p>
              </div>
              <div class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-pencil"></i>
              </div>
            </div>
          </div>
          <div v-else class="bg-white rounded-xl shadow-sm p-10 text-center text-gray-400">
            <i class="bi bi-arrow-up-circle text-4xl mb-3 block opacity-50"></i>
            <p class="font-semibold mb-1 text-sm">No expense categories</p>
            <p class="text-xs opacity-80">Add one to start tracking expenses</p>
          </div>
        </div>

        <!-- Income Categories -->
        <div>
          <div class="flex items-center gap-2 mb-4">
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
            <h2 class="text-lg font-semibold text-gray-900">Income Categories</h2>
            <span class="text-sm text-gray-500">({{ incomeCategories.length }})</span>
          </div>
          <div v-if="incomeCategories.length > 0" class="flex flex-col gap-4">
            <div
              v-for="category in incomeCategories"
              :key="category.id"
              @click="openEditModal(category)"
              class="bg-white p-5 rounded-xl shadow-sm flex items-center gap-4 cursor-pointer hover:shadow-md transition-shadow"
            >
              <div
                class="w-11 h-11 rounded-lg flex items-center justify-center text-lg text-white"
                :style="{ backgroundColor: getColorHex(category.color) }"
              >
                <i :class="category.icon || 'bi bi-tag'"></i>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 truncate">{{ category.name }}</h3>
                <p class="text-xs text-gray-400">Income source</p>
              </div>
              <div class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-pencil"></i>
              </div>
            </div>
          </div>
          <div v-else class="bg-white rounded-xl shadow-sm p-10 text-center text-gray-400">
            <i class="bi bi-arrow-down-circle text-4xl mb-3 block opacity-50"></i>
            <p class="font-semibold mb-1 text-sm">No income categories</p>
            <p class="text-xs opacity-80">Add one to start tracking income</p>
          </div>
        </div>
      </div>
    </div>

    <CategoryModal
      :isOpen="showCategoryModal"
      :editCategory="editingCategory"
      @close="showCategoryModal = false"
      @saved="onCategorySaved"
    />
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import MainLayout from './MainLayout.vue';
import CategoryModal from './CategoryModal.vue';
import { useCategories } from '@/composables/useCategories';

const { categories, isLoading, loadCategories } = useCategories();

const showCategoryModal = ref(false);
const editingCategory = ref(null);

const expenseCategories = computed(() => categories.value.filter(c => c.type === 'expense'));
const incomeCategories = computed(() => categories.value.filter(c => c.type === 'income'));

const colorMap = {
  primary: '#8169f1',
  green: '#10b981',
  red: '#ef4444',
  yellow: '#f59e0b',
  blue: '#3b82f6',
  indigo: '#6366f1',
  pink: '#ec4899',
  orange: '#f97316',
  teal: '#14b8a6',
  dark: '#1f2937'
};

const getColorHex = (color) => colorMap[color] || '#8169f1';

const openCreateModal = () => {
  editingCategory.value = null;
  showCategoryModal.value = true;
};

const openEditModal = (category) => {
  editingCategory.value = category;
  showCategoryModal.value = true;
};

const onCategorySaved = () => {
  loadCategories();
};

onMounted(() => {
  loadCategories();
});
</script>
