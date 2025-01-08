<?php

namespace Dialect\TransEdit\Console\Commands;

use Dialect\TransEdit\Models\Key;
use Dialect\TransEdit\Models\Locale;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class AddMissingPhrases extends Command
{
    /**
     * By default, we create a migration.
     * Use --direct=1 to skip creating a migration and push directly to DB.
     */
    protected $signature = 'transedit:add-missing-phrases 
                            {--direct=0 : Whether to skip creating a migration and push phrases directly to DB}
                            {--locale=en : The locale to add the missing phrases to}';

    protected $description = 'Searches the resource directory for missing phrases.';

    private array $seen_phrases = [];

    public function handle()
    {
        // Collect all missing phrases
        $missing_phrases = $this->collectMissingPhrases();

        if ($missing_phrases->isEmpty()) {
            $this->info('No missing phrases found.');
            return;
        }

        // Check the --direct option; default is '0' (i.e., create a migration).
        $pushDirectly = (bool) $this->option('direct');

        if ($pushDirectly) {
            // Push directly to DB
            $this->pushDirectlyToDB($missing_phrases);
            $this->info("Successfully added {$missing_phrases->count()} phrases directly to the database.");
        } else {
            // Create a migration file
            $migrationPath = $this->createMigration($missing_phrases);
            $this->info("A migration has been created at: {$migrationPath}");
            $this->info("Run 'php artisan migrate' to insert the {$missing_phrases->count()} missing phrases into the database.");
        }
    }

    /**
     * Gather missing phrases from files in the resources directory.
     */
    private function collectMissingPhrases(): Collection
    {
        $iterator = new \RecursiveDirectoryIterator(resource_path());
        $search_extensions = ['php', 'vue', 'js'];
        $missing_phrases = collect();

        foreach (new \RecursiveIteratorIterator($iterator) as $file) {
            if ($file->isDir() || !in_array($file->getExtension(), $search_extensions)) {
                continue;
            }

            $missing_phrases = $missing_phrases->merge($this->findMissingPhrases($file));
        }

        return $missing_phrases->unique();
    }

    /**
     * Insert phrases directly into the database for all locales.
     */
    private function pushDirectlyToDB(Collection $phrases): void
    {
        $locales = Locale::all();

        foreach ($phrases as $phrase) {
            foreach ($locales as $locale) {
                transEdit($phrase, $phrase, $locale->name);
            }
        }
    }

    /**
     * Create a new migration file for inserting the missing phrases.
     */
    private function createMigration(Collection $phrases): string
    {
        $migrationName = date('Y_m_d_His') . '_add_missing_transedit_phrases.php';
        $migrationPath = database_path("migrations/{$migrationName}");

        $phrasesArray = var_export($phrases->values()->toArray(), true);
        $defaultLocale = $this->option('locale') ?: 'en';
        
        $migrationStub = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Dialect\TransEdit\Models\Locale;
use Dialect\TransEdit\Models\Key;

return new class extends Migration
{
    public function up()
    {
        \$locale = '{$defaultLocale}';
        \$missingPhrases = {$phrasesArray};

        // Insert each phrase into the database for all locales
        foreach (\$missingPhrases as \$phrase) {
            transEdit()->locale(\$locale)->setKey(\$phrase, \$phrase);
        }
    }

    public function down()
    {
        \$missingPhrases = {$phrasesArray};

        // Optionally remove these phrases if desired
        foreach (\$missingPhrases as \$phrase) {
            Key::where('name', \$phrase)->delete();
        }
    }
};
PHP;

        // Write the migration file
        file_put_contents($migrationPath, $migrationStub);

        return $migrationPath;
    }

    /**
     * Find all phrases in a single file and check if they're missing from the DB.
     */
    private function findMissingPhrases(\SplFileInfo $file): Collection
    {
        $missing_phrases = collect();
        $content = file_get_contents($file->getRealPath());

        // Find calls like transEdit('Some phrase here')
        preg_match_all('/transEdit\([\'|\"](.*?)[\'|\"]\)/', $content, $matches);

        $phrasesInFile = collect($matches[1]);

        foreach ($phrasesInFile as $phrase) {
            // Skip if we've already seen this phrase
            if (in_array($phrase, $this->seen_phrases)) {
                continue;
            }

            // Mark as missing if it doesn't exist in the DB
            if (! Key::where('name', $phrase)->exists()) {
                $missing_phrases->push($phrase);
            }

            // Add to the "seen" list
            $this->seen_phrases[] = $phrase;
        }

        return $missing_phrases;
    }
}