<template>
  <MainLayout>
    <div class="page-container">
      <div class="page-header">
        <h1 class="page-title">Transactions</h1>
        <button class="primary-btn">
          <i class="bi bi-plus-circle"></i>
          Add Transaction
        </button>
      </div>
      
      <div class="transactions-content">
        <div class="filters-section">
          <div class="filter-group">
            <input type="text" placeholder="Search transactions..." class="search-input" />
            <select class="filter-select">
              <option>All Categories</option>
              <option>Food</option>
              <option>Transport</option>
              <option>Shopping</option>
            </select>
            <select class="filter-select">
              <option>All Time</option>
              <option>This Month</option>
              <option>Last Month</option>
            </select>
          </div>
        </div>

        <div class="transactions-table">
          <div class="empty-state">
            <i class="bi bi-arrow-left-right"></i>
            <h3>No transactions yet</h3>
            <p>Start adding transactions to see them here</p>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from './MainLayout.vue';
</script>
