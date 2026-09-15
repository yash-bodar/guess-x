<template>
    <div
        :class="[
            'perspective-tile flex items-center justify-center font-black font-mono uppercase select-none transition-all duration-200',
            tileSizeClass
        ]"
    >
        <div
            :class="[
                'w-full h-full flex items-center justify-center rounded-xl sm:rounded-2xl border-2 transition-all duration-300 transform-gpu relative overflow-hidden',
                tileStateClasses,
                { 'animate-pop': isTyping && letter },
                { 'animate-flip': isRevealed },
                { 'animate-bounce-win': isWinning }
            ]"
            :style="{
                animationDelay: isRevealed ? `${index * 220}ms` : (isWinning ? `${index * 120}ms` : '0ms'),
                transitionDelay: isRevealed ? `${index * 220}ms` : '0ms'
            }"
        >
            <!-- Top Subtle Glass Glare Reflection -->
            <div class="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-white/10 to-transparent pointer-events-none"></div>

            <span
                :class="[
                    'transition-transform duration-200 tracking-wider relative z-10',
                    isWinning ? 'scale-110 font-black drop-shadow-md' : 'drop-shadow-sm'
                ]"
            >
                {{ letter }}
            </span>
        </div>
    </div>
</template>

<script setup>
// YB - 15-09-2026 Enhanced 3D tactile game tile with glass gradients and responsive sizing
import { computed } from 'vue';

const props = defineProps({
    letter: {
        type: String,
        default: '',
    },
    status: {
        type: String, // 'empty' | 'filled' | 'green' | 'yellow' | 'gray'
        default: 'empty',
    },
    index: {
        type: Number,
        default: 0,
    },
    wordLength: {
        type: Number,
        default: 5,
    },
    isTyping: {
        type: Boolean,
        default: false,
    },
    isRevealed: {
        type: Boolean,
        default: false,
    },
    isWinning: {
        type: Boolean,
        default: false,
    },
});

// Dynamic sizing based on word length to fit mobile screens perfectly without overflow
const tileSizeClass = computed(() => {
    if (props.wordLength === 7) {
        return 'w-10 h-10 sm:w-13 sm:h-13 md:w-15 md:h-15 text-lg sm:text-2xl';
    } else if (props.wordLength === 6) {
        return 'w-11 h-11 sm:w-14 sm:h-14 md:w-16 md:h-16 text-xl sm:text-2xl';
    } else {
        return 'w-13 h-13 sm:w-16 sm:h-16 md:w-18 md:h-18 text-2xl sm:text-3xl';
    }
});

const tileStateClasses = computed(() => {
    switch (props.status) {
        case 'green':
            return 'bg-gradient-to-b from-emerald-500 to-emerald-600 border-emerald-400 text-white shadow-md shadow-emerald-950/60';
        case 'yellow':
            return 'bg-gradient-to-b from-amber-500 to-amber-600 border-amber-400 text-white shadow-md shadow-amber-950/60';
        case 'gray':
            return 'bg-slate-800/90 border-slate-700/80 text-slate-400 shadow-inner';
        case 'filled':
            return 'bg-slate-900 border-slate-300 text-white shadow-md ring-2 ring-slate-400/20';
        case 'empty':
        default:
            return 'bg-slate-950/80 border-slate-800/80 text-transparent';
    }
});
</script>

