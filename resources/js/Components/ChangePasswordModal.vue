<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xl transition-all duration-300 animate-pop"
        @click.self="$emit('close')"
    >
        <div class="relative w-full max-w-sm rounded-3xl bg-slate-900/95 border border-slate-700/80 p-6 text-left shadow-2xl shadow-black/90 overflow-hidden">
            <!-- Background Glow -->
            <div class="absolute -top-16 -right-16 w-36 h-36 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Close Button -->
            <button
                type="button"
                @click="$emit('close')"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-sm font-black transition-all duration-200 active:scale-95 cursor-pointer"
                aria-label="Close"
            >
                ✕
            </button>

            <!-- Modal Header -->
            <div class="text-center mb-5">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 font-black text-xl mb-2 shadow-lg shadow-amber-500/20">
                    🔑
                </div>
                <h3 class="text-xl font-black text-white tracking-tight">
                    {{ requiresCurrentPassword ? 'Change Password' : 'Set Account Password' }}
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    <span v-if="requiresCurrentPassword">Update your account password for secure access</span>
                    <span v-else>Set or update your password to log in with Email & Password anytime.</span>
                </p>
            </div>

            <!-- Notifications -->
            <div
                v-if="error"
                class="mb-3 p-2.5 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs font-semibold text-center"
            >
                {{ error }}
            </div>
            <div
                v-if="successMsg"
                class="mb-3 p-2.5 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-semibold text-center"
            >
                {{ successMsg }}
            </div>

            <!-- Change/Set Password Form -->
            <form @submit.prevent="handleSubmit" class="space-y-3">
                <!-- Current Password Field (Only shown for non-Google email accounts) -->
                <div v-if="requiresCurrentPassword">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Current Password
                    </label>
                    <input
                        v-model="form.current_password"
                        type="password"
                        required
                        placeholder="••••••••"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs font-medium placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                    />
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        New Password
                    </label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        placeholder="••••••••"
                        minlength="6"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs font-medium placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full py-3 px-4 mt-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer disabled:opacity-50"
                >
                    <span v-if="loading">Saving...</span>
                    <span v-else>{{ requiresCurrentPassword ? 'Update Password' : 'Set Password' }}</span>
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
// YB - 15-09-2026 Change/Set password modal dynamically distinguishing Google-only from password accounts
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const page = usePage();
const user = computed(() => page.props.auth?.user || null);
const authProvider = computed(() => page.props.auth?.auth_provider || 'email');

// Google OAuth active sessions don't need current password verification
const isGoogleSession = computed(() => authProvider.value === 'google');
const requiresCurrentPassword = computed(() => Boolean(user.value?.has_password) && !isGoogleSession.value);

const loading = ref(false);
const error = ref('');
const successMsg = ref('');

const form = ref({
    current_password: '',
    password: '',
});

const getApiUrl = (path) => {
    const pathname = window.location.pathname.replace(/\/+$/, '');
    const cleanPath = path.startsWith('/') ? path : `/${path}`;
    return `${pathname}${cleanPath}`;
};

const handleSubmit = async () => {
    loading.value = true;
    error.value = '';
    successMsg.value = '';

    const payload = {
        password: form.value.password,
    };

    if (requiresCurrentPassword.value) {
        payload.current_password = form.value.current_password;
    }

    try {
        const response = await fetch(getApiUrl('/api/auth/change-password'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            error.value = data.message || 'Failed to update password. Please check your credentials.';
            return;
        }

        successMsg.value = 'Password updated successfully!';
        if (user.value) {
            user.value.has_password = true;
        }

        setTimeout(() => {
            emit('close');
            form.value.current_password = '';
            form.value.password = '';
            error.value = '';
            successMsg.value = '';
        }, 1200);
    } catch (err) {
        error.value = err.message || 'Network error occurred.';
    } finally {
        loading.value = false;
    }
};
</script>
