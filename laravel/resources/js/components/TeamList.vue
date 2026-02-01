<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Teams Management</h2>
      <button
        @click="showCreateForm = true"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition"
      >
        + Create Team
      </button>
    </div>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search teams by name..."
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
      />
    </div>

    <!-- Teams Table -->
    <div v-if="!loading" class="overflow-x-auto bg-white rounded-lg shadow">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Members</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="team in teams" :key="team.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ team.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ team.members_count || 0 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="[
                  'inline-block px-3 py-1 rounded-full text-xs font-semibold',
                  team.is_active
                    ? 'bg-green-100 text-green-800'
                    : 'bg-red-100 text-red-800'
                ]"
              >
                {{ team.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
              <button
                @click="editTeam(team)"
                class="text-blue-600 hover:text-blue-900 font-semibold"
              >
                Edit
              </button>
              <button
                @click="deleteTeam(team.id)"
                class="text-red-600 hover:text-red-900 font-semibold"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
        <button
          @click="previousPage"
          :disabled="currentPage === 1"
          class="px-4 py-2 bg-gray-300 hover:bg-gray-400 disabled:bg-gray-200 text-gray-800 rounded-lg"
        >
          Previous
        </button>
        <span class="text-gray-600">Page {{ currentPage }}</span>
        <button
          @click="nextPage"
          :disabled="!hasNextPage"
          class="px-4 py-2 bg-gray-300 hover:bg-gray-400 disabled:bg-gray-200 text-gray-800 rounded-lg"
        >
          Next
        </button>
      </div>
    </div>

    <div v-else class="text-center py-8">
      <p class="text-gray-600">Loading teams...</p>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      {{ error }}
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">
          {{ editingTeam ? 'Edit Team' : 'Create Team' }}
        </h3>

        <form @submit.prevent="saveTeam" class="space-y-4">
          <div>
            <label class="block text-gray-700 font-semibold mb-2">Name</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              required
            />
          </div>

          <div>
            <label class="block text-gray-700 font-semibold mb-2">Slug</label>
            <input
              v-model="form.slug"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              required
            />
          </div>

          <div>
            <label class="block text-gray-700 font-semibold mb-2">Description</label>
            <textarea
              v-model="form.description"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              rows="3"
            ></textarea>
          </div>

          <div>
            <label class="flex items-center">
              <input v-model="form.is_active" type="checkbox" class="rounded" />
              <span class="ml-2 text-gray-700">Active</span>
            </label>
          </div>

          <div class="flex space-x-2">
            <button
              type="submit"
              class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition"
            >
              Save
            </button>
            <button
              type="button"
              @click="closeForm"
              class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition"
            >
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

const teams = ref([]);
const loading = ref(false);
const error = ref('');
const searchQuery = ref('');
const currentPage = ref(1);
const hasNextPage = ref(false);
const showCreateForm = ref(false);
const editingTeam = ref(null);

const form = ref({
  name: '',
  slug: '',
  description: '',
  is_active: true,
});

const token = localStorage.getItem('token');

onMounted(() => {
  fetchTeams();
});

const fetchTeams = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await fetch(
      `/api/admin/teams?page=${currentPage.value}&search=${searchQuery.value}`,
      {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
        },
      }
    );

    if (!response.ok) {
      error.value = 'Failed to fetch teams';
      return;
    }

    const data = await response.json();
    teams.value = data.data.data;
    hasNextPage.value = data.data.current_page < data.data.last_page;
  } catch (err) {
    error.value = 'Error fetching teams';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const saveTeam = async () => {
  try {
    const url = editingTeam.value
      ? `/api/admin/teams/${editingTeam.value.id}`
      : '/api/admin/teams';

    const method = editingTeam.value ? 'PUT' : 'POST';

    const response = await fetch(url, {
      method,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    });

    if (!response.ok) {
      error.value = 'Failed to save team';
      return;
    }

    closeForm();
    await fetchTeams();
  } catch (err) {
    error.value = 'Error saving team';
    console.error(err);
  }
};

const editTeam = (team) => {
  editingTeam.value = team;
  form.value = {
    name: team.name,
    slug: team.slug,
    description: team.description || '',
    is_active: team.is_active,
  };
  showCreateForm.value = true;
};

const deleteTeam = async (id) => {
  if (!confirm('Are you sure you want to delete this team?')) return;

  try {
    const response = await fetch(`/api/admin/teams/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
      },
    });

    if (!response.ok) {
      error.value = 'Failed to delete team';
      return;
    }

    await fetchTeams();
  } catch (err) {
    error.value = 'Error deleting team';
    console.error(err);
  }
};

const closeForm = () => {
  showCreateForm.value = false;
  editingTeam.value = null;
  form.value = {
    name: '',
    slug: '',
    description: '',
    is_active: true,
  };
};

const nextPage = async () => {
  currentPage.value++;
  await fetchTeams();
};

const previousPage = async () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    await fetchTeams();
  }
};
</script>
