<template>
    <div class="relative">
        <!-- Logged In User Button -->
        <div v-if="user" class="flex items-center gap-2">
            <button
                type="button"
                @click="isOpen = !isOpen"
                class="flex items-center gap-2 py-1 px-2.5 rounded-xl bg-slate-950/80 border border-slate-800 hover:border-amber-500/40 text-slate-200 text-xs font-bold transition-all duration-200 active:scale-95 shadow-sm cursor-pointer"
            >
                <img
                    v-if="user.avatar"
                    :src="user.avatar"
                    :alt="user.name"
                    class="w-6 h-6 rounded-lg object-cover ring-1 ring-amber-400/60"
                />
                <div
                    v-else
                    class="w-6 h-6 rounded-lg bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 font-black text-xs flex items-center justify-center shadow-sm"
                >
                    {{ userInitials }}
                </div>
                <span class="max-w-[80px] sm:max-w-[100px] truncate hidden xs:inline">{{ user.name }}</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-black tracking-wider bg-gradient-to-r from-amber-500/20 to-orange-500/20 border border-amber-500/40 text-amber-300 flex items-center gap-1 shadow-sm">
                    <span class="animate-flame inline-block">🔥</span>
                    <span>{{ user.current_streak || 0 }}</span>
                </span>
            </button>

            <!-- Dropdown Menu -->
            <div
                v-if="isOpen"
                @click.self="isOpen = false"
                class="fixed inset-0 z-40"
            ></div>

            <div
                v-if="isOpen"
                class="absolute right-0 top-full mt-2 w-60 rounded-3xl bg-slate-900/95 border border-slate-700/80 shadow-2xl shadow-black/90 backdrop-blur-2xl py-2.5 z-50 animate-pop text-left overflow-hidden"
            >
                <div class="px-4 py-2.5 border-b border-slate-800">
                    <p class="text-xs font-black text-white truncate">{{ user.name }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ user.email }}</p>
                </div>

                <div class="px-4 py-2.5 border-b border-slate-800 text-[11px] text-slate-400 space-y-1.5">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Games Won:</span>
                        <strong class="text-emerald-400 font-mono font-bold">{{ user.games_won || 0 }} / {{ user.games_played || 0 }}</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Best Streak:</span>
                        <strong class="text-amber-400 font-mono font-bold flex items-center gap-1">
                            <span>🔥</span>
                            <span>{{ user.max_streak || 0 }}</span>
                        </strong>
                    </div>
                </div>

                <div class="p-1.5 space-y-1">
                    <button
                        type="button"
                        @click="() => { isOpen = false; $emit('open-change-password'); }"
                        class="w-full text-left px-3 py-2 text-xs font-semibold text-slate-300 hover:text-amber-300 hover:bg-slate-800/80 rounded-xl transition-colors cursor-pointer flex items-center gap-2.5"
                    >
                        <span>🔑</span>
                        <span>Change Password</span>
                    </button>

                    <button
                        type="button"
                        @click="handleLogout"
                        :disabled="loading"
                        class="w-full text-left px-3 py-2 text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 rounded-xl transition-colors cursor-pointer flex items-center gap-2.5"
                    >
                        <span>🚪</span>
                        <span>Sign Out</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Guest Sign In Button -->
        <button
            v-else
            type="button"
            @click="$emit('open-auth')"
            class="py-1.5 px-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs transition-all shadow-md shadow-amber-500/20 hover:scale-105 active:scale-95 cursor-pointer flex items-center gap-1.5"
        >
            <span>👤</span>
            <span class="hidden xs:inline">Sign In</span>
        </button>
    </div>
</template>

<script setup>
// YB - 15-09-2026 User profile & authentication header trigger component
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

defineEmits(['open-auth', 'open-change-password']);

const page = usePage();
const user = computed(() => page.props.auth?.user || null);

const isOpen = ref(false);
const loading = ref(false);

const userInitials = computed(() => {
    if (!user.value || !user.value.name) return 'U';
    return user.value.name.substring(0, 2).toUpperCase();
});

const getApiUrl = (path) => {
    const pathname = window.location.pathname.replace(/\/+$/, '');
    const cleanPath = path.startsWith('/') ? path : `/${path}`;
    return `${pathname}${cleanPath}`;
};

const handleLogout = async () => {
    loading.value = true;
    try {
        localStorage.removeItem('guessx_active_game_id');
        localStorage.removeItem('guessx_game_stats_v1');
        await fetch(getApiUrl('/api/auth/logout'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        window.location.reload();
    } catch (e) {
        localStorage.removeItem('guessx_active_game_id');
        localStorage.removeItem('guessx_game_stats_v1');
        console.error('Logout error', e);
        window.location.reload();
    } finally {
        loading.value = false;
    }
};
</script>
