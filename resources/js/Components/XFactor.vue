<template>
    <div class="w-full max-w-sm sm:max-w-md mx-auto">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500/15 via-slate-900/90 to-amber-950/20 border border-amber-500/30 p-2.5 sm:p-3.5 shadow-lg shadow-amber-500/10 backdrop-blur-xl">
            <!-- Decorative background glow -->
            <div class="absolute -top-12 -right-12 w-28 h-28 bg-amber-500/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex items-center justify-between gap-2 relative z-10">
                <!-- Left Info Section (Never wraps badge onto next line) -->
                <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-400 flex items-center justify-center text-slate-950 shadow-md shadow-amber-500/30 font-black text-xs sm:text-sm shrink-0">
                        ⭐
                    </div>
                    <div class="min-w-0">
                        <div class="text-[9px] sm:text-[10px] font-black tracking-widest text-amber-400 uppercase leading-none mb-0.5">
                            X-Factor Clue
                        </div>
                        <div class="text-xs sm:text-sm font-semibold text-slate-200 whitespace-nowrap flex items-center gap-1" v-if="xFactor">
                            <span><strong class="text-white font-black">{{ positionOrdinal }}</strong> letter is</span>
                            <span class="inline-flex items-center justify-center min-w-[20px] px-1.5 py-0.5 rounded-md bg-amber-500/25 text-amber-300 font-mono font-black text-xs border border-amber-500/50">{{ xFactor.letter }}</span>
                        </div>
                        <div class="text-xs text-slate-400 animate-pulse" v-else>
                            Revealing clue...
                        </div>
                    </div>
                </div>

                <!-- Letter Slots Preview e.g. · · · I · · · -->
                <div class="flex items-center gap-0.5 sm:gap-1 font-mono text-xs sm:text-sm md:text-base font-black px-2 sm:px-2.5 py-1 rounded-xl bg-slate-950/80 border border-slate-800/80 shadow-inner shrink-0">
                    <span
                        v-for="i in wordLength"
                        :key="i"
                        :class="[
                            'w-3.5 xs:w-4 sm:w-5 text-center transition-all duration-300',
                            xFactor && xFactor.position === i
                                ? 'text-amber-400 font-black scale-110 drop-shadow-[0_0_8px_rgba(251,191,36,0.7)]'
                                : 'text-slate-600 font-normal'
                        ]"
                    >
                        {{ xFactor && xFactor.position === i ? xFactor.letter : '·' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    xFactor: {
        type: Object,
        default: null,
    },
    wordLength: {
        type: Number,
        default: 5,
    },
});

// Calculate ordinal text e.g. 1st, 2nd, 3rd, 4th
const positionOrdinal = computed(() => {
    if (!props.xFactor?.position) return '';
    const pos = props.xFactor.position;
    const s = ['th', 'st', 'nd', 'rd'];
    const v = pos % 100;
    return pos + (s[(v - 20) % 10] || s[v] || s[0]);
});
</script>
