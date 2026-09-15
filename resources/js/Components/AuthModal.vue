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
                    X
                </div>
                <h3 class="text-xl font-black text-white tracking-tight">
                    <span v-if="view === 'register'">Create an Account</span>
                    <span v-else-if="view === 'forgot'">Reset Password</span>
                    <span v-else-if="view === 'reset'">Set New Password</span>
                    <span v-else>Welcome Back</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    <span v-if="view === 'forgot'">Enter your email to receive a 6-digit recovery code</span>
                    <span v-else-if="view === 'reset'">Enter the 6-digit code and choose a new password</span>
                    <span v-else>Save your streaks and stats across all your devices</span>
                </p>
            </div>

            <!-- Google Sign-In Button (only on login/register view) -->
            <div v-if="view === 'login' || view === 'register'">
                <a
                    :href="googleAuthUrl"
                    class="w-full py-3 px-4 rounded-xl bg-white hover:bg-slate-100 text-slate-900 font-bold text-xs tracking-wide transition-all shadow-md flex items-center justify-center gap-2.5 cursor-pointer mb-4 hover:scale-[1.01] active:scale-[0.99]"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>

                <!-- Divider -->
                <div class="flex items-center my-4">
                    <div class="flex-1 border-t border-slate-800"></div>
                    <span class="px-3 text-[10px] font-extrabold uppercase tracking-widest text-slate-500">or with email</span>
                    <div class="flex-1 border-t border-slate-800"></div>
                </div>
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

            <!-- VIEW: LOGIN OR REGISTER -->
            <form v-if="view === 'login' || view === 'register'" @submit.prevent="handleSubmit" class="space-y-3">
                <div v-if="view === 'register'">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Your Name
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="John Doe"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs font-medium placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                    />
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Email Address
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        placeholder="you@example.com"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs font-medium placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                    />
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Password
                        </label>
                        <button
                            v-if="view === 'login'"
                            type="button"
                            @click="switchView('forgot')"
                            class="text-[11px] font-medium text-amber-400 hover:text-amber-300 transition-colors cursor-pointer"
                        >
                            Forgot Password?
                        </button>
                    </div>
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
                    <span v-if="loading">Please wait...</span>
                    <span v-else>{{ view === 'register' ? 'Create Account' : 'Sign In' }}</span>
                </button>
            </form>

            <!-- VIEW: FORGOT PASSWORD -->
            <form v-else-if="view === 'forgot'" @submit.prevent="handleForgotPassword" class="space-y-3">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Registered Email Address
                    </label>
                    <input
                        v-model="forgotForm.email"
                        type="email"
                        required
                        placeholder="you@example.com"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs font-medium placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full py-3 px-4 mt-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer disabled:opacity-50"
                >
                    <span v-if="loading">Sending Code...</span>
                    <span v-else>Get 6-Digit Reset Code</span>
                </button>
            </form>

            <!-- VIEW: RESET PASSWORD CONFIRMATION -->
            <form v-else-if="view === 'reset'" @submit.prevent="handleResetPassword" class="space-y-3">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        6-Digit Reset Code
                    </label>
                    <input
                        v-model="resetForm.token"
                        type="text"
                        required
                        maxlength="6"
                        placeholder="123456"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-center tracking-widest text-lg font-mono font-black placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                    />
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        New Password
                    </label>
                    <input
                        v-model="resetForm.password"
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
                    <span v-if="loading">Updating...</span>
                    <span v-else>Reset Password & Log In</span>
                </button>
            </form>

            <!-- Switch Mode & Guest Option -->
            <div class="mt-4 pt-4 border-t border-slate-800/80 text-center space-y-2">
                <div v-if="view === 'forgot' || view === 'reset'">
                    <button
                        type="button"
                        @click="switchView('login')"
                        class="text-xs text-amber-400 hover:text-amber-300 font-semibold transition-colors cursor-pointer"
                    >
                        ← Back to Sign In
                    </button>
                </div>
                <div v-else>
                    <button
                        type="button"
                        @click="switchView(view === 'register' ? 'login' : 'register')"
                        class="text-xs text-slate-400 hover:text-white transition-colors cursor-pointer"
                    >
                        <span v-if="view === 'register'">Already have an account? <strong class="text-amber-400">Sign In</strong></span>
                        <span v-else>Don't have an account? <strong class="text-amber-400">Create One</strong></span>
                    </button>
                </div>

                <div>
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="text-[11px] text-slate-500 hover:text-slate-300 transition-colors cursor-pointer"
                    >
                        Continue playing as guest
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// YB - 15-09-2026 Authentication modal supporting Login, Registration, Google OAuth, and Forgot/Reset Password
import { ref, computed } from 'vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'auth-success']);

// Views: 'login' | 'register' | 'forgot' | 'reset'
const view = ref('login');
const loading = ref(false);
const error = ref('');
const successMsg = ref('');

const form = ref({
    name: '',
    email: '',
    password: '',
});

const forgotForm = ref({
    email: '',
});

const resetForm = ref({
    token: '',
    password: '',
});

const switchView = (newView) => {
    view.value = newView;
    error.value = '';
    successMsg.value = '';
};

// Dynamic Google OAuth URL accounting for subfolders
const googleAuthUrl = computed(() => {
    const basePath = window.location.pathname.replace(/\/+$/, '');
    return `${basePath}/auth/google`;
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

    const endpoint = view.value === 'register' ? '/api/auth/register' : '/api/auth/login';

    try {
        const response = await fetch(getApiUrl(endpoint), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(form.value),
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            error.value = data.message || 'Authentication failed. Please check your credentials.';
            return;
        }

        localStorage.removeItem('guessx_active_game_id');
        localStorage.removeItem('guessx_game_stats_v1');
        emit('auth-success', data.user);
        emit('close');
        window.location.reload();
    } catch (err) {
        error.value = err.message || 'Network error occurred. Please try again.';
    } finally {
        loading.value = false;
    }
};

const handleForgotPassword = async () => {
    loading.value = true;
    error.value = '';
    successMsg.value = '';

    try {
        const response = await fetch(getApiUrl('/api/auth/forgot-password'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(forgotForm.value),
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            error.value = data.message || 'Could not process password reset request.';
            return;
        }

        successMsg.value = data.message;
        if (data.debug_token) {
            resetForm.value.token = data.debug_token;
        }
        view.value = 'reset';
    } catch (err) {
        error.value = err.message || 'Network error occurred.';
    } finally {
        loading.value = false;
    }
};

const handleResetPassword = async () => {
    loading.value = true;
    error.value = '';
    successMsg.value = '';

    try {
        const response = await fetch(getApiUrl('/api/auth/reset-password'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                email: forgotForm.value.email,
                token: resetForm.value.token,
                password: resetForm.value.password,
            }),
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            error.value = data.message || 'Password reset failed. Invalid or expired code.';
            return;
        }

        localStorage.removeItem('guessx_active_game_id');
        localStorage.removeItem('guessx_game_stats_v1');
        emit('auth-success', data.user);
        emit('close');
        window.location.reload();
    } catch (err) {
        error.value = err.message || 'Network error occurred.';
    } finally {
        loading.value = false;
    }
};
</script>
