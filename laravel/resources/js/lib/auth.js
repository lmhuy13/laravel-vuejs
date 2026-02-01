import { ref, computed } from 'vue';

export const authUser = ref(null);

export const getToken = () => localStorage.getItem('token');

export const isAdmin = computed(() => {
	const roles = authUser.value?.roles;
	if (!Array.isArray(roles)) return false;
	return roles.some((role) => ['admin', 'super-admin'].includes(role.slug));
});

export function loadCachedUser() {
	const raw = localStorage.getItem('user');
	if (!raw) return;
	try {
		authUser.value = JSON.parse(raw);
	} catch {
		// ignore
	}
}

export async function refreshMe() {
	const token = getToken();
	if (!token) return null;

	const response = await fetch('/api/me', {
		headers: {
			Authorization: `Bearer ${token}`,
			Accept: 'application/json',
		},
	});

	if (!response.ok) return null;

	const data = await response.json();
	authUser.value = data.data;
	localStorage.setItem('user', JSON.stringify(data.data));
	return data.data;
}

export function logout() {
	localStorage.removeItem('token');
	localStorage.removeItem('user');
	authUser.value = null;
}
