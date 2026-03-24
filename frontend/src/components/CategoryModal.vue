<template>
  <div v-if="show" class="fixed inset-0 flex items-center justify-center z-[1000] p-4" style="background: rgba(0, 0, 0, 0.5);" @click.self="$emit('close')">
    <div class="bg-white rounded-xl max-w-[500px] w-full max-h-[90vh] overflow-y-auto shadow-[0_20px_25px_-5px_rgba(0,0,0,0.1)]">
      <div class="flex justify-between items-center p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">{{ isEditing ? 'Edit Category' : 'Add Category' }}</h2>
        <button @click="$emit('close')" class="bg-none border-none text-xl text-gray-500 cursor-pointer p-1 hover:text-gray-900 transition-colors duration-200">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <form @submit.prevent="$emit('submit')" class="p-6">
        <div class="mb-6">
          <label for="category-name" class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
          <input
            id="category-name"
            v-model="form.name"
            type="text"
            placeholder="e.g., Food, Transport"
            required
            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-10"
          />
        </div>

        <div class="mb-6">
          <label for="category-icon" class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
          <select id="category-icon" v-model="form.icon" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-10">
            <option v-for="icon in iconOptions" :key="icon.value" :value="icon.value">
              {{ icon.label }}
            </option>
          </select>
          <div class="mt-2 flex items-center justify-center p-4 bg-gray-50 rounded-lg">
            <i :class="form.icon" class="text-2xl"></i>
          </div>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
          <div class="grid grid-cols-4 gap-3">
            <button
              v-for="color in colorOptions"
              :key="color.value"
              type="button"
              @click="form.color = color.value"
              :class="['w-full aspect-square rounded-lg border-2 border-transparent cursor-pointer transition-all duration-200 flex items-center justify-center text-xl hover:scale-105', color.class, { 'border-gray-900 shadow-[0_0_0_2px_white,0_0_0_4px_gray-900]': form.color === color.value }]"
              :title="color.label"
            >
              <i v-if="form.color === color.value" class="bi bi-check text-white"></i>
            </button>
          </div>
        </div>

        <div class="mb-6">
          <label for="budget-limit" class="block text-sm font-medium text-gray-700 mb-2">
            Budget Limit (Optional)
            <span class="text-xs text-gray-500 ml-1">Leave empty to track without budgeting</span>
          </label>
          <div class="relative">
            <span class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">₱</span>
            <input
              id="budget-limit"
              v-model.number="form.budget_limit"
              type="number"
              step="0.01"
              min="0"
              placeholder="0.00"
              class="w-full pl-8 pr-3.5 py-2.5 border border-gray-300 rounded-lg text-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-10"
            />
          </div>
        </div>

        <p v-if="error" class="text-red-500 text-sm mb-4 p-3 bg-red-50 rounded-lg border border-red-200">{{ error }}</p>

        <div class="flex gap-3 justify-end pt-4 border-t border-gray-200">
          <button type="button" @click="$emit('close')" class="px-5 py-2.5 rounded-lg text-sm font-medium cursor-pointer transition-all duration-200 border-none bg-gray-100 text-gray-700 hover:bg-gray-200">
            Cancel
          </button>
          <button type="submit" :disabled="isLoading" class="px-5 py-2.5 rounded-lg text-sm font-medium cursor-pointer transition-all duration-200 border-none bg-gradient-to-r from-indigo-500 to-indigo-400 text-white hover:translate-y-[-1px] hover:shadow-lg disabled:opacity-60 disabled:cursor-not-allowed">
            {{ isLoading ? 'Saving...' : (isEditing ? 'Update' : 'Add') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  isEditing: Boolean,
  form: Object,
  iconOptions: Array,
  colorOptions: Array,
  error: String,
  isLoading: Boolean
});

defineEmits(['close', 'submit']);
</script>

