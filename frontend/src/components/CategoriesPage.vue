<template>
  <MainLayout>
    <div class="page-container">
      <div class="page-header">
        <h1 class="page-title">Categories</h1>
        <button class="primary-btn">
          <i class="bi bi-plus-circle"></i>
          Add Category
        </button>
      </div>
      
      <div class="categories-grid">
        <div class="category-card">
          <div class="category-icon food">
            <i class="bi bi-cup-hot"></i>
          </div>
          <div class="category-info">
            <h3>Food</h3>
            <p class="category-amount">₱350 / ₱500</p>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 70%"></div>
            </div>
          </div>
        </div>

        <div class="category-card">
          <div class="category-icon transport">
            <i class="bi bi-car-front"></i>
          </div>
          <div class="category-info">
            <h3>Transport</h3>
            <p class="category-amount">₱200 / ₱300</p>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 66%"></div>
            </div>
          </div>
        </div>

        <div class="category-card">
          <div class="category-icon shopping">
            <i class="bi bi-bag"></i>
          </div>
          <div class="category-info">
            <h3>Shopping</h3>
            <p class="category-amount">₱150 / ₱400</p>
            <div class="progress-bar">
              <div class="progress-fill" style="width: 37%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from './MainLayout.vue';
</script>
