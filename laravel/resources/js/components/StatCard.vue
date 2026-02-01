<template>
	<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
		<div class="text-sm text-gray-500">{{ title }}</div>
		<div class="mt-2 text-3xl font-bold text-gray-900">{{ value }}</div>
		<div
			class="mt-2 text-sm font-semibold"
			:class="deltaTone === 'up' ? 'text-emerald-600' : 'text-red-600'"
		>
			{{ delta }}
		</div>
	</div>
</template>

<script setup>
defineProps({
	title: { type: String, required: true },
	value: { type: String, required: true },
	delta: { type: String, required: true },
	deltaTone: { type: String, default: 'up' },
});
</script>
