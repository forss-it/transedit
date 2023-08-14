<?php

namespace Dialect\TransEdit\Console\Commands;

use Dialect\TransEdit\Models\Key;
use Dialect\TransEdit\Models\Locale;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class AddMissingPhrases extends Command
{
    protected $signature = 'transedit:add-missing-phrases';
    protected $description = 'Searches the resource directory for missing phrases.';

    private $seen_phrases = [];

    public function handle()
    {
        $iterator = new \RecursiveDirectoryIterator(resource_path());
        $search_extensions = ['php', 'vue', 'js'];
        $missing_phrases = collect();

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            if ($file->isDir() || ! in_array($file->getExtension(), $search_extensions)) {
                continue;
            }

            $missing_phrases = $missing_phrases->merge($this->findMissingPhrases($file));
        }

        $locales = Locale::all();

        foreach ($missing_phrases as $phrase) {
            foreach ($locales as $locale) {
                transEdit($phrase, $phrase, $locale->name);
            }
        }

        if ($missing_phrases->count() > 0) {
            $this->info("Successfully added {$missing_phrases->count()} phrases to the database.");
        } else {
            $this->info('No missing phrases found.');
        }
    }

    private function findMissingPhrases($file): Collection
    {
        $missing_phrases = collect();

        foreach ($this->findPhrases($file) as $phrase) {
            if (in_array($phrase, $this->seen_phrases)) {
                continue;
            }

            if (! Key::where('name', $phrase)->exists()) {
                $missing_phrases->push($phrase);
            }

            $this->seen_phrases[] = $phrase;
        }

        return $missing_phrases;
    }

    private function findPhrases($file): Collection
    {
        $phrases = collect();

        $data = file_get_contents($file);
        preg_match_all('/transEdit\([\'|\"](.*?)[\'|\"]\)/', $data, $matches);

        return collect($matches[1]);
    }
}
