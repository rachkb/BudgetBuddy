<template>
  <div v-if="show" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2>Add Transaction</h2>
        <button class="close-btn" @click="closeModal">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <div class="modal-body">
        <!-- Transaction Type -->
        <div class="form-group">
          <label class="form-label">Transaction Type</label>
          <div class="type-selector">
            <button 
              :class="['type-btn', { active: transactionForm.type === 'expense' }]"
              @click="transactionForm.type = 'expense'"
            >
              <i class="bi bi-arrow-down-circle"></i>
              Expense
            </button>
            <button 
              :class="['type-btn', { active: transactionForm.type === 'income' }]"
              @click="transactionForm.type = 'income'"
            >
              <i class="bi bi-arrow-up-circle"></i>
              Income
            </button>
          </div>
        </div>

        <!-- Amount -->
        <div class="form-group">
          <label class="form-label">Amount *</label>
          <div class="input-group">
            <span class="input-prefix">₱</span>
            <input
              v-model="transactionForm.amount"
              type="number"
              step="0.01"
              min="0.01"
              class="form-input"
              placeholder="0.00"
              required
            />
          </div>
        </div>

        <!-- Description -->
        <div class="form-group">
          <label class="form-label">Description *</label>
          <input
            v-model="transactionForm.description"
            type="text"
            class="form-input"
            :placeholder="getTransactionPlaceholder()"
            required
          />
        </div>

        <!-- Category -->
        <div class="form-group">
          <label class="form-label">Category</label>
          <div class="category-input-group">
            <select v-model="transactionForm.category_id" class="form-select">
              <option value="">Select category</option>
              <option 
                v-for="category in categories" 
                :key="category.id" 
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
            <button type="button" class="add-category-btn" @click="openCategoryModal">
              <i class="bi bi-plus-circle"></i>
              New
            </button>
          </div>
        </div>

        <!-- Date -->
        <div class="form-group">
          <label class="form-label">Date *</label>
          <input
            v-model="transactionForm.date"
            type="date"
            class="form-input"
            required
          />
        </div>

        <!-- Notes -->
        <div class="form-group">
          <label class="form-label">Notes (Optional)</label>
          <textarea
            v-model="transactionForm.notes"
            class="form-textarea"
            placeholder="Add any additional notes..."
            rows="3"
          ></textarea>
        </div>

        <!-- Recurring Transaction Options -->
        <div class="form-group">
          <label class="checkbox-label">
            <input
              type="checkbox"
              v-model="transactionForm.is_recurring"
              class="checkbox-input"
            />
            <span class="checkbox-text">Make this a recurring transaction</span>
          </label>
        </div>

        <div v-if="transactionForm.is_recurring" class="recurring-options">
          <div class="form-group">
            <label class="form-label">Frequency</label>
            <select v-model="transactionForm.recurring_frequency" class="form-select">
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="biweekly">Bi-weekly</option>
              <option value="monthly">Monthly</option>
              <option value="quarterly">Quarterly</option>
              <option value="yearly">Yearly</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Repeat every</label>
            <div class="interval-input">
              <input
                v-model="transactionForm.recurring_interval"
                type="number"
                min="1"
                class="form-input"
                style="width: 80px;"
              />
              <span class="interval-text">
                {{ getFrequencyLabel(transactionForm.recurring_frequency) }}
              </span>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">End Date (Optional)</label>
            <input
              v-model="transactionForm.recurring_end_date"
              type="date"
              class="form-input"
              :min="transactionForm.date"
            />
            <small class="form-help">Leave blank to repeat indefinitely</small>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="error-message">
          <i class="bi bi-exclamation-circle"></i>
          {{ error }}
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn-secondary" @click="closeModal">
          Cancel
        </button>
        <button 
          class="btn-primary" 
          @click="handleSubmit"
          :disabled="isLoading"
        >
          <i v-if="isLoading" class="bi bi-arrow-repeat spin"></i>
          {{ isLoading ? 'Adding...' : 'Add Transaction' }}
        </button>
      </div>
    </div>

    <!-- Category Creation Modal -->
    <div v-if="showCategoryModal" class="modal-overlay" @click="closeCategoryModal">
      <div class="modal-content category-modal" @click.stop>
        <div class="modal-header">
          <h3>Create New Category</h3>
          <button class="close-btn" @click="closeCategoryModal">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Category Type *</label>
            <div class="type-selector">
              <button 
                :class="['type-btn', { active: categoryForm.type === 'expense' }]"
                @click="categoryForm.type = 'expense'"
              >
                <i class="bi bi-arrow-down-circle"></i>
                Expense
              </button>
              <button 
                :class="['type-btn', { active: categoryForm.type === 'income' }]"
                @click="categoryForm.type = 'income'"
              >
                <i class="bi bi-arrow-up-circle"></i>
                Income
              </button>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Category Name *</label>
            <input
              v-model="categoryForm.name"
              type="text"
              class="form-input"
              :placeholder="getCategoryPlaceholder()"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label">Icon</label>
            <div class="icon-selector">
              <button
                v-for="icon in getIconOptions()"
                :key="icon"
                :class="['icon-btn', { active: categoryForm.icon === icon }]"
                @click="categoryForm.icon = icon"
              >
                <i :class="icon"></i>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Color</label>
            <div class="color-selector">
              <button
                v-for="color in colorOptions"
                :key="color"
                :class="['color-btn', { active: categoryForm.color === color, [`active-${color}`]: categoryForm.color === color }]"
                @click="categoryForm.color = color"
              >
                <span :class="`color-dot ${color}`"></span>
              </button>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-secondary" @click="closeCategoryModal">
            Cancel
          </button>
          <button class="btn-primary" @click="handleCreateCategory">
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

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.category-modal {
  max-width: 400px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #6b7280;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 0.375rem;
  transition: background-color 0.2s;
}

.close-btn:hover {
  background-color: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-textarea {
  resize: vertical;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  font-weight: 500;
  color: #374151;
}

.checkbox-input {
  width: 1.25rem;
  height: 1.25rem;
  accent-color: #3b82f6;
}

.checkbox-text {
  user-select: none;
}

.recurring-options {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 1rem;
  margin-top: 0.5rem;
}

.interval-input {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.interval-text {
  color: #6b7280;
  font-size: 0.875rem;
}

.form-help {
  display: block;
  margin-top: 0.25rem;
  color: #6b7280;
  font-size: 0.75rem;
}

.type-selector {
  display: flex;
  gap: 0.5rem;
}

.type-btn {
  flex: 1;
  padding: 0.75rem;
  border: 2px solid #d1d5db;
  background: white;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-weight: 500;
}

.type-btn:hover {
  border-color: #9ca3af;
}

.type-btn.active {
  border-color: #3b82f6;
  background: #eff6ff;
  color: #3b82f6;
}

.type-btn.expense.active {
  border-color: #ef4444;
  background: #fef2f2;
  color: #ef4444;
}

.type-btn.income.active {
  border-color: #10b981;
  background: #ecfdf5;
  color: #10b981;
}

.input-group {
  display: flex;
  align-items: center;
}

.input-prefix {
  padding: 0.75rem;
  background: #f9fafb;
  border: 1px solid #d1d5db;
  border-right: none;
  border-radius: 0.5rem 0 0 0.5rem;
  color: #6b7280;
  font-weight: 500;
}

.input-group .form-input {
  border-radius: 0 0.5rem 0.5rem 0;
}

.category-input-group {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.category-input-group .form-select {
  flex: 1;
}

.add-category-btn {
  padding: 0.75rem;
  background: #f3f4f6;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background-color 0.2s;
  white-space: nowrap;
}

.add-category-btn:hover {
  background: #e5e7eb;
}

.icon-selector {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
  gap: 0.5rem;
}

.icon-btn {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-btn:hover {
  border-color: #9ca3af;
}

.icon-btn.active {
  border-color: #3b82f6;
  background: #eff6ff;
  color: #3b82f6;
}

.color-selector {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.color-btn {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
}

.color-btn:hover {
  border-color: #9ca3af;
}

.color-btn.active {
  border-color: #374151;
}

.color-dot {
  display: block;
  width: 20px;
  height: 20px;
  border-radius: 50%;
}

.color-dot.primary { background: #3b82f6; }
.color-dot.secondary { background: #6b7280; }
.color-dot.success { background: #10b981; }
.color-dot.danger { background: #ef4444; }
.color-dot.warning { background: #f59e0b; }
.color-dot.info { background: #06b6d4; }
.color-dot.dark { background: #1f2937; }
.color-dot.light { background: #f9fafb; border: 1px solid #d1d5db; }

.error-message {
  padding: 0.75rem;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 0.5rem;
  color: #dc2626;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn-secondary,
.btn-primary {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

.btn-primary {
  background: #3b82f6;
  color: white;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-primary:hover:not(:disabled) {
  background: #2563eb;
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@media (max-width: 640px) {
  .modal-content {
    width: 95%;
    margin: 1rem;
  }
  
  .type-selector {
    flex-direction: column;
  }
  
  .category-input-group {
    flex-direction: column;
  }
  
  .add-category-btn {
    width: 100%;
  }
}
</style>
