// YB - 15-09-2026 Word game composable handling state, input, X-factor clue, evaluation, and session recovery
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import confetti from 'canvas-confetti';

const STORAGE_KEY_STATS = 'guessx_game_stats_v1';
const STORAGE_KEY_ACTIVE_GAME = 'guessx_active_game_id';

export function useWordGame(initialMode = 5) {
    const page = usePage();
    const wordLength = ref(initialMode);
    const maxGuesses = computed(() => wordLength.value);
    const gameId = ref(null);
    const gameStatus = ref('not_started'); // 'not_started' | 'playing' | 'won' | 'lost'
    const xFactor = ref(null); // { position: number, letter: string }
    const secretWord = ref(null);

    // List of completed guesses: Array of { guess: string, result: Array<'green'|'yellow'|'gray'>, guessNumber: number }
    const guesses = ref([]);
    const currentGuess = ref('');

    const loading = ref(false);
    const errorMessage = ref('');
    const isShaking = ref(false);
    const showStatsModal = ref(false);
    const showHelpModal = ref(false);
    const isRevealing = ref(false);

    // Ref to native hidden input element
    const inputRef = ref(null);

    // Statistics state
    const stats = ref({
        played: 0,
        won: 0,
        lost: 0,
        currentStreak: 0,
        maxStreak: 0,
    });

    const guessesRemaining = computed(() => {
        return Math.max(0, maxGuesses.value - guesses.value.length);
    });

    const winPercentage = computed(() => {
        if (stats.value.played === 0) return 0;
        return Math.round((stats.value.won / stats.value.played) * 100);
    });

    const isGameOver = computed(() => {
        return gameStatus.value === 'won' || gameStatus.value === 'lost';
    });

    // Load statistics from user profile or localStorage
    const loadStats = () => {
        const authUser = page.props?.auth?.user;
        if (authUser) {
            stats.value = {
                played: authUser.games_played || 0,
                won: authUser.games_won || 0,
                lost: authUser.games_lost || 0,
                currentStreak: authUser.current_streak || 0,
                maxStreak: authUser.max_streak || 0,
            };
            return;
        }

        try {
            const saved = localStorage.getItem(STORAGE_KEY_STATS);
            if (saved) {
                stats.value = { ...stats.value, ...JSON.parse(saved) };
            } else {
                stats.value = {
                    played: 0,
                    won: 0,
                    lost: 0,
                    currentStreak: 0,
                    maxStreak: 0,
                };
            }
        } catch (e) {
            stats.value = {
                played: 0,
                won: 0,
                lost: 0,
                currentStreak: 0,
                maxStreak: 0,
            };
        }
    };

    // Save statistics to localStorage (only for guest mode)
    const saveStats = () => {
        if (page.props?.auth?.user) {
            return; // Authenticated user stats are managed and persisted server-side
        }

        try {
            localStorage.setItem(STORAGE_KEY_STATS, JSON.stringify(stats.value));
        } catch (e) {
            console.warn('Could not save stats to localStorage', e);
        }
    };

    // Update stats after game conclusion
    const recordGameEnd = (isWin) => {
        stats.value.played += 1;
        if (isWin) {
            stats.value.won += 1;
            stats.value.currentStreak += 1;
            if (stats.value.currentStreak > stats.value.maxStreak) {
                stats.value.maxStreak = stats.value.currentStreak;
            }
        } else {
            stats.value.lost += 1;
            stats.value.currentStreak = 0;
        }
        saveStats();
        try {
            localStorage.removeItem(STORAGE_KEY_ACTIVE_GAME);
        } catch (e) {
            // Ignore
        }
    };

    // Trigger error notification with shake animation
    const triggerError = (msg) => {
        errorMessage.value = msg;
        isShaking.value = true;
        setTimeout(() => {
            isShaking.value = false;
        }, 600);
        setTimeout(() => {
            if (errorMessage.value === msg) {
                errorMessage.value = '';
            }
        }, 3000);
    };

    // Helper to resolve API paths relative to deployment subfolder or root
    const getApiUrl = (path) => {
        const pathname = window.location.pathname.replace(/\/+$/, '');
        const cleanPath = path.startsWith('/') ? path : `/${path}`;
        return `${pathname}${cleanPath}`;
    };

    // Restore active game if exists
    const resumeOrStartGame = async () => {
        const activeId = localStorage.getItem(STORAGE_KEY_ACTIVE_GAME);
        if (!activeId) {
            await startNewGame(initialMode);
            return;
        }

        loading.value = true;
        try {
            const response = await fetch(getApiUrl(`/api/game/${activeId}`), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await response.json();

            if (!response.ok || !data.success || !data.data) {
                localStorage.removeItem(STORAGE_KEY_ACTIVE_GAME);
                await startNewGame(initialMode);
                return;
            }

            const game = data.data;

            // If game is already finished, start fresh
            if (game.status === 'won' || game.status === 'lost') {
                localStorage.removeItem(STORAGE_KEY_ACTIVE_GAME);
                await startNewGame(initialMode);
                return;
            }

            // Restore game session state
            gameId.value = game.id;
            wordLength.value = game.word_length;
            xFactor.value = game.x_factor;
            gameStatus.value = game.status;
            secretWord.value = game.secret_word;

            if (Array.isArray(game.guesses)) {
                guesses.value = game.guesses.map((g) => ({
                    guess: g.guess,
                    result: g.result,
                    guessNumber: g.guess_number,
                }));
            }

            focusInput();
        } catch (e) {
            localStorage.removeItem(STORAGE_KEY_ACTIVE_GAME);
            await startNewGame(initialMode);
        } finally {
            loading.value = false;
        }
    };

    // Start or restart a game session
    const startNewGame = async (mode = wordLength.value) => {
        wordLength.value = mode;
        guesses.value = [];
        currentGuess.value = '';
        gameStatus.value = 'not_started';
        xFactor.value = null;
        secretWord.value = null;
        errorMessage.value = '';
        loading.value = true;
        isRevealing.value = false;

        try {
            const response = await fetch(getApiUrl('/api/game/start'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    word_length: mode,
                }),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Failed to start game');
            }

            gameId.value = data.data.id;
            xFactor.value = data.data.x_factor;
            gameStatus.value = data.data.status || 'playing';
            secretWord.value = null;

            try {
                localStorage.setItem(STORAGE_KEY_ACTIVE_GAME, data.data.id);
            } catch (e) {
                // Ignore storage issues
            }

            focusInput();
        } catch (err) {
            triggerError(err.message || 'Network error starting game');
        } finally {
            loading.value = false;
        }
    };

    // Submit the current guess
    const submitGuess = async () => {
        if (loading.value || isRevealing.value || isGameOver.value) return;

        const guess = currentGuess.value.trim().toUpperCase();

        if (guess.length < wordLength.value) {
            triggerError(`Word must have ${wordLength.value} letters`);
            return;
        }

        if (!/^[A-Z]+$/.test(guess)) {
            triggerError('Word must only contain letters A-Z');
            return;
        }

        loading.value = true;
        errorMessage.value = '';

        try {
            const response = await fetch(getApiUrl(`/api/game/${gameId.value}/guess`), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    guess: guess,
                }),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                triggerError(data.message || 'Word not in dictionary');
                return;
            }

            const resultData = data.data;

            // Start reveal animation
            isRevealing.value = true;
            guesses.value.push({
                guess: resultData.guess,
                result: resultData.result,
                guessNumber: resultData.guess_number,
            });

            currentGuess.value = '';
            if (inputRef.value) {
                inputRef.value.value = '';
            }

            // Stagger reveal animation delay (tile count * 250ms)
            const revealTime = wordLength.value * 280 + 200;
            setTimeout(() => {
                isRevealing.value = false;
                gameStatus.value = resultData.game_status;

                if (resultData.game_status === 'won') {
                    secretWord.value = resultData.secret_word || resultData.guess;
                    recordGameEnd(true);
                    triggerCelebration();
                } else if (resultData.game_status === 'lost') {
                    secretWord.value = resultData.secret_word;
                    recordGameEnd(false);
                } else {
                    focusInput();
                }
            }, revealTime);

        } catch (err) {
            triggerError(err.message || 'Network error submitting guess');
        } finally {
            loading.value = false;
        }
    };

    // Confetti celebration when won
    const triggerCelebration = () => {
        try {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#22c55e', '#eab308', '#38bdf8', '#a855f7'],
            });
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                });
                confetti({
                    particleCount: 50,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                });
            }, 300);
        } catch (e) {
            // Ignore if canvas-confetti unsupported
        }
    };

    // Handle physical keyboard typing or keydown events
    const handleKeydown = (event) => {
        // If event is inside any modal or input/textarea/select element other than the game input, do NOT intercept
        const target = event.target;
        const targetTag = target?.tagName?.toUpperCase();
        if (targetTag === 'INPUT' || targetTag === 'TEXTAREA' || targetTag === 'SELECT') {
            if (target !== inputRef.value) {
                return;
            }
        }

        if (isGameOver.value || isRevealing.value || showStatsModal.value || showHelpModal.value) {
            return;
        }

        const key = event.key;

        if (key === 'Enter') {
            event.preventDefault();
            submitGuess();
        } else if (key === 'Backspace') {
            event.preventDefault();
            if (currentGuess.value.length > 0) {
                currentGuess.value = currentGuess.value.slice(0, -1);
                if (inputRef.value) {
                    inputRef.value.value = currentGuess.value;
                }
            }
        } else if (/^[a-zA-Z]$/.test(key)) {
            event.preventDefault();
            if (currentGuess.value.length < wordLength.value) {
                currentGuess.value = (currentGuess.value + key.toUpperCase()).slice(0, wordLength.value);
                if (inputRef.value) {
                    inputRef.value.value = currentGuess.value;
                }
            }
        }
    };

    // Sync input from native keyboard (mobile & tablet)
    const handleNativeInput = (event) => {
        if (isGameOver.value || isRevealing.value) return;

        const raw = event.target.value || '';
        const sanitized = raw.replace(/[^a-zA-Z]/g, '').toUpperCase().slice(0, wordLength.value);

        currentGuess.value = sanitized;
        event.target.value = sanitized;
    };

    // Focus the hidden/integrated native input
    const focusInput = () => {
        if (inputRef.value && !isGameOver.value) {
            inputRef.value.focus();
        }
    };

    // Dynamic keyboard key statuses computed from revealed guesses
    const keyStatuses = computed(() => {
        const map = {};
        for (const guessObj of guesses.value) {
            if (!guessObj.guess || !guessObj.result) continue;
            const letters = guessObj.guess.split('');
            const results = guessObj.result;
            for (let i = 0; i < letters.length; i++) {
                const char = letters[i];
                const res = results[i];
                const current = map[char];
                if (res === 'green') {
                    map[char] = 'green';
                } else if (res === 'yellow') {
                    if (current !== 'green') {
                        map[char] = 'yellow';
                    }
                } else if (res === 'gray') {
                    if (!current) {
                        map[char] = 'gray';
                    }
                }
            }
        }
        return map;
    });

    // Handle on-screen virtual keyboard button taps
    const handleVirtualKey = (key) => {
        if (isGameOver.value || isRevealing.value) return;

        if (key === 'ENTER') {
            submitGuess();
        } else if (key === 'BACKSPACE' || key === '⌫') {
            if (currentGuess.value.length > 0) {
                currentGuess.value = currentGuess.value.slice(0, -1);
                if (inputRef.value) {
                    inputRef.value.value = currentGuess.value;
                }
            }
        } else if (/^[A-Z]$/.test(key)) {
            if (currentGuess.value.length < wordLength.value) {
                currentGuess.value = (currentGuess.value + key).slice(0, wordLength.value);
                if (inputRef.value) {
                    inputRef.value.value = currentGuess.value;
                }
            }
        }
    };

    onMounted(() => {
        loadStats();
        window.addEventListener('keydown', handleKeydown);
        resumeOrStartGame();
    });

    onUnmounted(() => {
        window.removeEventListener('keydown', handleKeydown);
    });

    return {
        wordLength,
        maxGuesses,
        gameId,
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
        keyStatuses,
        startNewGame,
        submitGuess,
        handleNativeInput,
        handleVirtualKey,
        focusInput,
        triggerError,
    };
}
