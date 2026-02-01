<template>
	<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
		<div class="flex items-start justify-between gap-4">
			<div>
				<h2 class="text-2xl font-bold text-gray-900">My Profile</h2>
				<p class="text-sm text-gray-500 mt-1">Account information</p>
			</div>
			<div class="flex items-center gap-2">
				<span
					v-if="authUser"
					:class="[
						'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
						authUser.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700',
					]"
				>
					{{ authUser.is_active ? 'Active' : 'Inactive' }}
				</span>
			</div>
		</div>

		<!-- Cache Status -->
		<div v-if="cacheStatus" class="mt-4 p-3 rounded-lg" :class="[
			cacheStatus.hit ? 'bg-green-50 border border-green-200' : 'bg-blue-50 border border-blue-200'
		]">
			<p class="text-xs font-semibold" :class="cacheStatus.hit ? 'text-green-700' : 'text-blue-700'">
				{{ cacheStatus.hit ? '✓ Cache HIT' : '⚠️ Cache MISS' }}
				<span class="text-gray-600 ml-2">{{ cacheStatus.key }}</span>
			</p>
		</div>

		<div v-if="!authUser" class="py-10 text-center text-gray-600">Loading...</div>

		<div v-else class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
			<section class="rounded-xl bg-gray-50 border border-gray-200 p-5">
				<h3 class="text-sm font-bold text-gray-500">USER</h3>
				<div class="mt-4 space-y-4">
					<div>
						<div class="text-xs font-bold text-gray-400">NAME</div>
						<div class="mt-1 text-lg font-semibold text-gray-900">{{ authUser.name }}</div>
					</div>
					<div>
						<div class="text-xs font-bold text-gray-400">EMAIL</div>
						<div class="mt-1 text-gray-900">{{ authUser.email }}</div>
					</div>
					<div>
						<div class="text-xs font-bold text-gray-400">TEAM</div>
						<div class="mt-1 text-gray-900">{{ authUser.team?.name || 'N/A' }}</div>
					</div>
					<div>
						<div class="text-xs font-bold text-gray-400">ROLES</div>
						<div class="mt-2 flex flex-wrap gap-2">
							<span
								v-for="role in (authUser.roles || [])"
								:key="role.id"
								class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700"
							>
								{{ role.name }}
							</span>
							<span v-if="(authUser.roles || []).length === 0" class="text-sm text-gray-500">No roles</span>
						</div>
					</div>
				</div>
			</section>

			<section class="rounded-xl bg-gray-50 border border-gray-200 p-5">
				<h3 class="text-sm font-bold text-gray-500">PROFILE</h3>
				<div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
					<div>
						<div class="text-xs font-bold text-gray-400">PHONE</div>
						<div class="mt-1 text-gray-900">{{ authUser.profile?.phone || 'N/A' }}</div>
					</div>
					<div>
						<div class="text-xs font-bold text-gray-400">CITY</div>
						<div class="mt-1 text-gray-900">{{ authUser.profile?.city || 'N/A' }}</div>
					</div>
					<div>
						<div class="text-xs font-bold text-gray-400">COUNTRY</div>
						<div class="mt-1 text-gray-900">{{ authUser.profile?.country || 'N/A' }}</div>
					</div>
					<div class="sm:col-span-2">
						<div class="text-xs font-bold text-gray-400">BIO</div>
						<div class="mt-1 text-gray-900">{{ authUser.profile?.bio || 'N/A' }}</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</template>

<script setup>
import { authUser } from '../lib/auth.js';
import { ref, onMounted } from 'vue';

const cacheStatus = ref(null);

onMounted(async () => {
	try {
		const token = localStorage.getItem('token');
		const apiBaseUrl = (import.meta.env?.VITE_API_BASE_URL || '').replace(/\/$/, '');
		
		// Get current authenticated user
		const response = await fetch(apiBaseUrl ? `${apiBaseUrl}/api/auth/me` : '/api/auth/me', {
			headers: {
				'Authorization': `Bearer ${token}`,
				'Accept': 'application/json',
			},
		});

		if (response.ok) {
			const data = await response.json();
			cacheStatus.value = data.cache || null;
			console.log('Cache Status:', cacheStatus.value);
		}
	} catch (error) {
		console.error('Error fetching cache status:', error);
	}
});
</script>

