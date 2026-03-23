<template>
  <MainLayout>
    <div class="p-8 max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
        <button class="flex items-center gap-2 px-6 py-3 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors">
          <i class="bi bi-check-circle"></i>
          Save Changes
        </button>
      </div>
      
      <div class="grid grid-cols-1 gap-8">
        <div class="bg-white p-8 rounded-xl shadow-sm flex gap-8">
          <div class="flex flex-col items-center gap-4">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center text-5xl text-gray-600">
              <i class="bi bi-person-circle"></i>
            </div>
            <button class="flex items-center gap-2 px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-600 cursor-pointer transition-all hover:bg-gray-200">
              <i class="bi bi-camera"></i>
              Change Photo
            </button>
          </div>
          
          <div class="flex-1 flex flex-col gap-6">
            <div class="flex flex-col gap-2">
              <label for="name" class="font-semibold text-gray-900">Full Name</label>
              <input type="text" id="name" v-model="user.name" class="px-4 py-3 border border-gray-300 rounded-md text-sm transition-colors focus:outline-none focus:border-purple-500 focus:ring-3 focus:ring-purple-100" />
            </div>
            
            <div class="flex flex-col gap-2">
              <label for="email" class="font-semibold text-gray-900">Email Address</label>
              <input type="email" id="email" v-model="user.email" class="px-4 py-3 border border-gray-300 rounded-md text-sm transition-colors focus:outline-none focus:border-purple-500 focus:ring-3 focus:ring-purple-100" />
            </div>
            
            <div class="flex flex-col gap-2">
              <label for="phone" class="font-semibold text-gray-900">Phone Number</label>
              <input type="tel" id="phone" v-model="user.phone" class="px-4 py-3 border border-gray-300 rounded-md text-sm transition-colors focus:outline-none focus:border-purple-500 focus:ring-3 focus:ring-purple-100" />
            </div>
            
            <div class="flex flex-col gap-2">
              <label for="currency" class="font-semibold text-gray-900">Default Currency</label>
              <select id="currency" v-model="user.currency" class="px-4 py-3 border border-gray-300 rounded-md text-sm bg-white transition-colors focus:outline-none focus:border-purple-500 focus:ring-3 focus:ring-purple-100">
                <option value="PHP">PHP - Philippine Peso</option>
                <option value="USD">USD - US Dollar</option>
                <option value="EUR">EUR - Euro</option>
                <option value="JPY">JPY - Japanese Yen</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue';
import MainLayout from './MainLayout.vue';

const user = ref({ //just sample data
  name: 'Demo User',
  email: 'demo@budgetbuddy.com',
  phone: '+1 (555) 123-4567',
  currency: 'PHP'
});

</script>
