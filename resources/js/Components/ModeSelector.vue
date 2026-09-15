<template>
    <div class="flex items-center justify-center p-1 bg-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-800/80 shadow-lg shadow-black/40 max-w-sm mx-auto w-full">
        <button
            v-for="mode in modes"
            :key="mode"
            type="button"
            :disabled="disabled"
            @click="$emit('change-mode', mode)"
            :class="[
                'flex-1 py-2 px-3 text-xs sm:text-sm font-black tracking-wider rounded-xl transition-all duration-200 flex items-center justify-center gap-1.5 focus:outline-none cursor-pointer',
                currentMode === mode
                    ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-md shadow-amber-500/25 scale-[1.02]'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60 disabled:opacity-40'
            ]"
            :aria-label="`${mode} Letter Mode`"
        >
            <span class="text-sm font-mono">{{ mode }}</span>
            <span class="text-[10px] sm:text-xs font-bold opacity-90">LETTERS</span>
        </button>
    </div>
</template>

<script setup>
defineProps({
    currentMode: {
        type: Number,
        default: 5,
    },
    modes: {
        type: Array,
        default: () => [5, 6, 7],
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['change-mode']);
</script>
