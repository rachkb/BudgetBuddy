<template>
<div class="app-layout">
  <div class="form-card">
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
          <span class="input-icon">
            <i class="bi bi-envelope text-lg"></i>
          </span>
          <input
            v-model="loginData.email"
            type="email"
            placeholder="Email"
            required
            class="input-field"
          />
        </div>

        <div class="relative">
          <span class="input-icon">
            <i class="bi bi-lock text-lg"></i>
          </span>
          <input
            v-model="loginData.password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Password"
            required
            class="input-field pr-10"
          />
          <span class="password-toggle" @click="togglePassword">
            <i :class="showPassword ? 'bi bi-eye-slash text-lg' : 'bi bi-eye text-lg'"></i>
          </span>
        </div>

        <div class="flex items-center justify-start text-xs text-gray-600 py-2">
          <label class="flex items-center cursor-pointer select-none hover:text-primary-600 transition-colors">
            <input
              type="checkbox"
              class="w-4 h-4 mr-2 rounded border-gray-300 text-primary-500 focus:ring-primary-200 focus:ring-2"
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
          class="auth-button"
        >
          {{ isLoading ? "Logging in..." : "Log In" }}
        </button>
      </form>

      <p class="text-center text-xs text-gray-600 mt-6 tracking-wide">
        Don't have an account?
        <router-link to="/signup" class="purple-link">
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
