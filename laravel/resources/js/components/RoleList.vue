<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Roles Management</h2>
      <button @click="openCreate"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
        + Create Role
      </button>
    </div>

    <div class="mb-4">
      <input v-model="searchQuery" @keyup.enter="search" type="text"
        placeholder="Search roles by name or slug... (press Enter)"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" />
    </div>

    <div v-if="!loading" class="overflow-x-auto bg-white rounded-lg shadow">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ role.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ role.slug }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="[
                'inline-block px-3 py-1 rounded-full text-xs font-semibold',
                role.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
              ]">
                {{ role.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm flex gap-2">
              <button @click="editRole(role)"
                class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition font-semibold text-xs">
                ✎ Edit
              </button>
              <button @click="deleteRole(role.id)"
                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 transition font-semibold text-xs">
                🗑 Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
        <button @click="previousPage" :disabled="currentPage === 1"
          class="px-4 py-2 bg-gray-300 hover:bg-gray-400 disabled:bg-gray-200 text-gray-800 rounded-lg">
          Previous
        </button>
        <span class="text-gray-600">Page {{ currentPage }}</span>
        <button @click="nextPage" :disabled="!hasNextPage"
          class="px-4 py-2 bg-gray-300 hover:bg-gray-400 disabled:bg-gray-200 text-gray-800 rounded-lg">
          Next
        </button>
      </div>
    </div>

    <div v-else class="text-center py-8">
      <p class="text-gray-600">Loading roles...</p>
    </div>

    <div v-if="error" class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      {{ error }}
    </div>

    <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">{{ editingRole ? 'Edit Role' : 'Create Role' }}</h3>

        <form @submit.prevent="saveRole" class="space-y-4">
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Name</label>
            <input v-model="form.name" type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              required />
          </div>

          <div>
            <label class="block text-gray-700 font-semibold mb-2">Slug</label>
            <input v-model="form.slug" type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              required />
          </div>

          <div>
            <label class="block text-gray-700 font-semibold mb-2">Description</label>
            <textarea v-model="form.description"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              rows="3"></textarea>
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
import { ref, onMounted } from 'vue';

const roles = ref([]);
const loading = ref(false);
const error = ref('');
const searchQuery = ref('');
const currentPage = ref(1);
const hasNextPage = ref(false);
const showForm = ref(false);
const editingRole = ref(null);

const form = ref({
  name: '',
  slug: '',
  description: '',
  is_active: true,
});

const token = () => localStorage.getItem('token');

onMounted(() => {
  fetchRoles();
});

const fetchRoles = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await fetch(
      `/api/admin/roles?page=${currentPage.value}&search=${encodeURIComponent(searchQuery.value)}`,
      {
        headers: {
          'Authorization': `Bearer ${token()}`,
          'Accept': 'application/json',
        },
      }
    );

    if (!response.ok) {
      error.value = 'Failed to fetch roles';
      return;
    }

    const data = await response.json();
    roles.value = data.data.data;
    hasNextPage.value = data.data.current_page < data.data.last_page;
  } catch (err) {
    error.value = 'Error fetching roles';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const search = async () => {
  currentPage.value = 1;
  await fetchRoles();
};

const openCreate = () => {
  editingRole.value = null;
  form.value = {
    name: '',
    slug: '',
    description: '',
    is_active: true,
  };
  showForm.value = true;
};

const saveRole = async () => {
  try {
    const url = editingRole.value
      ? `/api/admin/roles/${editingRole.value.id}`
      : '/api/admin/roles';

    const method = editingRole.value ? 'PUT' : 'POST';

    const response = await fetch(url, {
      method,
      headers: {
        'Authorization': `Bearer ${token()}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    });

    if (!response.ok) {
      error.value = 'Failed to save role';
      return;
    }

    closeForm();
    await fetchRoles();
  } catch (err) {
    error.value = 'Error saving role';
    console.error(err);
  }
};

const editRole = (role) => {
  editingRole.value = role;
  form.value = {
    name: role.name,
    slug: role.slug,
    description: role.description || '',
    is_active: role.is_active,
  };
  showForm.value = true;
};

const deleteRole = async (id) => {
  if (!confirm('Are you sure you want to delete this role?')) return;

  try {
    const response = await fetch(`/api/admin/roles/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token()}`,
        'Accept': 'application/json',
      },
    });

    if (!response.ok) {
      error.value = 'Failed to delete role';
      return;
    }

    await fetchRoles();
  } catch (err) {
    error.value = 'Error deleting role';
    console.error(err);
  }
};

const closeForm = () => {
  showForm.value = false;
  editingRole.value = null;
};

const nextPage = async () => {
  currentPage.value++;
  await fetchRoles();
};

const previousPage = async () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    await fetchRoles();
  }
};
</script>
