<template>
    <div class="w-full max-w-md mx-auto px-1 sm:px-2 select-none" role="group" aria-label="On-screen Keyboard">
        <!-- Row 1 -->
        <div class="flex items-center justify-center gap-1 sm:gap-1.5 mb-1.5 touch-manipulation">
            <button
                v-for="key in row1"
                :key="key"
                type="button"
                :disabled="disabled"
                @click="onKeyPress(key)"
                :class="[
                    'h-12 sm:h-13 rounded-lg sm:rounded-xl font-bold font-mono text-sm sm:text-base flex-1 flex items-center justify-center transition-all duration-150 active:scale-95 active:translate-y-0.5 cursor-pointer shadow-sm',
                    getKeyClass(key)
                ]"
                :aria-label="key"
            >
                {{ key }}
            </button>
        </div>

        <!-- Row 2 -->
        <div class="flex items-center justify-center gap-1 sm:gap-1.5 mb-1.5 px-3 sm:px-4 touch-manipulation">
            <button
                v-for="key in row2"
                :key="key"
                type="button"
                :disabled="disabled"
                @click="onKeyPress(key)"
                :class="[
                    'h-12 sm:h-13 rounded-lg sm:rounded-xl font-bold font-mono text-sm sm:text-base flex-1 flex items-center justify-center transition-all duration-150 active:scale-95 active:translate-y-0.5 cursor-pointer shadow-sm',
                    getKeyClass(key)
                ]"
                :aria-label="key"
            >
                {{ key }}
            </button>
        </div>

        <!-- Row 3 with Enter and Backspace -->
        <div class="flex items-center justify-center gap-1 sm:gap-1.5 touch-manipulation">
            <!-- ENTER KEY -->
            <button
                type="button"
                :disabled="disabled || !canSubmit"
                @click="onKeyPress('ENTER')"
                :class="[
                    'h-12 sm:h-13 px-2 sm:px-3 rounded-lg sm:rounded-xl font-extrabold text-[11px] sm:text-xs tracking-wider flex-[1.5] flex items-center justify-center gap-1 transition-all duration-150 active:scale-95 active:translate-y-0.5 cursor-pointer shadow-md',
                    canSubmit
                        ? 'bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 shadow-emerald-900/30'
                        : 'bg-slate-800/80 text-slate-400 border border-slate-700/60 opacity-75'
                ]"
                aria-label="Submit Guess"
            >
                <span>ENTER</span>
                <span class="text-sm font-bold">↵</span>
            </button>

            <button
                v-for="key in row3"
                :key="key"
                type="button"
                :disabled="disabled"
                @click="onKeyPress(key)"
                :class="[
                    'h-12 sm:h-13 rounded-lg sm:rounded-xl font-bold font-mono text-sm sm:text-base flex-1 flex items-center justify-center transition-all duration-150 active:scale-95 active:translate-y-0.5 cursor-pointer shadow-sm',
                    getKeyClass(key)
                ]"
                :aria-label="key"
            >
                {{ key }}
            </button>

            <!-- BACKSPACE KEY -->
            <button
                type="button"
                :disabled="disabled"
                @click="onKeyPress('BACKSPACE')"
                class="h-12 sm:h-13 px-2 sm:px-3 rounded-lg sm:rounded-xl font-bold text-base sm:text-lg flex-[1.4] flex items-center justify-center bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700/80 transition-all duration-150 active:scale-95 active:translate-y-0.5 cursor-pointer shadow-sm disabled:opacity-50"
                aria-label="Backspace"
            >
                ⌫
            </button>
        </div>
    </div>
</template>

<script setup>
// YB - 15-09-2026 Interactive virtual tactile keyboard with real-time guess color evaluation
const props = defineProps({
    keyStatuses: {
        type: Object, // { 'A': 'green' | 'yellow' | 'gray' }
        default: () => ({}),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    canSubmit: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['key-press']);

const row1 = ['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'];
const row2 = ['A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L'];
const row3 = ['Z', 'X', 'C', 'V', 'B', 'N', 'M'];

const getKeyClass = (key) => {
    const status = props.keyStatuses[key];
    switch (status) {
        case 'green':
            return 'bg-emerald-600 border border-emerald-400 text-white shadow-emerald-900/40 shadow-sm';
        case 'yellow':
            return 'bg-amber-600 border border-amber-400 text-white shadow-amber-900/40 shadow-sm';
        case 'gray':
            return 'bg-slate-900/90 border border-slate-800 text-slate-500 opacity-60';
        default:
            return 'bg-slate-800/90 hover:bg-slate-700 border border-slate-700/80 text-slate-200 hover:text-white';
    }
};

const onKeyPress = (key) => {
    emit('key-press', key);
};
</script>
