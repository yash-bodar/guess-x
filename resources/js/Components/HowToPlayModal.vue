<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xl transition-all duration-300 animate-pop"
        @click.self="$emit('close')"
    >
        <div class="relative w-full max-w-md rounded-3xl bg-slate-900/95 border border-slate-700/80 p-6 text-left shadow-2xl shadow-black/90 max-h-[90vh] overflow-y-auto">
            <!-- Background Glow -->
            <div class="absolute -top-16 -right-16 w-36 h-36 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <button
                type="button"
                @click="$emit('close')"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-sm font-black transition-all duration-200 active:scale-95 cursor-pointer"
                aria-label="Close Rules"
            >
                ✕
            </button>

            <div class="flex items-center gap-2.5 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-400 text-slate-950 font-black text-lg flex items-center justify-center shadow-lg shadow-amber-500/20">
                    📖
                </div>
                <h3 class="text-xl font-black text-white tracking-tight uppercase">
                    How To Play
                </h3>
            </div>

            <div class="space-y-4 text-xs sm:text-sm text-slate-300">
                <p class="leading-relaxed">
                    Guess the secret word in as many attempts as there are letters (5 guesses for 5 letters, 6 for 6 letters, 7 for 7 letters).
                </p>

                <div class="p-3.5 rounded-2xl bg-gradient-to-br from-amber-500/15 to-amber-950/20 border border-amber-500/30">
                    <div class="font-bold text-amber-400 flex items-center gap-1.5 mb-1">
                        <span>⭐</span>
                        <span>The X-Factor Clue</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Every game reveals one free letter at a random position before your first guess to give you a smart starting edge!
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="font-black text-white text-[11px] uppercase tracking-wider">
                        Color Clues After Each Guess:
                    </div>

                    <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-950/80 border border-slate-800/80">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-b from-emerald-500 to-emerald-600 border border-emerald-400 text-white font-mono font-black flex items-center justify-center text-sm shadow-md shadow-emerald-950/60">
                            W
                        </div>
                        <div class="text-xs">
                            <span class="text-emerald-400 font-bold">Green:</span> Letter is in the word and in the <span class="text-white font-semibold">correct spot</span>.
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-950/80 border border-slate-800/80">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-b from-amber-500 to-amber-600 border border-amber-400 text-white font-mono font-black flex items-center justify-center text-sm shadow-md shadow-amber-950/60">
                            I
                        </div>
                        <div class="text-xs">
                            <span class="text-amber-400 font-bold">Yellow:</span> Letter is in the word but in the <span class="text-white font-semibold">wrong spot</span>.
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-950/80 border border-slate-800/80">
                        <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 text-slate-400 font-mono font-black flex items-center justify-center text-sm">
                            N
                        </div>
                        <div class="text-xs">
                            <span class="text-slate-400 font-bold">Gray:</span> Letter is <span class="text-white font-semibold">not</span> in the secret word.
                        </div>
                    </div>
                </div>

                <p class="text-[11px] text-slate-400">
                    💡 Type with your keyboard or tap the on-screen keys!
                </p>
            </div>

            <button
                type="button"
                @click="$emit('close')"
                class="w-full mt-5 py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-black text-xs uppercase tracking-wider transition-all duration-200 active:scale-95 cursor-pointer shadow-lg shadow-emerald-500/20"
            >
                Got It, Let's Play!
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
});

defineEmits(['close']);
</script>
