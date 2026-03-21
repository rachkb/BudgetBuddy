<template>
  <div class="app-layout">
    <div class="form-card">
      <h1 class="flex justify-center text-3xl font-bold text-black">
        Budget Buddy
      </h1>
      <div class="flex justify-center">
        <img 
          src="../assets/favicon.png" 
          alt="BudgetBuddy Logo" 
          class="w-32 h-32 object-contain"
        />
      </div>
      
      <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">
        Create Account
      </h2>

      <form @submit.prevent="handleSignup" class="space-y-4">
        <div class="relative">
          <span class="input-icon">
            <i class="bi bi-person text-lg"></i>
          </span>
          <input
            v-model="signupData.name"
            type="text"
            placeholder="Full Name"
            required
            class="input-field"
          />
        </div>

        <div class="relative">
          <span class="input-icon">
            <i class="bi bi-envelope text-lg"></i>
          </span>
          <input
            v-model="signupData.email"
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
            v-model="signupData.password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Password"
            required
            class="input-field pr-10"
          />
          <span class="password-toggle" @click="togglePassword">
            <i :class="showPassword ? 'bi bi-eye-slash text-lg' : 'bi bi-eye text-lg'"></i>
          </span>
        </div>

        <div class="relative">
          <span class="input-icon">
            <i class="bi bi-lock text-lg"></i>
          </span>
          <input
            v-model="signupData.confirmPassword"
            :type="showConfirmPassword ? 'text' : 'password'"
            placeholder="Confirm Password"
            required
            class="input-field pr-10"
          />
          <span class="password-toggle" @click="toggleConfirmPassword">
            <i :class="showConfirmPassword ? 'bi bi-eye-slash text-lg' : 'bi bi-eye text-lg'"></i>
          </span>
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
          {{ isLoading ? "Creating account..." : "Sign Up" }}
        </button>
      </form>

      <p class="text-center text-xs text-gray-600 mt-6 tracking-wide">
        Already have an account?
        <router-link to="/login" class="purple-link">
          Log In
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';

const { showPassword, showConfirmPassword, error, isLoading, signupData, togglePassword, toggleConfirmPassword, handleSignup 
} = useAuth();
</script>
