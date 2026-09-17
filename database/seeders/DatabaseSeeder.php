<?php

namespace Database\Seeders;

use App\Models\Word;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // YB - 17-09-2026 Auto-import 86K open-source dictionary words if not already present
        if (Word::where('is_valid', true)->count() < 2000) {
            $this->command?->info('Extended dictionary not detected (< 2,000 words). Auto-fetching 86K open-source words list...');
            try {
                Artisan::call('words:import', ['--download' => true], $this->command?->getOutput());
            } catch (\Throwable $e) {
                Log::warning('Automatic dictionary download failed during seeder: ' . $e->getMessage());
                $this->command?->warn('Could not auto-download extended dictionary. Proceeding with curated target words.');
            }
        }

        // YB - 15-09-2026 Seed initial dictionary words for gameplay
        $this->call([
            WordSeeder::class,
        ]);
    }
}
