<template>
  <MainLayout>
    <div class="p-8 max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
        <button class="flex items-center gap-2 px-6 py-3 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
          <i class="bi bi-download"></i>
          Export Report
        </button>
      </div>
      
      <div class="flex flex-col gap-8">
        <div class="bg-white p-6 rounded-xl shadow-sm mb-8">
          <div class="flex gap-4 items-center">
            <select class="px-4 py-2 border border-gray-300 rounded-md text-sm min-w-40">
              <option>This Month</option>
              <option>Last Month</option>
              <option>Last 3 Months</option>
              <option>This Year</option>
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-md text-sm min-w-40">
              <option>All Categories</option>
              <option>Food</option>
              <option>Transport</option>
              <option>Shopping</option>
            </select>
            <button class="flex items-center gap-2 px-6 py-3 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">Generate Report</button>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm mb-8">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm text-center">
              <h3 class="text-sm text-gray-600 mb-2">Total Income</h3>
              <p class="text-2xl font-bold text-green-600">₱5,000</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm text-center">
              <h3 class="text-sm text-gray-600 mb-2">Total Expenses</h3>
              <p class="text-2xl font-bold text-red-600">₱3,200</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm text-center">
              <h3 class="text-sm text-gray-600 mb-2">Net Savings</h3>
              <p class="text-2xl font-bold text-blue-600">₱1,800</p>
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm">
          <div class="bg-white p-6 rounded-xl shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Monthly Trend</h2>
            <div class="h-80 relative">
              <div class="flex flex-col items-center justify-center h-full text-gray-400 text-center">
                <i class="bi bi-graph-up text-5xl mb-4 opacity-50"></i>
                <p class="font-semibold mb-2">Chart will appear here</p>
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
