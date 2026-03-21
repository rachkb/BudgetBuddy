<template>
  <MainLayout>
    <div class="page-container">
      <div class="page-header">
        <h1 class="page-title">Reports</h1>
        <button class="primary-btn">
          <i class="bi bi-download"></i>
          Export Report
        </button>
      </div>
      
      <div class="reports-content">
        <div class="report-filters">
          <div class="filter-group">
            <select class="filter-select">
              <option>This Month</option>
              <option>Last Month</option>
              <option>Last 3 Months</option>
              <option>This Year</option>
            </select>
            <select class="filter-select">
              <option>All Categories</option>
              <option>Food</option>
              <option>Transport</option>
              <option>Shopping</option>
            </select>
            <button class="primary-btn">Generate Report</button>
          </div>
        </div>

        <div class="report-summary">
          <div class="summary-grid">
            <div class="summary-card">
              <h3>Total Income</h3>
              <p class="summary-amount income">₱5,000</p>
            </div>
            <div class="summary-card">
              <h3>Total Expenses</h3>
              <p class="summary-amount expense">₱3,200</p>
            </div>
            <div class="summary-card">
              <h3>Net Savings</h3>
              <p class="summary-amount savings">₱1,800</p>
            </div>
          </div>
        </div>

        <div class="report-charts">
          <div class="chart-card">
            <h2>Monthly Trend</h2>
            <div class="chart-container">
              <div class="empty-state">
                <i class="bi bi-graph-up"></i>
                <p>Chart will appear here</p>
              </div>
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
