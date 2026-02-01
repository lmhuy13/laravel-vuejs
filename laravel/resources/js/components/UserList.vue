<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Users Management</h2>
      <button @click="showCreateForm = true"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
        + Create User
      </button>
    </div>

    <!-- Search + Per Page -->
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">


      <div class="flex items-center gap-2">
        <label class="text-sm text-gray-600">Per page</label>
        <select v-model.number="perPage"
          class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
          <option :value="20">20</option>
          <option :value="50">50</option>
          <option :value="100">100</option>
        </select>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">

      <h3 class="text-sm font-bold text-gray-800 mb-3">🔍 Filters</h3>
      <div class="grid grid-cols-3 gap-4 items-end">
        <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
        <input v-model="searchQuery" type="text" placeholder="Search users by name or email..."
          class="w-full sm:max-w-xl px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" />

          </div>

           <!-- Team Filter -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Team</label>
          <select v-model="filterTeamId"
            class="w-full px-3 py-2 border-2 border-blue-300 rounded-lg focus:outline-none focus:border-blue-500 text-sm bg-white">
            <option value="">All Teams</option>
            <option v-for="team in teams" :key="team.id" :value="team.id">
              {{ team.name }}
            </option>
          </select>
        </div>

       
      </div>


      <div class="grid grid-cols-3 gap-4 items-end">
       

        <!-- Roles Filter -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Roles</label>
          <div class="border-2 border-blue-300 rounded-lg bg-white p-2">
            <!-- Selected Tags -->
            <div class="flex flex-wrap gap-2 mb-2 min-h-6">
              <span v-for="roleId in filterRoleIds" :key="roleId"
                class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500 text-white rounded-full text-sm font-medium">
                {{roles.find(r => String(r.id) === String(roleId))?.name}}
                <button type="button" @click="filterRoleIds = filterRoleIds.filter(id => id !== roleId)"
                  class="ml-1 hover:text-blue-200 font-bold">
                  ✕
                </button>
              </span>
            </div>

            <!-- Dropdown List -->
            <select @change="(e) => {
              if (e.target.value && !filterRoleIds.includes(e.target.value)) {
                filterRoleIds = [...filterRoleIds, e.target.value];
              }
              e.target.value = '';
            }"
              class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:outline-none focus:border-blue-400">
              <option value="">+ Select role</option>
              <option v-for="role in roles" :key="role.id" :value="role.id"
                :disabled="filterRoleIds.includes(String(role.id))">
                {{ role.name }}
              </option>
            </select>
          </div>
        </div>

        <div>
         <button @click="clearFilters"
          class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition">
          Clear All
        </button>
        </div>  
      </div>

      <!-- Active Filters -->
      <div v-if="filterTeamId || filterRoleIds.length > 0" class="mt-3 pt-3 border-t border-blue-200">
        <p class="text-xs text-gray-700">
          <span class="font-semibold">Active Filters:</span>
          <span v-if="filterTeamId" class="ml-2 inline-block px-2 py-1 bg-blue-200 text-blue-800 rounded text-xs">
            Team: {{teams.find(t => t.id == filterTeamId)?.name}}
          </span>
          <span v-for="roleId in filterRoleIds" :key="roleId"
            class="ml-2 inline-block px-2 py-1 bg-indigo-200 text-indigo-800 rounded text-xs">
            Role: {{roles.find(r => r.id == roleId)?.name}}
          </span>
        </p>
      </div>
    </div>

    <!-- Users Table -->
    <div v-if="!loading" class="overflow-x-auto bg-white rounded-lg shadow">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              <button type="button" @click="toggleSort('name')"
                class="inline-flex items-center gap-1 hover:text-gray-700">
                Name
                <span v-if="sortBy === 'name'" class="text-gray-400">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
              </button>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              <button type="button" @click="toggleSort('team')"
                class="inline-flex items-center gap-1 hover:text-gray-700">
                Team
                <span v-if="sortBy === 'team'" class="text-gray-400">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
              </button>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
              <button type="button" @click="toggleSort('role')"
                class="inline-flex items-center gap-1 hover:text-gray-700">
                Roles
                <span v-if="sortBy === 'role'" class="text-gray-400">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
              </button>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ user.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ user.email }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ user.team?.name || 'N/A' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ formatRoles(user.roles) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="[
                'inline-block px-3 py-1 rounded-full text-xs font-semibold',
                user.is_active
                  ? 'bg-green-100 text-green-800'
                  : 'bg-red-100 text-red-800'
              ]">
                {{ user.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
              <button @click="editUser(user)"
                class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition font-semibold text-xs">
                ✎ Edit
              </button> 

              <button @click="deleteUser(user.id)"
                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 transition font-semibold text-xs">
                🗑 Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
        <button @click="previousPage" :disabled="currentPage === 1"
          class="px-4 py-2 bg-gray-300 hover:bg-gray-400 disabled:bg-gray-200 text-gray-800 rounded-lg">
          Previous
        </button>
        <span class="text-gray-600 font-semibold">Page {{ currentPage }} / {{ lastPage }}</span>
        <button @click="nextPage" :disabled="!hasNextPage"
          class="px-4 py-2 bg-gray-300 hover:bg-gray-400 disabled:bg-gray-200 text-gray-800 rounded-lg">
          Next
        </button>
      </div>
    </div>

    <div v-else class="text-center py-8">
      <p class="text-gray-600">Loading users...</p>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      {{ error }}
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">
          {{ editingUser ? 'Edit User' : 'Create User' }}
        </h3>

        <form @submit.prevent="saveUser" class="space-y-4">
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Name</label>
            <input v-model="form.name" type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              required />
          </div>

          <div>
            <label class="block text-gray-700 font-semibold mb-2">Email</label>
            <input v-model="form.email" type="email"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              required />
          </div>

          <div v-if="!editingUser">
            <label class="block text-gray-700 font-semibold mb-2">Password</label>
            <input v-model="form.password" type="password"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              required />
          </div>

          <div>
            <label class="block text-gray-700 font-semibold mb-2">Team</label>
            <select v-model="form.team_id"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              required>
              <option value="">Select Team</option>
              <option v-for="team in teams" :key="team.id" :value="team.id">
                {{ team.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-gray-700 font-semibold mb-2">Roles</label>
            <div class="border-2 border-gray-300 rounded-lg bg-white p-2">
              <!-- Selected Tags -->
              <div class="flex flex-wrap gap-2 mb-2 min-h-6">
                <span
                  v-for="roleId in form.roles"
                  :key="roleId"
                  class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500 text-white rounded-full text-sm font-medium"
                >
                  {{ roles.find(r => String(r.id) === String(roleId))?.name }}
                  <button
                    type="button"
                    @click="form.roles = form.roles.filter(id => id !== roleId)"
                    class="ml-1 hover:text-blue-200 font-bold"
                  >
                    ✕
                  </button>
                </span>
              </div>

              <!-- Dropdown List -->
              <select
                @change="(e) => {
                  if (e.target.value && !form.roles.includes(e.target.value)) {
                    form.roles = [...form.roles, e.target.value];
                  }
                  e.target.value = '';
                }"
                class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:outline-none focus:border-blue-400"
              >
                <option value="">+ Select role</option>
                <option
                  v-for="role in roles"
                  :key="role.id"
                  :value="role.id"
                  :disabled="form.roles.includes(role.id)"
                >
                  {{ role.name }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="flex items-center">
              <input v-model="form.is_active" type="checkbox" class="rounded" />
              <span class="ml-2 text-gray-700">Active</span>
            </label>
          </div>

          <div class="flex space-x-2">
            <button type="submit"
              class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
              Save
            </button>
            <button type="button" @click="closeForm"
              class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

const apiBaseUrl = (import.meta.env?.VITE_API_BASE_URL || '').replace(/\/$/, '');
const apiUrl = (path) => (apiBaseUrl ? `${apiBaseUrl}${path}` : path);
const getToken = () => localStorage.getItem('token');

const users = ref([]);
const teams = ref([]);
const roles = ref([]);
const loading = ref(false);
const error = ref('');
const searchQuery = ref('');
const filterTeamId = ref('');
const filterRoleIds = ref([]);
const currentPage = ref(1);
const hasNextPage = ref(false);
const lastPage = ref(1);
const perPage = ref(20);
const sortBy = ref('name');
const sortDir = ref('asc');
const showCreateForm = ref(false);
const editingUser = ref(null);

const form = ref({
  name: '',
  email: '',
  password: '',
  team_id: '',
  is_active: true,
  roles: [],
});

onMounted(async () => {
  await fetchUsers();
  await fetchTeams();
  await fetchRoles();
});

watch(perPage, async () => {
  currentPage.value = 1;
  await fetchUsers();
});

let searchDebounceTimer;
watch(searchQuery, () => {
  currentPage.value = 1;
  clearTimeout(searchDebounceTimer);
  searchDebounceTimer = setTimeout(() => {
    fetchUsers();
  }, 300);
});

watch(filterTeamId, () => {
  currentPage.value = 1;
  fetchUsers();
});

watch(filterRoleIds, () => {
  currentPage.value = 1;
  fetchUsers();
});

const toggleSort = async (column) => {
  if (sortBy.value === column) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortBy.value = column;
    sortDir.value = 'asc';
  }

  currentPage.value = 1;
  await fetchUsers();
};

const formatRoles = (userRoles) => {
  if (!Array.isArray(userRoles) || userRoles.length === 0) return '—';
  return userRoles.map((r) => r?.name).filter(Boolean).join(', ') || '—';
};

const fetchUsers = async () => {
  loading.value = true;
  error.value = '';

  try {
    const qs = new URLSearchParams({
      page: String(currentPage.value),
      per_page: String(perPage.value),
      search: searchQuery.value || '',
      sort_by: sortBy.value,
      sort_dir: sortDir.value,
    });

    if (filterTeamId.value) {
      qs.append('team_id', filterTeamId.value);
    }

    if (filterRoleIds.value && filterRoleIds.value.length > 0) {
      filterRoleIds.value.forEach((roleId) => {
        qs.append('role_ids[]', roleId);
      });
    }

    const response = await fetch(
      apiUrl(`/api/admin/users?${qs.toString()}`),
      {
        headers: {
          'Authorization': `Bearer ${getToken()}`,
          'Accept': 'application/json',
        },
      }
    );

    if (!response.ok) {
      error.value = 'Failed to fetch users';
      return;
    }

    const data = await response.json();
    users.value = data.data.data;
    lastPage.value = data.data.last_page;
    hasNextPage.value = data.data.current_page < data.data.last_page;
  } catch (err) {
    error.value = 'Error fetching users';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const fetchTeams = async () => {
  try {
    const response = await fetch(apiUrl('/api/admin/teams?per_page=100'), {
      headers: {
        'Authorization': `Bearer ${getToken()}`,
        'Accept': 'application/json',
      },
    });

    if (response.ok) {
      const data = await response.json();
      teams.value = data.data.data;
    }
  } catch (err) {
    console.error('Error fetching teams:', err);
  }
};

const fetchRoles = async () => {
  try {
    const response = await fetch(apiUrl('/api/admin/roles?per_page=100'), {
      headers: {
        'Authorization': `Bearer ${getToken()}`,
        'Accept': 'application/json',
      },
    });

    if (response.ok) {
      const data = await response.json();
      roles.value = data.data.data;
    }
  } catch (err) {
    console.error('Error fetching roles:', err);
  }
};

const saveUser = async () => {
  try {
    const url = editingUser.value
      ? apiUrl(`/api/admin/users/${editingUser.value.id}`)
      : apiUrl('/api/admin/users');

    const method = editingUser.value ? 'PUT' : 'POST';

    const payload = { ...form.value };
    if (editingUser.value && (!payload.password || payload.password.trim() === '')) {
      delete payload.password;
    }

    const response = await fetch(url, {
      method,
      headers: {
        'Authorization': `Bearer ${getToken()}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
    });

    console.debug('[UserList] saveUser', { url, method, status: response.status });

    if (!response.ok) {
      let message = 'Failed to save user';
      try {
        const data = await response.json();
        message = data?.message || message;
        if (data?.errors && typeof data.errors === 'object') {
          const firstError = Object.values(data.errors).flat()?.[0];
          if (firstError) message = `${message}: ${firstError}`;
        }
      } catch {
        try {
          const text = await response.text();
          if (text) message = `${message}: ${text}`;
        } catch {
          // ignore parse errors
        }
      }

      error.value = message;
      return;
    }

    closeForm();
    await fetchUsers();
  } catch (err) {
    error.value = 'Error saving user';
    console.error(err);
  }
};

const editUser = (user) => {
  editingUser.value = user;
  form.value = {
    name: user.name,
    email: user.email,
    password: '',
    team_id: user.team_id,
    is_active: user.is_active,
    roles: (user.roles || []).map((r) => r.id),
  };
  showCreateForm.value = true;
};

const deleteUser = async (id) => {
  if (!confirm('Are you sure you want to delete this user?')) return;

  try {
    const response = await fetch(apiUrl(`/api/admin/users/${id}`), {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${getToken()}`,
        'Accept': 'application/json',
      },
    });

    if (!response.ok) {
      error.value = 'Failed to delete user';
      return;
    }

    await fetchUsers();
  } catch (err) {
    error.value = 'Error deleting user';
    console.error(err);
  }
};

const closeForm = () => {
  showCreateForm.value = false;
  editingUser.value = null;
  form.value = {
    name: '',
    email: '',
    password: '',
    team_id: '',
    is_active: true,
    roles: [],
  };
};

const nextPage = async () => {
  currentPage.value++;
  await fetchUsers();
};

const previousPage = async () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    await fetchUsers();
  }
};

const clearFilters = () => {
  searchQuery.value = '';
  filterTeamId.value = '';
  filterRoleIds.value = [];
  currentPage.value = 1;
  fetchUsers();
};
</script>
