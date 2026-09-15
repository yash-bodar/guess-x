<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xl transition-all duration-300 animate-pop"
        @click.self="$emit('close')"
    >
        <div class="relative w-full max-w-sm rounded-3xl bg-slate-900/95 border border-slate-700/80 p-6 text-center shadow-2xl shadow-black/90 overflow-hidden">
            <!-- Background Accent Glow -->
            <div class="absolute -top-16 -right-16 w-36 h-36 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Close Button -->
            <button
                type="button"
                @click="$emit('close')"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-sm font-black transition-all duration-200 active:scale-95 cursor-pointer"
                aria-label="Close Statistics"
            >
                ✕
            </button>

            <!-- Title -->
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 font-black text-xl mb-3 shadow-lg shadow-amber-500/20">
                📊
            </div>
            <h3 class="text-xl font-black text-white tracking-tight uppercase mb-5">
                Player Statistics
            </h3>

            <!-- 4 Grid Stats -->
            <div class="grid grid-cols-4 gap-2 mb-6">
                <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 shadow-inner">
                    <div class="text-2xl font-mono font-black text-white">
                        {{ stats.played || 0 }}
                    </div>
                    <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase mt-0.5">
                        Played
                    </div>
                </div>

                <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 shadow-inner">
                    <div class="text-2xl font-mono font-black text-emerald-400">
                        {{ winPercentage }}%
                    </div>
                    <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase mt-0.5">
                        Win %
                    </div>
                </div>

                <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 shadow-inner">
                    <div class="text-2xl font-mono font-black text-amber-400">
                        {{ stats.currentStreak || 0 }}
                    </div>
                    <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase mt-0.5">
                        Streak
                    </div>
                </div>

                <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 shadow-inner">
                    <div class="text-2xl font-mono font-black text-amber-500">
                        {{ stats.maxStreak || 0 }}
                    </div>
                    <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase mt-0.5">
                        Max
                    </div>
                </div>
            </div>

            <button
                type="button"
                @click="$emit('close')"
                class="w-full py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider transition-all duration-200 active:scale-95 cursor-pointer shadow-sm"
            >
                Close
            </button>
        </div>
    </div>
</template>

<script setup>
defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    stats: {
        type: Object,
        default: () => ({
            played: 0,
            won: 0,
            lost: 0,
            currentStreak: 0,
            maxStreak: 0,
        }),
    },
    winPercentage: {
        type: Number,
        default: 0,
    },
});

defineEmits(['close']);
</script>
