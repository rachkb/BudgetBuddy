<template>
  <div v-if="show" class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h2>{{ isEditing ? 'Edit Category' : 'Add Category' }}</h2>
        <button @click="$emit('close')" class="close-btn">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <form @submit.prevent="$emit('submit')" class="modal-body">
        <div class="form-group">
          <label for="category-name">Category Name *</label>
          <input
            id="category-name"
            v-model="form.name"
            type="text"
            placeholder="e.g., Food, Transport"
            required
            class="form-input"
          />
        </div>

        <div class="form-group">
          <label for="category-icon">Icon</label>
          <select id="category-icon" v-model="form.icon" class="form-input">
            <option v-for="icon in iconOptions" :key="icon.value" :value="icon.value">
              {{ icon.label }}
            </option>
          </select>
          <div class="icon-preview">
            <i :class="form.icon" class="text-2xl"></i>
          </div>
        </div>

        <div class="form-group">
          <label>Color</label>
          <div class="color-grid">
            <button
              v-for="color in colorOptions"
              :key="color.value"
              type="button"
              @click="form.color = color.value"
              :class="['color-option', color.class, { 'selected': form.color === color.value }]"
              :title="color.label"
            >
              <i v-if="form.color === color.value" class="bi bi-check text-white"></i>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label for="budget-limit">
            Budget Limit (Optional)
            <span class="text-xs text-gray-500">Leave empty to track without budgeting</span>
          </label>
          <div class="input-with-prefix">
            <span class="prefix">₱</span>
            <input
              id="budget-limit"
              v-model.number="form.budget_limit"
              type="number"
              step="0.01"
              min="0"
              placeholder="0.00"
              class="form-input with-prefix"
            />
          </div>
        </div>

        <p v-if="error" class="error-message">{{ error }}</p>

        <div class="modal-footer">
          <button type="button" @click="$emit('close')" class="secondary-btn">
            Cancel
          </button>
          <button type="submit" :disabled="isLoading" class="primary-btn">
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
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 1rem;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
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
  transition: color 0.2s;
}

.close-btn:hover {
  color: #1f2937;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #8169f1;
  box-shadow: 0 0 0 3px rgba(129, 105, 241, 0.1);
}

.icon-preview {
  margin-top: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 0.5rem;
}

.color-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.75rem;
}

.color-option {
  width: 100%;
  aspect-ratio: 1;
  border-radius: 0.5rem;
  border: 2px solid transparent;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

.color-option:hover {
  transform: scale(1.05);
}

.color-option.selected {
  border-color: #1f2937;
  box-shadow: 0 0 0 2px white, 0 0 0 4px #1f2937;
}

.input-with-prefix {
  position: relative;
}

.prefix {
  position: absolute;
  left: 0.875rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6b7280;
  font-weight: 500;
}

.form-input.with-prefix {
  padding-left: 2rem;
}

.error-message {
  color: #ef4444;
  font-size: 0.875rem;
  margin-bottom: 1rem;
  padding: 0.75rem;
  background: #fef2f2;
  border-radius: 0.5rem;
  border: 1px solid #fecaca;
}

.modal-footer {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.primary-btn,
.secondary-btn {
  padding: 0.625rem 1.25rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.primary-btn {
  background: linear-gradient(135deg, #8169f1 0%, #9b87f5 100%);
  color: white;
}

.primary-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(129, 105, 241, 0.4);
}

.primary-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.secondary-btn {
  background: #f3f4f6;
  color: #374151;
}

.secondary-btn:hover {
  background: #e5e7eb;
}
</style>
