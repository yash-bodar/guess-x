<template>
    <div
        v-if="isGameOver"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xl transition-all duration-300 animate-pop"
    >
        <div class="relative w-full max-w-sm rounded-3xl bg-slate-900/95 border border-slate-700/80 p-6 text-center shadow-2xl shadow-black/90 overflow-hidden">
            <!-- Glow background effect -->
            <div
                :class="[
                    'absolute -top-16 -left-16 w-40 h-40 rounded-full blur-3xl pointer-events-none opacity-50',
                    isWin ? 'bg-emerald-500' : 'bg-rose-500'
                ]"
            ></div>

            <!-- Icon Header -->
            <div class="mx-auto mb-3 flex items-center justify-center">
                <div
                    v-if="isWin"
                    class="w-16 h-16 rounded-3xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center text-3xl shadow-lg shadow-emerald-500/30 animate-bounce-win"
                >
                    🏆
                </div>
                <div
                    v-else
                    class="w-16 h-16 rounded-3xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-3xl shadow-lg shadow-rose-500/30"
                >
                    💔
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-2xl font-black tracking-tight text-white mb-1">
                {{ isWin ? winPraise : 'Good Effort!' }}
            </h2>

            <p class="text-xs sm:text-sm text-slate-400 mb-5">
                <template v-if="isWin">
                    You solved the {{ wordLength }}-letter word in <span class="text-emerald-400 font-bold font-mono">{{ guessesCount }}</span>/{{ maxGuesses }} attempts!
                </template>
                <template v-else>
                    You used all {{ maxGuesses }} attempts for this round.
                </template>
            </p>

            <!-- Secret Word Reveal -->
            <div class="mb-6 p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 shadow-inner">
                <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">
                    Secret Word Was
                </div>
                <div class="font-mono text-2xl sm:text-3xl font-black tracking-widest text-amber-400 drop-shadow-[0_0_12px_rgba(251,191,36,0.5)]">
                    {{ secretWord || '...' }}
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-2.5">
                <button
                    type="button"
                    @click="$emit('new-game')"
                    class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-black text-sm uppercase tracking-wider shadow-lg shadow-emerald-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 cursor-pointer flex items-center justify-center gap-2"
                >
                    <span>Play Next Round</span>
                    <span>→</span>
                </button>

                <button
                    type="button"
                    @click="$emit('open-stats')"
                    class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs uppercase tracking-wider transition-all duration-200 active:scale-95 cursor-pointer"
                >
                    View Statistics
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    gameStatus: {
        type: String,
        default: 'playing',
    },
    secretWord: {
        type: String,
        default: '',
    },
    guessesCount: {
        type: Number,
        default: 0,
    },
    maxGuesses: {
        type: Number,
        default: 5,
    },
    wordLength: {
        type: Number,
        default: 5,
    },
});

defineEmits(['new-game', 'open-stats']);

const isGameOver = computed(() => props.gameStatus === 'won' || props.gameStatus === 'lost');
const isWin = computed(() => props.gameStatus === 'won');

const winPraise = computed(() => {
    if (props.guessesCount === 1) return 'Genius!';
    if (props.guessesCount === 2) return 'Magnificent!';
    if (props.guessesCount === 3) return 'Impressive!';
    if (props.guessesCount === 4) return 'Splendid!';
    return 'Victory!';
});
</script>
