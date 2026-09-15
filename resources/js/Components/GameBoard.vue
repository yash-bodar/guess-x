<template>
    <div
        class="relative flex flex-col items-center justify-center gap-1.5 sm:gap-2 md:gap-2.5 my-auto cursor-pointer select-none outline-none focus:outline-none"
        @click="triggerFocus"
        role="region"
        aria-label="Word Game Board"
        tabindex="0"
    >
        <!-- Native Hidden/Integrated Input for Mobile & Tablet Keyboard Capture -->
        <input
            ref="nativeInput"
            type="text"
            class="absolute -top-10 left-0 w-1 h-1 opacity-0 pointer-events-none p-0 m-0 border-0 outline-none text-transparent"
            autocomplete="off"
            autocorrect="off"
            autocapitalize="characters"
            spellcheck="false"
            inputmode="text"
            maxlength="7"
            :disabled="isGameOver"
            @input="onInput"
            @keydown.enter.prevent="$emit('submit')"
            aria-hidden="true"
        />

        <!-- Grid of Game Rows (5x5, 6x6, or 7x7) -->
        <GameRow
            v-for="rowIndex in maxGuesses"
            :key="rowIndex"
            :word-length="wordLength"
            :guess-data="guesses[rowIndex - 1] || null"
            :current-guess="isCurrentRow(rowIndex) ? currentGuess : ''"
            :is-current="isCurrentRow(rowIndex)"
            :is-shaking="isCurrentRow(rowIndex) && isShaking"
            :is-winning="isWinning"
        />

        <!-- Tap to type mobile helper hint if not typing and on mobile -->
        <div
            v-if="!isGameOver && currentGuess.length === 0 && guesses.length === 0"
            class="text-[11px] text-slate-500 font-medium tracking-wide mt-2 flex items-center gap-1 opacity-80"
        >
            <span>Tap board to type</span>
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import GameRow from './GameRow.vue';

const props = defineProps({
    wordLength: {
        type: Number,
        default: 5,
    },
    maxGuesses: {
        type: Number,
        default: 5,
    },
    guesses: {
        type: Array,
        default: () => [],
    },
    currentGuess: {
        type: String,
        default: '',
    },
    isGameOver: {
        type: Boolean,
        default: false,
    },
    isShaking: {
        type: Boolean,
        default: false,
    },
    isWinning: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['input-change', 'submit', 'mounted-input']);

const nativeInput = ref(null);

const isCurrentRow = (rowIndex) => {
    return !props.isGameOver && rowIndex === props.guesses.length + 1;
};

const triggerFocus = () => {
    if (!props.isGameOver && nativeInput.value) {
        nativeInput.value.focus();
    }
};

const onInput = (e) => {
    emit('input-change', e);
};

onMounted(() => {
    if (nativeInput.value) {
        emit('mounted-input', nativeInput.value);
        nativeInput.value.focus();
    }
});

// Re-focus on mode or game reset
watch(() => props.guesses.length, () => {
    if (!props.isGameOver) {
        triggerFocus();
    }
});
</script>
