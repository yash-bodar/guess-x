<template>
    <div
        :class="[
            'flex items-center justify-center transition-transform duration-200',
            wordLength === 7 ? 'gap-1 sm:gap-1.5 md:gap-2' : (wordLength === 6 ? 'gap-1.5 sm:gap-2 md:gap-2.5' : 'gap-1.5 sm:gap-2 md:gap-2.5'),
            { 'animate-shake': isCurrent && isShaking }
        ]"
    >
        <GameTile
            v-for="(tile, i) in tiles"
            :key="i"
            :letter="tile.letter"
            :status="tile.status"
            :index="i"
            :word-length="wordLength"
            :is-typing="isCurrent"
            :is-revealed="isCompleted"
            :is-winning="isWinningRow"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import GameTile from './GameTile.vue';

const props = defineProps({
    wordLength: {
        type: Number,
        default: 5,
    },
    guessData: {
        type: Object, // { guess: string, result: string[] }
        default: null,
    },
    currentGuess: {
        type: String,
        default: '',
    },
    isCurrent: {
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

const isCompleted = computed(() => !!props.guessData);

const isWinningRow = computed(() => {
    if (!props.guessData || !props.guessData.result) return false;
    return props.isWinning && props.guessData.result.every(r => r === 'green');
});

const tiles = computed(() => {
    const list = [];

    if (props.guessData) {
        // Completed row with results
        const letters = props.guessData.guess.split('');
        const results = props.guessData.result;
        for (let i = 0; i < props.wordLength; i++) {
            list.push({
                letter: letters[i] || '',
                status: results[i] || 'gray',
            });
        }
    } else if (props.isCurrent) {
        // Active typing row
        const letters = props.currentGuess.split('');
        for (let i = 0; i < props.wordLength; i++) {
            const letter = letters[i] || '';
            list.push({
                letter: letter,
                status: letter ? 'filled' : 'empty',
            });
        }
    } else {
        // Upcoming empty row
        for (let i = 0; i < props.wordLength; i++) {
            list.push({
                letter: '',
                status: 'empty',
            });
        }
    }

    return list;
});
</script>
