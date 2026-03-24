<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black/20 backdrop-blur-md flex items-center justify-center z-[9999] p-4" @click.self="closeModal">
    <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl">
      <!-- Header -->
      <div class="flex justify-between items-center p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">{{ isEditing ? 'Edit Category' : 'Add Category' }}</h2>
        <button @click="closeModal" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
          <i class="bi bi-x text-2xl text-gray-600"></i>
        </button>
      </div>

      <!-- Body -->
      <div class="p-6">
        <form @submit.prevent="handleSubmit" class="flex flex-col gap-6">
          <!-- Type Selection -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-gray-700">Category Type</label>
            <div class="flex gap-3">
              <button
                type="button"
                @click="formData.type = 'expense'"
                :class="[
                  'flex-1 py-3 px-4 rounded-lg border-2 font-medium transition-all',
                  formData.type === 'expense'
                    ? 'border-red-500 bg-red-50 text-red-700'
                    : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'
                ]"
              >
                <i class="bi bi-arrow-up-circle mr-2"></i>
                Expense
              </button>
              <button
                type="button"
                @click="formData.type = 'income'"
                :class="[
                  'flex-1 py-3 px-4 rounded-lg border-2 font-medium transition-all',
                  formData.type === 'income'
                    ? 'border-green-500 bg-green-50 text-green-700'
                    : 'border-gray-300 bg-white text-gray-700 hover:border-gray-400'
                ]"
              >
                <i class="bi bi-arrow-down-circle mr-2"></i>
                Income
              </button>
            </div>
          </div>

          <!-- Category Name -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-gray-700">Category Name</label>
            <input
              v-model="formData.name"
              type="text"
              required
              :placeholder="namePlaceholder"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            />
          </div>

          <!-- Icon Selection -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-gray-700">Icon</label>
            <div class="grid grid-cols-8 gap-2">
              <button
                v-for="icon in iconOptions"
                :key="icon"
                type="button"
                @click="formData.icon = icon"
                :class="[
                  'p-3 rounded-lg border-2 text-lg flex items-center justify-center transition-all',
                  formData.icon === icon
                    ? 'border-purple-500 bg-purple-50 text-purple-700'
                    : 'border-gray-200 text-gray-600 hover:border-gray-300'
                ]"
              >
                <i :class="icon"></i>
              </button>
            </div>
          </div>

          <!-- Color Selection -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-gray-700">Color</label>
            <div class="flex flex-wrap gap-3">
              <button
                v-for="color in colorOptions"
                :key="color.value"
                type="button"
                @click="formData.color = color.value"
                :class="[
                  'w-10 h-10 rounded-full border-2 transition-all hover:scale-110',
                  formData.color === color.value
                    ? 'border-gray-900 ring-2 ring-offset-2 ring-gray-400'
                    : 'border-transparent'
                ]"
                :style="{ backgroundColor: color.hex }"
              ></button>
            </div>
          </div>

          <!-- Budget Limit (expense only) -->
          <div v-if="showBudgetLimit" class="flex flex-col gap-2">
            <label class="text-sm font-medium text-gray-700">Budget Limit (Optional)</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">₱</span>
              <input
                v-model="formData.budget_limit"
                type="number"
                step="0.01"
                placeholder="0.00"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              />
            </div>
            <p class="text-xs text-gray-500">Set a monthly spending limit for this category</p>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600">{{ error }}</p>
          </div>

          <!-- Footer Buttons -->
          <div class="flex gap-3 pt-4">
            <button
              v-if="isEditing"
              type="button"
              @click="handleDelete"
              :disabled="isLoading"
              class="px-6 py-3 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition-colors disabled:opacity-50"
            >
              <i class="bi bi-trash mr-2"></i>
              Delete
            </button>
            <div class="flex-1"></div>
            <button
              type="button"
              @click="closeModal"
              class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isLoading"
              class="px-6 py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isLoading" class="flex items-center gap-2">
                <i class="bi bi-arrow-repeat animate-spin"></i>
                Saving...
              </span>
              <span v-else>{{ isEditing ? 'Update' : 'Create' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCategoryModal } from '@/composables/useCategoryModal';

const props = defineProps({
  isOpen: { type: Boolean, default: false },
  editCategory: { type: Object, default: null }
});

const emit = defineEmits(['close', 'saved']);

const {
  isEditing, formData, isLoading, error,
  iconOptions, namePlaceholder, showBudgetLimit, colorOptions,
  closeModal, handleSubmit, handleDelete
} = useCategoryModal(props, emit);
</script>
