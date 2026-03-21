<template>
  <MainLayout>
    <div class="dashboard-container">

      <div class="dashboard-header">
        <h1 class="dashboard-title">Dashboard</h1>
        <div class="user-info">
          <span class="welcome-text">Welcome, {{ user?.name || 'User' }}!</span>
          <button @click="logout" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            Logout
          </button>
        </div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="bi bi-wallet2"></i>
          </div>
          <div class="stat-content">
            <h3>Total Balance</h3>
            <p class="stat-amount">₱{{ totalBalance.toLocaleString() }}</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon income">
            <i class="bi bi-arrow-up-circle"></i>
          </div>
          <div class="stat-content">
            <h3>Income</h3>
            <p class="stat-amount income">₱{{ totalIncome.toLocaleString() }}</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon expense">
            <i class="bi bi-arrow-down-circle"></i>
          </div>
          <div class="stat-content">
            <h3>Expenses</h3>
            <p class="stat-amount expense">₱{{ totalExpenses.toLocaleString() }}</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon savings">
            <i class="bi bi-piggy-bank"></i>
          </div>
          <div class="stat-content">
            <h3>Savings</h3>
            <p class="stat-amount savings">₱{{ savings.toLocaleString() }}</p>
          </div>
        </div>
      </div>


      <div class="charts-section">

        <div class="chart-card">
          <h2>Spending Overview</h2>
          <div class="chart-container">
            <canvas v-if="hasTransactionData" ref="spendingChart"></canvas>
            <div v-else class="empty-state">
              <i class="bi bi-graph-up"></i>
              <p>No spending data available</p>
              <small>Start adding transactions to see your spending overview</small>
            </div>
          </div>
        </div>

        <div class="chart-card">
          <h2>Budget Progress</h2>
          <div class="chart-container">
            <canvas v-if="hasBudgetData" ref="budgetChart"></canvas>
            <div v-else class="empty-state">
              <i class="bi bi-pie-chart"></i>
              <p>No budget data available</p>
              <small>Set up budgets to track your progress</small>
            </div>
          </div>
        </div>
      </div>

      <div class="transactions-section">
        <div class="section-header">
          <h2>Recent Transactions</h2>
          <button class="add-btn">
            <i class="bi bi-plus-circle"></i>
            Add Transaction
          </button>
        </div>
        <div class="transactions-list">
          <div v-if="hasTransactionData" class="transaction-item" v-for="transaction in recentTransactions" :key="transaction.id">
            <div class="transaction-icon" :class="transaction.type">
              <i :class="getTransactionIcon(transaction.category)"></i>
            </div>
            <div class="transaction-details">
              <h4>{{ transaction.description }}</h4>
              <p>{{ transaction.category }} • {{ transaction.date }}</p>
            </div>
            <div class="transaction-amount" :class="transaction.type">
              {{ transaction.type === 'income' ? '+' : '-' }}₱{{ transaction.amount }}
            </div>
          </div>
          <div v-else class="empty-state">
            <i class="bi bi-receipt"></i>
            <p>No transactions yet</p>
            <small>Your recent transactions will appear here</small>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import { useDashboard } from '@/composables/useDashboard';
import MainLayout from './MainLayout.vue';

const {spendingChart, budgetChart, user, totalBalance, totalIncome, totalExpenses, savings, hasTransactionData, hasBudgetData, recentTransactions, logout, getTransactionIcon, loadDashboardData
} = useDashboard();

onMounted(() => {
  console.log('Dashboard mounted, loading data...');
  loadDashboardData();
});
</script>
