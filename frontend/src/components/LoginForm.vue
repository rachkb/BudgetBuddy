<template>
  <form
    @submit.prevent="login"
    class="p-6 bg-gray-100 rounded max-w-sm mx-auto"
  >
    <h2 class="text-xl font-bold mb-4">Login</h2>
    <input
      v-model="email"
      type="email"
      placeholder="Email"
      class="mb-2 p-2 border rounded w-full"
    />
    <input
      v-model="password"
      type="password"
      placeholder="Password"
      class="mb-4 p-2 border rounded w-full"
    />
    <button
      type="submit"
      class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
    >
      Login
    </button>

    <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>

    <!-- Sign Up link/button -->
    <p class="mt-4 text-sm text-gray-600">
      Don't have an account?
      <router-link to="/signup" class="text-blue-500 hover:underline ml-1">
        Sign Up
      </router-link>
    </p>
  </form>
</template>

<script>
export default {
  data() {
    return {
      email: "",
      password: "",
      error: "",
    };
  },
  methods: {
    async login() {
      try {
        const res = await fetch("http://localhost:8000/auth/login.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ email: this.email, password: this.password }),
        });

        const data = await res.json();
        if (data.success) {
          localStorage.setItem("user", JSON.stringify(data.user));
          this.$router.push("/dashboard");
        } else {
          this.error = data.message;
        }
      } catch (err) {
        this.error = "Server error";
        console.error(err);
      }
    },
  },
};
</script>
