<template>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img :src="logoUrl" alt="logo" />
              </div>
              <h4>Hello! let's get started</h4>
              <h6 class="fw-light">Sign in to continue.</h6>

              <div v-if="error" class="alert alert-danger mt-3" role="alert">
                {{ error }}
              </div>

              <form class="pt-3" @submit.prevent="handleLogin">
                <div class="form-group">
                  <input
                    v-model.trim="form.email"
                    type="email"
                    class="form-control form-control-lg"
                    autocomplete="username"
                    placeholder="Email"
                    required
                  />
                </div>

                <div class="form-group">
                  <input
                    v-model="form.password"
                    type="password"
                    class="form-control form-control-lg"
                    autocomplete="current-password"
                    placeholder="Password"
                    required
                  />
                </div>

                <div class="mt-3 d-grid gap-2">
                  <button
                    type="submit"
                    class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn"
                    :disabled="loading"
                  >
                    <span v-if="!loading">SIGN IN</span>
                    <span v-else>Signing in...</span>
                  </button>
                </div>

                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input v-model="remember" type="checkbox" class="form-check-input" /> Keep me signed in
                    </label>
                  </div>
                  <a href="#" class="auth-link text-black" @click.prevent>Forgot password?</a>
                </div>

                <div class="mt-4">
                  <small class="text-muted d-block">Demo credentials:</small>
                  <small class="text-muted d-block">Email: superadmin@example.com</small>
                  <small class="text-muted d-block">Password: password</small>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const logoUrl = '/admin-theme/assets/images/logo.svg';

const router = useRouter();
const loading = ref(false);
const error = ref('');
const remember = ref(true);

const form = ref({
  email: '',
  password: '',
});

const handleLogin = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await fetch('/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    });

    const data = await response.json();

    if (!response.ok) {
      error.value = data.message || 'Login failed';
      return;
    }

    const token = data?.data?.token;
    const user = data?.data?.user;
    if (!token || !user) {
      error.value = 'Unexpected login response';
      return;
    }

    // Store token and user
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));

    // Redirect to admin dashboard
    router.push('/admin');
  } catch (err) {
    error.value = 'Connection error. Please try again.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};
</script>
