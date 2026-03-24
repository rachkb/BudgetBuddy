<template>
  <MainLayout>
    <div class="p-8 max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
      </div>

      <!-- Period Filter -->
      <div class="bg-white p-4 rounded-xl shadow-sm mb-8">
        <div class="flex gap-3 items-center">
          <label class="text-sm font-medium text-gray-600">Period:</label>
          <select v-model="period" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white">
            <option value="this_month">This Month</option>
            <option value="last_month">Last Month</option>
            <option value="last_3_months">Last 3 Months</option>
            <option value="this_year">This Year</option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="isLoading" class="flex items-center justify-center py-16">
        <div class="text-center text-gray-400">
          <i class="bi bi-arrow-repeat text-3xl mb-2 block animate-spin"></i>
          <p class="text-sm">Loading report...</p>
        </div>
      </div>

      <template v-else>
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white p-6 rounded-xl shadow-sm text-center">
            <h3 class="text-sm text-gray-600 mb-2">Total Income</h3>
            <p class="text-2xl font-bold text-green-600">₱{{ totalIncome.toLocaleString() }}</p>
          </div>
          <div class="bg-white p-6 rounded-xl shadow-sm text-center">
            <h3 class="text-sm text-gray-600 mb-2">Total Expenses</h3>
            <p class="text-2xl font-bold text-red-600">₱{{ totalExpenses.toLocaleString() }}</p>
          </div>
          <div class="bg-white p-6 rounded-xl shadow-sm text-center">
            <h3 class="text-sm text-gray-600 mb-2">Net Savings</h3>
            <p class="text-2xl font-bold" :class="netSavings >= 0 ? 'text-blue-600' : 'text-red-600'">₱{{ netSavings.toLocaleString() }}</p>
          </div>
        </div>

        <!-- Chart + Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Doughnut Chart -->
          <div class="bg-white p-6 rounded-xl shadow-sm">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-lg font-semibold text-gray-900">
                {{ view === 'overview' ? 'Income vs Expenses' : 'Expense Breakdown' }}
              </h2>
              <button
                v-if="view === 'expense_breakdown'"
                @click="goBackToOverview"
                class="flex items-center gap-1 text-sm text-purple-600 hover:text-purple-800 font-medium"
              >
                <i class="bi bi-arrow-left"></i> Back
              </button>
            </div>
            <div class="h-80 relative">
              <canvas v-if="totalIncome > 0 || totalExpenses > 0" ref="chartRef"></canvas>
              <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 text-center">
                <i class="bi bi-pie-chart text-5xl mb-4 opacity-50"></i>
                <p class="font-semibold mb-2">No data for this period</p>
                <small class="text-xs opacity-80">Add transactions to see your report</small>
              </div>
            </div>
            <p v-if="view === 'overview' && (totalIncome > 0 || totalExpenses > 0)" class="text-xs text-gray-400 text-center mt-3">
              <i class="bi bi-hand-index mr-1"></i> Click the Expenses slice to see category breakdown
            </p>
          </div>

          <!-- Category Breakdown List -->
          <div class="bg-white p-6 rounded-xl shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Expense Categories</h2>
            <div v-if="expenseCategoryBreakdown.length > 0" class="flex flex-col gap-3 max-h-96 overflow-y-auto">
              <div
                v-for="cat in expenseCategoryBreakdown"
                :key="cat.id"
                class="flex items-center gap-3 p-3 rounded-lg bg-gray-50"
              >
                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white text-sm" :style="{ backgroundColor: cat.color }">
                  <i :class="cat.icon"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-900 truncate">{{ cat.name }}</span>
                    <span class="text-sm font-semibold text-gray-700">₱{{ cat.total.toLocaleString() }}</span>
                  </div>
                  <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden">
                    <div
                      class="h-full rounded-full"
                      :style="{ width: (totalExpenses > 0 ? (cat.total / totalExpenses * 100) : 0) + '%', backgroundColor: cat.color }"
                    ></div>
                  </div>
                </div>
                <span class="text-xs text-gray-400 w-10 text-right">{{ totalExpenses > 0 ? Math.round(cat.total / totalExpenses * 100) : 0 }}%</span>
              </div>
            </div>
            <div v-else class="flex flex-col items-center justify-center h-64 text-gray-400 text-center">
              <i class="bi bi-tags text-5xl mb-4 opacity-50"></i>
              <p class="font-semibold mb-2">No expenses this period</p>
              <small class="text-xs opacity-80">Expense breakdown will appear here</small>
            </div>
          </div>
        </div>

        <!-- Spending Overview -->
        <div class="bg-white p-6 rounded-xl shadow-sm mt-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Spending Overview</h2>
          <div class="h-80 relative">
            <canvas v-if="totalIncome > 0 || totalExpenses > 0" ref="spendingChartRef"></canvas>
            <div v-else class="flex flex-col items-center justify-center h-full text-gray-400 text-center">
              <i class="bi bi-bar-chart text-5xl mb-4 opacity-50"></i>
              <p class="font-semibold mb-2">No spending data yet</p>
              <small class="text-xs opacity-80">Add transactions to see your monthly overview</small>
            </div>
          </div>
        </div>
      </template>
    </div>
  </MainLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import MainLayout from './MainLayout.vue';
import { useReports } from '@/composables/useReports';

const {
  isLoading, period, chartRef, spendingChartRef, view,
  totalIncome, totalExpenses, netSavings,
  expenseCategoryBreakdown, goBackToOverview,
  loadReportData
} = useReports();

onMounted(() => {
  loadReportData();
});
</script>
