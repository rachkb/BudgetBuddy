<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black/20 backdrop-blur-md flex items-center justify-center z-[9999] p-4" @click.self="close">
    <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl">
      <!-- Header -->
      <div class="flex justify-between items-center p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Add Transaction</h2>
        <button @click="close" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
          <i class="bi bi-x text-2xl text-gray-600"></i>
        </button>
      </div>

      <!-- Body -->
      <div class="p-6">
        <form @submit.prevent="handleSubmit" class="flex flex-col gap-6">
          <!-- Type Selection -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-gray-700">Type</label>
            <div class="flex gap-3">
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
            </div>
          </div>

          <!-- Amount -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-gray-700">Amount</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">₱</span>
              <input
                v-model="formData.amount"
                type="number"
                step="0.01"
                required
                placeholder="0.00"
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              />
            </div>
          </div>

          <!-- Description -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-gray-700">Description</label>
            <input
              v-model="formData.description"
              type="text"
              required
              placeholder="Enter description"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            />
          </div>

          <!-- Category -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-gray-700">Category</label>
            <div class="flex gap-2">
              <select
                v-model="formData.category_id"
                required
                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white"
              >
                <option value="">Select a category</option>
                <option
                  v-for="category in filteredCategories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
              <button
                type="button"
                @click="openCategoryModal"
                class="px-4 py-3 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors font-medium text-sm whitespace-nowrap"
              >
                <i class="bi bi-plus-lg mr-1"></i>
                New
              </button>
            </div>
          </div>

          <!-- Date -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-gray-700">Date</label>
            <input
              v-model="formData.transaction_date"
              type="date"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            />
          </div>

          <!-- Notes (Optional) -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-gray-700">Notes (Optional)</label>
            <textarea
              v-model="formData.notes"
              rows="3"
              placeholder="Add any additional notes..."
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
            ></textarea>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600">{{ error }}</p>
          </div>

          <!-- Footer Buttons -->
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="close"
              class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isLoading"
              class="flex-1 px-6 py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isLoading" class="flex items-center justify-center gap-2">
                <i class="bi bi-arrow-repeat animate-spin"></i>
                Adding...
              </span>
              <span v-else>Add Transaction</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <CategoryModal
    :isOpen="showCategoryModal"
    :editCategory="null"
    @close="showCategoryModal = false"
    @saved="onCategorySaved"
  />
</template>

<script setup>
import { useAddTransactionModal } from '@/composables/useAddTransactionModal';
import CategoryModal from './CategoryModal.vue';

const {
  isOpen, formData, isLoading, error,
  filteredCategories, showCategoryModal,
  openCategoryModal, onCategorySaved,
  close, handleSubmit
} = useAddTransactionModal();
</script>
