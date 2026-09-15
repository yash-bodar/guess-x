# Guess-X — Production Word Guessing Game with X-Factor Clue

A modern, responsive Wordle-style word guessing game with dynamic **X-Factor** letter clues, user authentication (Email/Password & Google OAuth), streak persistence, and mid-game session recovery. Built with **Laravel 12**, **Vue 3 (Composition API)**, **Inertia.js**, **Tailwind CSS**, and **Capacitor Android**.

---

## 🌟 Game Concept & Highlights

1. **Three Dynamic Game Modes:**
   - **5 Letter Mode:** 5-letter secret word, exactly 5 guesses ($5 \times 5$ board).
   - **6 Letter Mode:** 6-letter secret word, exactly 6 guesses ($6 \times 6$ board).
   - **7 Letter Mode:** 7-letter secret word, exactly 7 guesses ($7 \times 7$ board).
2. **⭐ The X-Factor Clue:**
   - Every round randomly reveals one letter position from the secret word as an immediate starting hint (e.g. `4th letter: N` for `_ _ _ N _ _`).
3. **Curated Dictionary & Targetability:**
   - **1,611 Common Target Words** (`is_targetable = 1`) used as secret words to prevent obscure answers.
   - **86,199 Valid English Words** (`is_valid = 1`) recognized in dictionary guesses.
4. **Full Authentication & Stats Synchronization:**
   - Email/Password login & registration.
   - Google Sign-In with Socialite.
   - 6-digit numeric PIN password reset & change password.
   - User statistics (Played, Win Rate %, Streaks) stored in database for authenticated players and `localStorage` for guests.

---

## 💻 1. Cloning & Setting Up on a Local Machine

Follow these exact steps when cloning this repository to a new computer:

### Step 1: Clone Repository
```bash
git clone https://github.com/your-username/Guess-x.git
cd Guess-x
```

### Step 2: Install PHP & Node Dependencies
```bash
composer install
npm install
```

### Step 3: Configure Environment (`.env`)
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and configure your local database and URLs:
```ini
APP_NAME=Guess-X
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000 # (or http://guess-x.test in Laragon)

# Local Database Settings
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=guess_x
DB_USERNAME=root
DB_PASSWORD=

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Google OAuth (Optional for local testing)
GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-your-google-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### Step 4: Run Migrations & Dictionary Seeding
```bash
php artisan migrate:fresh --seed
```
*(This seeds 87,810 dictionary words with 1,611 common target words)*

### Step 5: Start Local Development Servers
```bash
# Terminal 1 (Frontend Vite HMR)
npm run dev

# Terminal 2 (Laravel Backend Server)
php artisan serve
```
Open `http://localhost:8000` (or your Laragon virtual host) in your browser!

### Step 6: Run Tests
```bash
php artisan test
```

---

## 🚀 2. Steps to Make the Project Live (Production Deployment)

When deploying Guess-X to a live VPS, Forge, cPanel, or Cloud server:

### Step 1: Production `.env` Settings
Create `.env` on your production server:
```ini
APP_NAME=Guess-X
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Production Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_production_user
DB_PASSWORD=your_production_password

# HTTPS Cookie Security
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

# Google OAuth Live Credentials
GOOGLE_CLIENT_ID=your-live-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-your-live-secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback

# Mail Configuration (For Password Reset Emails)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 2: Google Cloud Console Configuration
1. Go to [Google Cloud Console](https://console.cloud.google.com/) → **APIs & Services** → **Credentials**.
2. Update **Authorized JavaScript Origins:**
   - `https://yourdomain.com`
3. Update **Authorized Redirect URIs:**
   - `https://yourdomain.com/auth/google/callback`
4. In **OAuth Consent Screen**, set the publishing status to **In Production**.

### Step 3: Server Build & Optimization Commands
Run the following commands on your production server:
```bash
# 1. Install optimized dependencies
composer install --no-dev --optimize-autoloader
npm ci

# 2. Build production assets
npm run build

# 3. Database migrations and seeding
php artisan migrate --force
php artisan db:seed --class=WordSeeder --force

# 4. Cache configurations and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Step 4: Web Server Document Root
Ensure your web server (Nginx/Apache) points its document root strictly to the `public/` directory:
- **Nginx root directive:** `root /var/www/guess-x/public;`
- Ensure URL rewrites point to `index.php` (`try_files $uri $uri/ /index.php?$query_string;`).

---

## 📱 3. Making the Application Downloadable for Users

You have two ways to deliver Guess-X as a downloadable application:

### Method A: Instant Web App (PWA / Add to Home Screen) — *Recommended & Easiest*
No app store review or APK compilation is required. Users on mobile can install Guess-X with one tap:

1. **On Android (Chrome):**
   - Open `https://yourdomain.com`.
   - Tap **⋮ (Menu)** → **"Add to Home screen"** or **"Install app"**.
2. **On iPhone (Safari):**
   - Open `https://yourdomain.com`.
   - Tap **Share (⬆)** → **"Add to Home Screen"**.
3. **User Experience:**
   - The app launches full-screen with safe-area notch padding and custom icon without any browser URL bars.

---

### Method B: Native Android APK / Google Play (.aab) via Capacitor

If you want to generate a standalone downloadable `.apk` file or publish to the **Google Play Store**:

#### 1. Set Production Domain in `capacitor.config.json`
Update [capacitor.config.json](file:///c:/laragon/www/Guess-x/capacitor.config.json):
```json
{
  "appId": "com.company.guessx",
  "appName": "Guess-X",
  "webDir": "public/build",
  "server": {
    "url": "https://yourdomain.com",
    "cleartext": false
  },
  "android": {
    "allowMixedContent": false,
    "backgroundColor": "#030712"
  }
}
```

#### 2. Sync Assets & Build APK
```bash
# 1. Build frontend bundle
npm run build

# 2. Sync web build into Android project
npx cap sync android

# 3. Build standalone APK
cd android
./gradlew assembleRelease
```
The downloadable APK will be located at:
```
android/app/build/outputs/apk/release/app-release.apk
```

#### 3. Build Signed Bundle for Google Play Store (.aab)
```bash
# 1. Generate release keystore (keep this safe)
keytool -genkey -v -keystore guessx-release.jks -alias guessx-alias -keyalg RSA -keysize 2048 -validity 10000

# 2. Generate AAB file
cd android
./gradlew bundleRelease
```
The release bundle will be generated at:
```
android/app/build/outputs/bundle/release/app-release.aab
```
Upload this `app-release.aab` directly to your [Google Play Console](https://play.google.com/console).

---

## 🏛️ Project Architecture

```
Guess-x/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php      # Login, Register, Google OAuth, Reset PIN
│   │   │   └── GameController.php      # Game initialization, Guesses & Isolation
│   │   ├── Middleware/
│   │   │   └── HandleInertiaRequests.php # Shared auth & game session props
│   │   └── Requests/                   # Form requests for validation
│   ├── Models/
│   │   ├── Game.php                    # Game state & guesses relationship
│   │   ├── GameGuess.php               # Individual guess evaluations
│   │   ├── User.php                    # Player stats, streaks, avatar
│   │   └── Word.php                    # Dictionary words & targetability
│   └── Services/
│       └── WordGameService.php         # Wordle evaluation & X-Factor algorithm
├── resources/
│   ├── css/
│   │   └── app.css                     # Custom glassmorphic styling & 3D flips
│   ├── js/
│   │   ├── Components/                 # Vue UI Components (Tiles, Header, Modals)
│   │   ├── composables/
│   │   │   └── useWordGame.js          # Core reactive game state composable
│   │   └── Pages/
│   │       └── Game/Index.vue          # Main game screen
├── android/                            # Native Capacitor Android project
├── capacitor.config.json               # Mobile configuration
└── routes/
    ├── api.php                         # Game & Auth API endpoints
    └── web.php                         # Inertia web routes & OAuth callback
```

---

## 🔒 Security & Quality Standards

- **Server-Side Word Security:** Secret words are strictly hidden from API payloads until the game concludes.
- **Session-Aware Authorization:** Password change dynamically enforces old password verification on email accounts while allowing direct updates on active OAuth sessions.
- **Full Test Coverage:** 25 passing automated tests (96 assertions) covering authentication, duplicate letter evaluations, and session recovery.
