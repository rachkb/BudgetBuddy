import { ref } from "vue";
import { useRouter } from "vue-router";

export function useAuth() {
  const router = useRouter();
  const isLogin = ref(true);
  const showPassword = ref(false);
  const showConfirmPassword = ref(false);
  const error = ref("");
  const isLoading = ref(false);

  const loginData = ref({
    email: "",
    password: "",
  });

  const signupData = ref({
    name: "",
    email: "",
    password: "",
    confirmPassword: "",
  });

  const togglePassword = () => {
    showPassword.value = !showPassword.value;
  };

  const toggleConfirmPassword = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
  };

  const toggleForm = () => {
    isLogin.value = !isLogin.value;
    error.value = "";
    loginData.value = { email: "", password: "" };
    signupData.value = { name: "", email: "", password: "", confirmPassword: "" };
  };

  const handleLogin = async () => {
    error.value = "";
    isLoading.value = true;

    try {
      const res = await fetch("http://localhost:8000/backend/auth/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          email: loginData.value.email,
          password: loginData.value.password,
        }),
      });

      const data = await res.json();

      if (data.success) {
        localStorage.setItem("user", JSON.stringify(data.user));
        router.push("/dashboard");
      } else {
        error.value = data.error || "Invalid credentials";
      }
    } catch (err) {
      error.value = "Connection failed. Check your local PHP server.";
      console.error(err);
    } finally {
      isLoading.value = false;
    }
  };

  const handleSignup = async () => {
    if (signupData.value.password !== signupData.value.confirmPassword) {
      error.value = "Passwords do not match";
      return;
    }

    error.value = "";
    isLoading.value = true;

    try {
      const res = await fetch("http://localhost:8000/backend/auth/signup.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name: signupData.value.name,
          email: signupData.value.email,
          password: signupData.value.password,
        }),
      });

      const data = await res.json();

      if (data.success) {
        localStorage.setItem("user", JSON.stringify(data.user));
        router.push("/dashboard");
      } else {
        error.value = data.error || "Registration failed";
      }
    } catch (err) {
      error.value = "Connection failed. Check your local PHP server.";
      console.error(err);
    } finally {
      isLoading.value = false;
    }
  };

  return {
    isLogin,
    showPassword,
    showConfirmPassword,
    error,
    isLoading,
    loginData,
    signupData,
    togglePassword,
    toggleConfirmPassword,
    toggleForm,
    handleLogin,
    handleSignup,
  };
}
