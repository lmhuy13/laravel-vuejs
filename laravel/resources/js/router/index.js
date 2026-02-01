import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import DashboardLayout from '../components/Dashboard.vue';
import DashboardHome from '../components/DashboardHome.vue';
import ProfileView from '../components/ProfileView.vue';
import UserList from '../components/UserList.vue';
import TeamList from '../components/TeamList.vue';
import RoleList from '../components/RoleList.vue';

const routes = [
  {
    path: '/login',
    component: Login,
    meta: { requiresAuth: false },
  },
  {
    path: '/admin',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', redirect: '/admin/dashboard' },
      { path: 'dashboard', component: DashboardHome },
      { path: 'profile', component: ProfileView },
      { path: 'users', component: UserList, meta: { requiresAdmin: true } },
      { path: 'teams', component: TeamList, meta: { requiresAdmin: true } },
      { path: 'roles', component: RoleList, meta: { requiresAdmin: true } },
    ],
  },
  {
    path: '/',
    redirect: '/login',
  },
  // Back-compat: old route name
  {
    path: '/profile',
    redirect: '/admin/profile',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guard to check authentication
router.beforeEach(async (to, from, next) => {
  const token = localStorage.getItem('token');

  let me = null;
  let meChecked = false;
  const fetchMe = async () => {
    if (meChecked) return me;
    meChecked = true;

    if (!token) return null;

    try {
      const response = await fetch('/api/me', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
        },
      });

      if (!response.ok) return null;

      const data = await response.json();
      me = data?.data ?? null;
      if (me) {
        localStorage.setItem('user', JSON.stringify(me));
      }
      return me;
    } catch {
      return null;
    }
  };

  // If the user navigates to /login with a cached token, validate it first.
  // This prevents getting "stuck" being redirected away from the login page
  // when the token is expired/invalid (common after container resets).
  if (to.path === '/login' && token) {
    const currentUser = await fetchMe();
    if (currentUser) {
      next('/admin');
      return;
    }

    localStorage.removeItem('token');
    localStorage.removeItem('user');
    next();
    return;
  }

  // Enforce login first for protected routes.
  if (to.meta.requiresAuth) {
    if (!token) {
      next('/login');
      return;
    }

    const currentUser = await fetchMe();
    if (!currentUser) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      next('/login');
      return;
    }
  }

  // Admin guard: trust cached user first, fallback to /api/me.
  const needsAdmin = to.matched.some((record) => record.meta?.requiresAdmin);
  if (needsAdmin) {
    let cachedUser = null;
    try {
      cachedUser = JSON.parse(localStorage.getItem('user') || 'null');
    } catch {
      cachedUser = null;
    }

    const hasAdminRole = (u) =>
      Array.isArray(u?.roles) &&
      u.roles.some((role) => ['admin', 'super-admin'].includes(role.slug));

    if (!hasAdminRole(cachedUser)) {
      const currentUser = await fetchMe();
      if (!currentUser) {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        next('/login');
        return;
      }

      if (!hasAdminRole(currentUser)) {
        next('/admin/profile');
        return;
      }
    }
  }

  next();
});

export default router;
