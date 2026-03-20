<template>
  <form
    @submit.prevent="signup"
    class="p-6 bg-gray-100 rounded max-w-sm mx-auto"
  >
    <h2 class="text-xl font-bold mb-4">Sign Up</h2>
    <input
      v-model="name"
      type="text"
      placeholder="Name"
      class="mb-2 p-2 border rounded w-full"
    />
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
      class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
    >
      Sign Up
    </button>
    <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>

    <p class="mt-4 text-sm text-gray-600">
      Already have an account?
      <router-link to="/login" class="text-blue-500 hover:underline ml-1">
        Login
      </router-link>
    </p>
  </form>
</template>

<script>
export default {
  data() {
    return {
      name: "",
      email: "",
      password: "",
      error: "",
    };
  },
  methods: {
    async signup() {
      try {
        const res = await fetch(
          "http://localhost:8000/backend/auth/signup.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              name: this.name,
              email: this.email,
              password: this.password,
            }),
          },
        );
        const data = await res.json();
        if (data.success) {
          this.$router.push("/login");
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
