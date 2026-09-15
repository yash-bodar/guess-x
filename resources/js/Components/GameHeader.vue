<template>
    <header class="w-full border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-xl sticky top-0 z-40">
        <div class="max-w-lg mx-auto flex items-center justify-between py-2.5 px-3 sm:px-4">
            <!-- Left Action Buttons -->
            <div class="flex items-center gap-1.5 sm:gap-2">
                <button
                    type="button"
                    @click="$emit('open-help')"
                    class="w-9 h-9 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-300 hover:text-amber-400 hover:border-amber-500/40 hover:bg-slate-900 flex items-center justify-center text-sm font-black transition-all duration-200 active:scale-95 cursor-pointer shadow-sm"
                    aria-label="How to play"
                    title="How To Play"
                >
                    ?
                </button>

                <button
                    type="button"
                    @click="$emit('open-stats')"
                    class="w-9 h-9 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-300 hover:text-amber-400 hover:border-amber-500/40 hover:bg-slate-900 flex items-center justify-center text-sm transition-all duration-200 active:scale-95 cursor-pointer shadow-sm"
                    aria-label="View Statistics"
                    title="Statistics"
                >
                    📊
                </button>
            </div>

            <!-- Center Brand Logo (Clean & Balanced without extra X box) -->
            <div
                class="flex items-center cursor-pointer select-none group"
                @click="$emit('new-game')"
                title="Start New Game"
            >
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-200 bg-clip-text text-transparent group-hover:opacity-90 transition-opacity">
                    GUESS<span class="text-amber-400 drop-shadow-[0_0_12px_rgba(251,191,36,0.6)]">-X</span>
                </h1>
            </div>

            <!-- Right Action Buttons & User Menu -->
            <div class="flex items-center gap-1.5 sm:gap-2">
                <button
                    type="button"
                    @click="$emit('new-game')"
                    :disabled="loading"
                    class="w-9 h-9 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-300 hover:text-amber-400 hover:border-amber-500/40 hover:bg-slate-900 flex items-center justify-center text-sm transition-all duration-200 active:scale-95 cursor-pointer shadow-sm disabled:opacity-40"
                    aria-label="New Game"
                    title="Restart Round"
                >
                    🔄
                </button>

                <UserMenu
                    @open-auth="$emit('open-auth')"
                    @open-change-password="$emit('open-change-password')"
                />
            </div>
        </div>
    </header>
</template>

<script setup>
// YB - 15-09-2026 Header component with glassmorphic navbar, glowing logo, and interactive actions
import UserMenu from '@/Components/UserMenu.vue';

defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['new-game', 'open-stats', 'open-help', 'open-auth', 'open-change-password']);
</script>

