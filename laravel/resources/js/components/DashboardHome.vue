<template>
	<div class="space-y-6">
		<!-- Top stats row -->
		<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
			<StatCard title="Bounce Rate" :value="stats.bounceRate" delta="-0.5%" deltaTone="down" />
			<StatCard title="Page Views" :value="stats.pageViews" delta="+0.1%" deltaTone="up" />
			<StatCard title="New Sessions" :value="stats.newSessions" delta="-68.8" deltaTone="down" />
			<StatCard title="Avg. Time on Site" :value="stats.avgTime" delta="+0.8%" deltaTone="up" />
			<StatCard title="New Sessions" :value="stats.newSessions" delta="-68.8" deltaTone="down" />
		</div>

		<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
			<!-- Chart placeholder -->
			<section class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
				<div class="flex items-start justify-between gap-4">
					<div>
						<h2 class="text-lg font-semibold text-gray-900">Performance Line Chart</h2>
						<p class="text-sm text-gray-500">This week vs last week</p>
					</div>
					<div class="text-sm text-gray-500">(placeholder)</div>
				</div>
				<div class="mt-6 h-56 rounded-xl bg-gradient-to-b from-blue-50 to-white border border-dashed border-blue-200 flex items-center justify-center text-sm text-blue-700">
					Chart goes here
				</div>
			</section>

			<!-- Status summary -->
			<section class="bg-blue-700 text-white rounded-2xl shadow-sm p-6">
				<div class="flex items-start justify-between">
					<div>
						<h2 class="text-lg font-semibold">Status Summary</h2>
						<p class="text-sm text-blue-100 mt-1">Closed Value</p>
						<div class="text-3xl font-bold mt-4">357</div>
					</div>
					<div class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center">
						<svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M12 20h9" />
							<path d="M12 4h9" />
							<path d="M4 9h7" />
							<path d="M4 15h7" />
						</svg>
					</div>
				</div>
				<div class="mt-6 h-24 rounded-xl bg-white/10 border border-white/10"></div>
			</section>
		</div>

		<!-- Quick profile card -->
		<section class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h2 class="text-lg font-semibold text-gray-900">My Account</h2>
					<p class="text-sm text-gray-500">Quick overview</p>
				</div>
				<router-link to="/admin/profile" class="text-sm font-semibold text-blue-600 hover:text-blue-700">View profile</router-link>
			</div>
			<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
				<div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
					<div class="text-xs font-bold text-gray-400">NAME</div>
					<div class="mt-1 font-semibold text-gray-900">{{ authUser?.name || '-' }}</div>
				</div>
				<div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
					<div class="text-xs font-bold text-gray-400">EMAIL</div>
					<div class="mt-1 font-semibold text-gray-900">{{ authUser?.email || '-' }}</div>
				</div>
				<div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
					<div class="text-xs font-bold text-gray-400">TEAM</div>
					<div class="mt-1 font-semibold text-gray-900">{{ authUser?.team?.name || 'N/A' }}</div>
				</div>
			</div>
		</section>
	</div>
</template>

<script setup>
import { computed } from 'vue';
import { authUser } from '../lib/auth.js';
import StatCard from './StatCard.vue';

const stats = computed(() => ({
	bounceRate: '32.53%',
	pageViews: '7,682',
	newSessions: '68.8',
	avgTime: '2m:35s',
}));
</script>
