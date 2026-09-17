<template>
    <div class="flex flex-col min-h-screen bg-slate-950 text-slate-100 safe-top safe-bottom select-none">
        <!-- Top App Header (Full Width & Sticky) -->
        <GameHeader
            :loading="loading"
            @new-game="() => startNewGame()"
            @open-stats="showStatsModal = true"
            @open-help="showHelpModal = true"
            @open-auth="showAuthModal = true"
            @open-change-password="showChangePasswordModal = true"
        />

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col items-center justify-between px-3 sm:px-4 py-3 sm:py-4 max-w-lg mx-auto w-full gap-3 sm:gap-4">
            <!-- Mode Selector Tabs (5, 6, 7 letter & Daily) -->
            <ModeSelector
                :current-mode="wordLength"
                :is-daily="isDailyMode"
                :daily-streak="dailyStreak"
                :disabled="loading || isRevealing"
                @change-mode="(mode) => startNewGame(mode)"
                @select-daily="handleSelectDaily"
            />

            <!-- YB - 17-09-2026 Daily Challenge Cockpit Banner -->
            <div
                v-if="isDailyMode"
                class="w-full max-w-sm flex items-center justify-between px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-purple-950/80 via-indigo-950/80 to-slate-900/90 border border-purple-500/40 shadow-lg shadow-purple-950/40 text-xs animate-pop"
            >
                <div class="flex items-center gap-1.5 font-bold text-purple-300">
                    <span class="text-sm">📅</span>
                    <span class="uppercase tracking-wider text-[11px] font-black">Daily Challenge</span>
                    <span v-if="dailyDate" class="text-slate-400 font-mono text-[10px]">({{ dailyDate }})</span>
                </div>
                <div class="flex items-center gap-2 font-mono text-[11px]">
                    <span v-if="dailyStreak > 0" class="text-amber-400 font-bold flex items-center gap-0.5">
                        🔥 {{ dailyStreak }}
                    </span>
                    <span v-if="dailyCountdown" class="text-slate-300 flex items-center gap-1 font-bold">
                        ⏳ {{ dailyCountdown }}
                    </span>
                </div>
            </div>

            <!-- X-Factor Clue Card -->
            <XFactor
                :x-factor="xFactor"
                :word-length="wordLength"
            />

            <!-- Error Banner Notification -->
            <div
                v-if="errorMessage"
                class="w-full max-w-sm px-4 py-2 rounded-xl bg-rose-600/95 text-white font-bold text-xs sm:text-sm text-center shadow-lg shadow-rose-900/50 border border-rose-400/40 animate-pop"
            >
                {{ errorMessage }}
            </div>

            <!-- Game Board Area (Full Height & Centered) -->
            <div class="flex-1 flex flex-col items-center justify-center w-full my-auto py-2">
                <GameBoard
                    :word-length="wordLength"
                    :max-guesses="maxGuesses"
                    :guesses="guesses"
                    :current-guess="currentGuess"
                    :is-game-over="isGameOver"
                    :is-shaking="isShaking"
                    :is-winning="gameStatus === 'won'"
                    @input-change="handleNativeInput"
                    @submit="submitGuess"
                    @mounted-input="(el) => inputRef = el"
                />
            </div>

            <!-- Status & Bottom Controls -->
            <div class="w-full max-w-sm flex items-center justify-between pt-2 pb-2 text-xs text-slate-400 font-medium">
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-500 font-semibold">Guesses:</span>
                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-900/90 border border-slate-800 text-white font-mono font-black">
                        {{ guessesRemaining }} / {{ maxGuesses }} left
                    </span>
                </div>

                <!-- Action Button when active or complete -->
                <div class="flex items-center gap-2">
                    <button
                        v-if="!isGameOver && currentGuess.length === wordLength"
                        type="button"
                        @click="submitGuess"
                        :disabled="loading || isRevealing"
                        class="py-2 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition-all cursor-pointer flex items-center gap-1.5 animate-pop"
                    >
                        <span>ENTER</span>
                        <span class="font-black text-sm">↵</span>
                    </button>
                    <button
                        v-else-if="isGameOver"
                        type="button"
                        @click="() => isDailyMode ? startNewGame(5) : startNewGame()"
                        class="py-2 px-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg shadow-amber-500/20 hover:scale-105 active:scale-95 transition-all cursor-pointer"
                    >
                        {{ isDailyMode ? 'Play Standard Game' : 'New Game' }}
                    </button>
                </div>
            </div>
        </main>

        <!-- Game Result Modal (Won / Lost) -->
        <GameResult
            :game-status="gameStatus"
            :secret-word="secretWord"
            :guesses-count="guesses.length"
            :max-guesses="maxGuesses"
            :word-length="wordLength"
            :guesses="guesses"
            :is-daily="isDailyMode"
            :daily-streak="dailyStreak"
            :daily-date="dailyDate"
            :daily-countdown="dailyCountdown"
            @new-game="() => isDailyMode ? startNewGame(5) : startNewGame()"
            @open-stats="showStatsModal = true"
        />

        <!-- Player Statistics Modal -->
        <GameStatsModal
            :is-open="showStatsModal"
            :stats="stats"
            :win-percentage="winPercentage"
            @close="showStatsModal = false"
        />

        <!-- How To Play Modal -->
        <HowToPlayModal
            :is-open="showHelpModal"
            @close="showHelpModal = false"
        />

        <!-- Authentication Modal (Sign In / Register / Google / Forgot Password) -->
        <AuthModal
            :is-open="showAuthModal"
            @close="showAuthModal = false"
            @auth-success="handleAuthSuccess"
        />

        <!-- Change Password Modal -->
        <ChangePasswordModal
            :is-open="showChangePasswordModal"
            @close="showChangePasswordModal = false"
        />
    </div>
</template>

<script setup>
// YB - 17-09-2026 Main game screen container with Daily Challenge integration
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useWordGame } from '@/composables/useWordGame';
import GameHeader from '@/Components/GameHeader.vue';
import ModeSelector from '@/Components/ModeSelector.vue';
import XFactor from '@/Components/XFactor.vue';
import GameBoard from '@/Components/GameBoard.vue';
import GameResult from '@/Components/GameResult.vue';
import GameStatsModal from '@/Components/GameStatsModal.vue';
import HowToPlayModal from '@/Components/HowToPlayModal.vue';
import AuthModal from '@/Components/AuthModal.vue';
import ChangePasswordModal from '@/Components/ChangePasswordModal.vue';

const props = defineProps({
    initialMode: {
        type: Number,
        default: 5,
    },
    supportedModes: {
        type: Array,
        default: () => [5, 6, 7],
    },
});

const page = usePage();
const showAuthModal = ref(false);
const showChangePasswordModal = ref(false);

const {
    wordLength,
    maxGuesses,
    gameStatus,
    xFactor,
    secretWord,
    guesses,
    currentGuess,
    guessesRemaining,
    loading,
    errorMessage,
    isShaking,
    isGameOver,
    isRevealing,
    stats,
    winPercentage,
    showStatsModal,
    showHelpModal,
    inputRef,
    isDailyMode,
    dailyStatus,
    dailyStreak,
    dailyMaxStreak,
    dailyDate,
    dailyCountdown,
    startNewGame,
    startDailyGame,
    fetchDailyStatus,
    submitGuess,
    handleNativeInput,
} = useWordGame(props.initialMode);

// YB - 17-09-2026 Handle Daily Challenge selection (enforces signed-in users only)
const handleSelectDaily = async () => {
    const user = page.props?.auth?.user;
    if (!user) {
        showAuthModal.value = true;
        return;
    }
    await startDailyGame();
};

// YB - 17-09-2026 Refresh daily status upon successful login/registration
const handleAuthSuccess = async () => {
    await fetchDailyStatus();
};
</script>


