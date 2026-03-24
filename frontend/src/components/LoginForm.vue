<template>
<div class="min-h-screen w-full flex items-center justify-center p-6 font-sans bg-gradient-to-br from-purple-600 via-purple-400 to-pink-300 relative">
  <div class="w-full max-w-md bg-white p-8 rounded-xl border border-purple-200 shadow-lg">
      <h1 class="flex justify-center text-3xl font-bold text-black mb-2">
        Budget Buddy
      </h1>
      <div class="flex justify-center mb-0">
        <img 
          src="../assets/favicon.png" 
          alt="BudgetBuddy Logo" 
          class="w-32 h-32 object-contain"
        />
      </div>
      
      <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">
        Welcome Back!
      </h2>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div class="relative">
          <span class="absolute left-0 top-0 bottom-0 flex items-center pl-5 text-gray-400 transition-colors">
            <i class="bi bi-envelope text-lg"></i>
          </span>
          <input
            v-model="loginData.email"
            type="email"
            placeholder="Email"
            required
            class="w-full pl-12 pr-5 py-3 bg-white border border-gray-300 rounded-full outline-none transition-all text-sm text-gray-700 shadow-sm focus:border-purple-500 focus:ring-3 focus:ring-purple-100"
          />
        </div>

        <div class="relative">
          <span class="absolute left-0 top-0 bottom-0 flex items-center pl-5 text-gray-400 transition-colors">
            <i class="bi bi-lock text-lg"></i>
          </span>
          <input
            v-model="loginData.password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Password"
            required
            class="w-full pl-12 pr-12 py-3 bg-white border border-gray-300 rounded-full outline-none transition-all text-sm text-gray-700 shadow-sm focus:border-purple-500 focus:ring-3 focus:ring-purple-100"
          />
          <span class="absolute right-0 top-0 bottom-0 flex items-center pr-4 text-gray-400 cursor-pointer transition-colors hover:text-purple-500" @click="togglePassword">
            <i :class="showPassword ? 'bi bi-eye-slash text-lg' : 'bi bi-eye text-lg'"></i>
          </span>
        </div>

        <div class="flex items-center justify-start text-xs text-gray-600 py-2">
          <label class="flex items-center cursor-pointer select-none hover:text-purple-600 transition-colors">
            <input
              type="checkbox"
              class="w-4 h-4 mr-2 rounded border-gray-300 text-purple-500 focus:ring-purple-200 focus:ring-2"
            />
            Remember Me
          </label>
        </div>

        <p
          v-if="error"
          class="text-xs text-red-500 text-center animate-pulse"
        >
          {{ error }}
        </p>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full bg-purple-500 text-white text-sm font-semibold py-3.5 rounded-full transition-all shadow-md hover:bg-purple-600 hover:shadow-lg disabled:opacity-50 disabled:cursor-wait"
        >
          {{ isLoading ? "Logging in..." : "Log In" }}
        </button>
      </form>

      <p class="text-center text-xs text-gray-600 mt-6 tracking-wide">
        Don't have an account?
        <router-link to="/signup" class="text-purple-500 font-semibold ml-1 transition-colors underline hover:text-purple-600">
          Sign Up
        </router-link>
      </p>
  </div>
  </div>
  
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';

const { showPassword, error, isLoading, loginData, togglePassword, handleLogin } = useAuth();
</script>
