<template>
  <div class="container-scroller">
    <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <div class="me-3">
          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
            <span class="icon-menu"></span>
          </button>
        </div>
        <div>
          <a class="navbar-brand brand-logo" href="#">
            <img :src="logoUrl" alt="logo" />
          </a>
          <a class="navbar-brand brand-logo-mini" href="#">
            <img :src="logoMiniUrl" alt="logo" />
          </a>
        </div>
      </div>

      <div class="navbar-menu-wrapper d-flex align-items-top">
        <ul class="navbar-nav">
          <li class="nav-item fw-semibold d-none d-lg-block ms-0">
            <h1 class="welcome-text">
              {{ greeting }},
              <span class="text-black fw-bold">{{ authUser?.name || '...' }}</span>
            </h1>
            <h3 class="welcome-sub-text">Your performance summary this week</h3>
          </li>
        </ul>

        <ul class="navbar-nav ms-auto">
          <li class="nav-item dropdown d-none d-lg-block">
            <a class="nav-link dropdown-bordered dropdown-toggle dropdown-toggle-split" href="#" data-bs-toggle="dropdown" aria-expanded="false">
              Select Category
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0">
              <a class="dropdown-item py-3"><p class="mb-0 fw-medium float-start">Select category</p></a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item preview-item" href="#"><div class="preview-item-content flex-grow py-2"><p class="preview-subject ellipsis fw-medium text-dark">Users</p></div></a>
              <a class="dropdown-item preview-item" href="#"><div class="preview-item-content flex-grow py-2"><p class="preview-subject ellipsis fw-medium text-dark">Teams</p></div></a>
              <a class="dropdown-item preview-item" href="#"><div class="preview-item-content flex-grow py-2"><p class="preview-subject ellipsis fw-medium text-dark">Roles</p></div></a>
            </div>
          </li>

          <li class="nav-item d-none d-lg-block">
            <div id="datepicker-popup" class="input-group date datepicker navbar-date-picker">
              <span class="input-group-addon input-group-prepend border-right">
                <span class="icon-calendar input-group-text calendar-icon"></span>
              </span>
              <input type="text" class="form-control" />
            </div>
          </li>

          <li class="nav-item">
            <form class="search-form" action="#" @submit.prevent>
              <i class="icon-search"></i>
              <input v-model="search" type="search" class="form-control" placeholder="Search Here" title="Search here" />
            </form>
          </li>

          <li class="nav-item dropdown d-none d-lg-block user-dropdown">
            <a class="nav-link" href="#" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="d-flex flex-column align-items-center">
                <span class="small text-muted" style="line-height: 1;">My Profile</span>
                <img class="img-xs rounded-circle mt-1" :src="profileImageUrl" alt="Profile image" />
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown">
              <div class="dropdown-header text-center">
                <img class="img-md rounded-circle" :src="profileImageUrl" alt="Profile image" />
                <p class="mb-1 mt-3 fw-semibold">{{ authUser?.name || '' }}</p>
                <p class="fw-light text-muted mb-0">{{ authUser?.email || '' }}</p>
              </div>
              <router-link class="dropdown-item" to="/admin/profile">
                <i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>
                My Profile
              </router-link>
              <a class="dropdown-item" href="#" @click.prevent="handleLogout">
                <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>
                Sign Out
              </a>
            </div>
          </li>

          <li class="nav-item d-lg-none">
            <button class="navbar-toggler navbar-toggler-right align-self-center" type="button" data-bs-toggle="offcanvas">
              <span class="mdi mdi-menu"></span>
            </button>
          </li>
        </ul>
      </div>
    </nav>

    <div class="container-fluid page-body-wrapper">
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/dashboard">
              <i class="mdi mdi-grid-large menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </router-link>
          </li>

          <li class="nav-item nav-category">User Pages</li>


          <template v-if="isAdmin">
            <li class="nav-item nav-category">Users</li>
            <li class="nav-item">
              <router-link class="nav-link" to="/admin/users">
                <i class="menu-icon mdi mdi-account-multiple"></i>
                <span class="menu-title">Users</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/admin/teams">
                <i class="menu-icon mdi mdi-account-group"></i>
                <span class="menu-title">Teams</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/admin/roles">
                <i class="menu-icon mdi mdi-shield-account"></i>
                <span class="menu-title">Roles</span>
              </router-link>
            </li>
          </template>

          <li class="nav-item nav-category">Theme Admin</li>
          <li class="nav-item">
            <a class="nav-link" href="/admin-theme/" target="_blank">
              <i class="menu-icon mdi mdi-palette"></i>
              <span class="menu-title">Theme Preview</span>
            </a>
          </li>
        </ul>
      </nav>

      <div class="main-panel">
        <div class="content-wrapper">
          <div v-if="!authUser" class="text-center py-5 text-muted">Loading...</div>
          <router-view v-else />
        </div>

        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Laravel + Vue Admin</span>
            <span class="float-none float-sm-end d-block mt-1 mt-sm-0 text-center">{{ today }}</span>
          </div>
        </footer>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

import { authUser, isAdmin, loadCachedUser, refreshMe, logout } from '../lib/auth.js';

const logoUrl = '/admin-theme/assets/images/logo.svg';
const logoMiniUrl = '/admin-theme/assets/images/logo-mini.svg';
const profileImageUrl = '/admin-theme/assets/images/faces/face8.jpg';

const router = useRouter();
const search = ref('');

const today = computed(() => {
  const d = new Date();
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  const dd = String(d.getDate()).padStart(2, '0');
  const yyyy = d.getFullYear();
  return `${mm}/${dd}/${yyyy}`;
});

const greeting = computed(() => {
  const hour = new Date().getHours();
  if (hour >= 5 && hour < 12) {
    return 'Good Morning';
  } else if (hour >= 12 && hour < 17) {
    return 'Good Afternoon';
  } else if (hour >= 17 && hour < 21) {
    return 'Good Evening';
  } else {
    return 'Good Night';
  }
});

onMounted(async () => {
  const token = localStorage.getItem('token');
  if (!token) {
    router.push('/login');
    return;
  }

  loadCachedUser();
  const me = await refreshMe();
  if (!me) {
    handleLogout();
  }
});

const handleLogout = () => {
  logout();
  router.push('/login');
};
</script>
