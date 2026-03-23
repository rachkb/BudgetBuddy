<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeModal">
    <div class="bg-white rounded-xl max-w-lg w-full mx-4" @click.stop>
      <div class="flex justify-between items-center p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Add Transaction</h2>
        <button @click="closeModal" class="p-2 hover:bg-gray-100 rounded-md transition-colors">
          <i class="bi bi-x-lg text-gray-500"></i>
        </button>
      </div>

      <div class="p-6">
        <!-- Transaction Type -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
          <div class="flex gap-2">
            <button 
              :class="['flex-1 py-3 px-4 border-2 rounded-md transition-colors', transactionForm.type === 'expense' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-300 text-gray-700 hover:border-gray-400']"
              @click="transactionForm.type = 'expense'"
            >
              <i class="bi bi-arrow-down-circle mr-2"></i>
              Expense
            </button>
            <button 
              :class="['flex-1 py-3 px-4 border-2 rounded-md transition-colors', transactionForm.type === 'income' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-300 text-gray-700 hover:border-gray-400']"
              @click="transactionForm.type = 'income'"
            >
              <i class="bi bi-arrow-up-circle mr-2"></i>
              Income
            </button>
          </div>
        </div>

        <!-- Amount -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₱</span>
            <input
              v-model="transactionForm.amount"
              type="number"
              step="0.01"
              min="0.01"
              class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              placeholder="0.00"
              required
            />
          </div>
        </div>

        <!-- Description -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
          <input
            v-model="transactionForm.description"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            :placeholder="getTransactionPlaceholder()"
            required
          />
        </div>

        <!-- Category -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
          <div class="flex gap-2">
            <select v-model="transactionForm.category_id" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
              <option value="">Select category</option>
              <option 
                v-for="category in categories" 
                :key="category.id" 
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
            <button 
              @click="openCategoryModal" 
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors"
            >
              <i class="bi bi-plus-circle mr-1"></i>
              New
            </button>
          </div>
        </div>

        <!-- Date -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
          <input
            v-model="transactionForm.date"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            required
          />
        </div>

        <!-- Notes -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
          <textarea
            v-model="transactionForm.notes"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="Add any additional notes..."
          ></textarea>
        </div>

        <!-- Recurring Transaction Options -->
        <div class="mb-6">
          <label class="flex items-center">
            <input
              type="checkbox"
              v-model="transactionForm.is_recurring"
              class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
            />
            <span class="ml-2 text-sm text-gray-700">Make this a recurring transaction</span>
          </label>
        </div>

        <div v-if="transactionForm.is_recurring" class="space-y-4 mb-6 p-4 bg-gray-50 rounded-md">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
            <select v-model="transactionForm.recurring_frequency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="biweekly">Bi-weekly</option>
              <option value="monthly">Monthly</option>
              <option value="quarterly">Quarterly</option>
              <option value="yearly">Yearly</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Repeat every</label>
            <div class="flex items-center gap-2">
              <input
                v-model="transactionForm.recurring_interval"
                type="number"
                min="1"
                class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              />
              <span class="text-sm text-gray-600">
                {{ getFrequencyLabel(transactionForm.recurring_frequency) }}
              </span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">End Date (Optional)</label>
            <input
              v-model="transactionForm.recurring_end_date"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            />
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-md flex items-center">
          <i class="bi bi-exclamation-circle mr-2"></i>
          {{ error }}
        </div>
      </div>

      <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
        <button 
          @click="closeModal" 
          class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
        >
          Cancel
        </button>
        <button 
          @click="handleSubmit" 
          :disabled="isLoading"
          class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <span v-if="isLoading" class="inline-block animate-spin mr-2">⟳</span>
          {{ isLoading ? 'Adding...' : 'Add Transaction' }}
        </button>
      </div>
    </div>
  </div>   <!-- Category Creation Modal -->
    <div v-if="showCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeCategoryModal">
      <div class="bg-white rounded-xl max-w-lg w-full mx-4" @click.stop>
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
          <h3 class="text-xl font-semibold text-gray-900">Create New Category</h3>
          <button @click="closeCategoryModal" class="p-2 hover:bg-gray-100 rounded-md transition-colors">
            <i class="bi bi-x-lg text-gray-500"></i>
          </button>
        </div>

        <div class="p-6">
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Category Type *</label>
            <div class="flex gap-2">
              <button 
                :class="['flex-1 py-3 px-4 border-2 rounded-md transition-colors', categoryForm.type === 'expense' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-300 text-gray-700 hover:border-gray-400']"
                @click="categoryForm.type = 'expense'"
              >
                <i class="bi bi-arrow-down-circle mr-2"></i>
                Expense
              </button>
              <button 
                :class="['flex-1 py-3 px-4 border-2 rounded-md transition-colors', categoryForm.type === 'income' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-300 text-gray-700 hover:border-gray-400']"
                @click="categoryForm.type = 'income'"
              >
                <i class="bi bi-arrow-up-circle mr-2"></i>
                Income
              </button>
            </div>
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
            <input
              v-model="categoryForm.name"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              :placeholder="getCategoryPlaceholder()"
              required
            />
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
            <div class="grid grid-cols-8 gap-2">
              <button
                v-for="icon in getIconOptions()"
                :key="icon"
                :class="['p-3 border-2 rounded-md transition-colors', categoryForm.icon === icon ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-300 text-gray-700 hover:border-gray-400']"
                @click="categoryForm.icon = icon"
              >
                <i :class="icon"></i>
              </button>
            </div>
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
            <div class="flex gap-2">
              <button
                v-for="color in colorOptions"
                :key="color"
                :class="['w-8 h-8 rounded-full border-2 transition-colors', categoryForm.color === color ? 'border-gray-800' : 'border-gray-300']"
                @click="categoryForm.color = color"
              >
                <span :class="['w-full h-full rounded-full', getColorClass(color)]"></span>
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
          <button @click="closeCategoryModal" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
            Cancel
          </button>
          <button @click="handleCreateCategory" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
            Create Category
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, watch } from 'vue';

const props = defineProps({
  show: Boolean,
  transactionForm: Object,
  categoryForm: Object,
  categories: Array,
  isLoading: Boolean,
  error: String,
  showCategoryModal: Boolean
});

const emit = defineEmits([
  'close',
  'submit',
  'open-category-modal',
  'close-category-modal',
  'create-category'
]);

// Icon options for categories
const iconOptions = [
  'bi bi-cup-hot',
  'bi bi-car-front',
  'bi bi-bag',
  'bi bi-controller',
  'bi bi-file-text',
  'bi bi-briefcase',
  'bi bi-laptop',
  'bi bi-graph-up-arrow',
  'bi bi-house',
  'bi bi-heart',
  'bi bi-book',
  'bi bi-phone',
  'bi bi-gift',
  'bi bi-star'
];

// Color options for categories
const colorOptions = [
  'primary',
  'secondary',
  'success',
  'danger',
  'warning',
  'info',
  'dark',
  'light'
];

const closeModal = () => {
  emit('close');
};

const handleSubmit = () => {
  emit('submit');
};

const openCategoryModal = () => {
  emit('open-category-modal');
};

const closeCategoryModal = () => {
  emit('close-category-modal');
};

const handleCreateCategory = () => {
  emit('create-category');
};

const getFrequencyLabel = (frequency) => {
  const labels = {
    daily: 'day(s)',
    weekly: 'week(s)',
    biweekly: 'weeks',
    monthly: 'month(s)',
    quarterly: 'quarter(s)',
    yearly: 'year(s)'
  };
  return labels[frequency] || 'period(s)';
};

const getTransactionPlaceholder = () => {
  return props.transactionForm.type === 'income' 
    ? 'e.g., Monthly Salary, Freelance Work, Investment' 
    : 'e.g., Grocery Shopping, Gas, Coffee Shop';
};

const getCategoryPlaceholder = () => {
  return props.transactionForm.type === 'income'
    ? 'e.g., Salary, Freelance, Investment, Bonus'
    : 'e.g., Groceries, Gas, Entertainment, Utilities';
};

const getIconOptions = () => {
  if (props.categoryForm.type === 'income') {
    return [
      'bi-briefcase', 'bi-cash-stack', 'bi-coin', 'bi-piggy-bank',
      'bi-graph-up', 'bi-credit-card', 'bi-wallet',
      'bi-bank', 'bi-building', 'bi-house', 'bi-gift',
      'bi-trophy', 'bi-star-fill', 'bi-check-circle-fill'
    ];
  } else {
    return [
      'bi-cart', 'bi-cup-hot', 'bi-car-front-fill', 'bi-house-door-fill',
      'bi-heart-fill', 'bi-controller', 'bi-book', 'bi-film',
      'bi-music-note', 'bi-camera', 'bi-telephone-fill', 'bi-tv',
      'bi-bag-fill', 'bi-box', 'bi-truck', 'bi-droplet'
    ];
  }
};

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

// Watch for modal close to reset error
watch(() => props.show, (newShow) => {
  if (!newShow) {
    // Reset error when modal closes
    emit('close');
  }
});

// Watch for transaction type changes to reload categories
watch(() => props.transactionForm.type, (newType) => {
  if (props.show) {
    // Load categories for the new transaction type
    emit('load-categories', newType);
  }
});

// Watch for category type changes to update default icon
watch(() => props.categoryForm.type, (newType) => {
  if (newType === 'income') {
    props.categoryForm.icon = 'bi-briefcase'; // Default income icon
  } else {
    props.categoryForm.icon = 'bi-cart'; // Default expense icon
  }
});
</script>
