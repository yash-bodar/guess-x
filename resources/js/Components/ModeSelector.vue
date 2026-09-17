<template>
    <div class="flex items-center justify-center p-1 bg-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-800/80 shadow-lg shadow-black/40 max-w-md mx-auto w-full gap-1">
        <button
            v-for="mode in modes"
            :key="mode"
            type="button"
            :disabled="disabled"
            @click="$emit('change-mode', mode)"
            :class="[
                'flex-1 py-2 px-2 text-xs sm:text-sm font-black tracking-wider rounded-xl transition-all duration-200 flex items-center justify-center gap-1 focus:outline-none cursor-pointer',
                !isDaily && currentMode === mode
                    ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-md shadow-amber-500/25 scale-[1.02]'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60 disabled:opacity-40'
            ]"
            :aria-label="`${mode} Letter Mode`"
        >
            <span class="text-sm font-mono">{{ mode }}</span>
            <span class="text-[9px] sm:text-xs font-bold opacity-90">LETTERS</span>
        </button>

        <!-- YB - 17-09-2026 Daily Challenge toggle with streak badge -->
        <button
            type="button"
            :disabled="disabled"
            @click="$emit('select-daily')"
            :class="[
                'flex-1 py-2 px-2 text-xs sm:text-sm font-black tracking-wider rounded-xl transition-all duration-200 flex items-center justify-center gap-1 focus:outline-none cursor-pointer',
                isDaily
                    ? 'bg-gradient-to-r from-purple-500 via-indigo-500 to-cyan-500 text-white shadow-md shadow-purple-500/30 scale-[1.02]'
                    : 'text-purple-400 hover:text-purple-300 hover:bg-slate-800/60 disabled:opacity-40 border border-purple-500/30'
            ]"
            aria-label="Daily Challenge Mode"
        >
            <span class="text-xs sm:text-sm">📅</span>
            <span class="text-[9px] sm:text-xs font-black uppercase tracking-wider">DAILY</span>
            <span
                v-if="dailyStreak > 0"
                class="px-1 py-0.2 rounded-full bg-amber-500 text-slate-950 font-black text-[9px] flex items-center leading-tight shadow-sm"
                title="Current Daily Streak"
            >
                🔥{{ dailyStreak }}
            </span>
        </button>
    </div>
</template>

<script setup>
// YB - 17-09-2026 Mode selector supporting 5, 6, 7 letter modes and synchronized Daily Challenge
defineProps({
    currentMode: {
        type: Number,
        default: 5,
    },
    modes: {
        type: Array,
        default: () => [5, 6, 7],
    },
    isDaily: {
        type: Boolean,
        default: false,
    },
    dailyStreak: {
        type: Number,
        default: 0,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['change-mode', 'select-daily']);
</script>
