<?php

namespace App\Console\Commands;

use App\Models\Word;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ImportWordsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'words:import 
                            {file? : Path to text file containing one word per line} 
                            {--download : Download and import comprehensive open-source English word dictionary}
                            {--length= : Filter by specific word length (5, 6, or 7)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import comprehensive 5, 6, and 7-letter words into the dictionary';

    /**
     * Execute the console command.
     *
     * // YB - 15-09-2026 Import large words dictionary from file or official open-source repository
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');
        $shouldDownload = $this->option('download');
        $filterLength = $this->option('length') ? (int) $this->option('length') : null;

        $lines = [];

        if ($shouldDownload) {
            $this->info('Downloading comprehensive English words list from verified repository...');
            $url = 'https://raw.githubusercontent.com/dwyl/english-words/master/words_alpha.txt';
            
            try {
                $response = Http::timeout(60)->get($url);
                if (! $response->successful()) {
                    $this->error('Failed to download word list from repository (HTTP ' . $response->status() . ').');
                    return Command::FAILURE;
                }
                $lines = explode("\n", $response->body());
                $this->info('Downloaded ' . number_format(count($lines)) . ' total English words.');
            } catch (\Throwable $e) {
                $this->error('Error during download: ' . $e->getMessage());
                return Command::FAILURE;
            }
        } elseif ($filePath) {
            if (! File::exists($filePath)) {
                $this->error("File not found: {$filePath}");
                return Command::FAILURE;
            }
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $this->info('Read ' . number_format(count($lines)) . ' lines from local file.');
        } else {
            $this->error('Please provide a file path or use the --download flag (e.g. php artisan words:import --download).');
            return Command::INVALID;
        }

        $this->info('Filtering and importing 5, 6, and 7-letter words into the database...');
        $progressBar = $this->output->createProgressBar();

        $records = [];
        $now = now();
        $importedCount = 0;
        $seen = [];

        foreach ($lines as $line) {
            $word = strtoupper(trim($line));
            $length = mb_strlen($word);

            // Filter strictly for alphabetic words
            if (! preg_match('/^[A-Z]+$/', $word)) {
                continue;
            }

            if ($filterLength !== null && $length !== $filterLength) {
                continue;
            }

            if (! in_array($length, [5, 6, 7], true)) {
                continue;
            }

            // Deduplicate within the file
            if (isset($seen[$word])) {
                continue;
            }
            $seen[$word] = true;

            $records[] = [
                'word' => $word,
                'length' => $length,
                'is_valid' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($records) >= 500) {
                Word::upsert($records, ['word'], ['length', 'is_valid', 'updated_at']);
                $importedCount += count($records);
                $progressBar->advance(count($records));
                $records = [];
            }
        }

        if (count($records) > 0) {
            Word::upsert($records, ['word'], ['length', 'is_valid', 'updated_at']);
            $importedCount += count($records);
            $progressBar->advance(count($records));
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info(" Successfully imported " . number_format($importedCount) . " valid words into the dictionary!");

        // Display breakdown
        $count5 = Word::where('length', 5)->count();
        $count6 = Word::where('length', 6)->count();
        $count7 = Word::where('length', 7)->count();

        $this->table(
            ['Mode / Length', 'Total Words in Database'],
            [
                ['5 Letter Mode', number_format($count5) . ' words'],
                ['6 Letter Mode', number_format($count6) . ' words'],
                ['7 Letter Mode', number_format($count7) . ' words'],
                ['Total Available', number_format($count5 + $count6 + $count7) . ' words'],
            ]
        );

        return Command::SUCCESS;
    }
}
