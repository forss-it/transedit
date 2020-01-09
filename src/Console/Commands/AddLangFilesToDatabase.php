<?php

namespace Dialect\TransEdit\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddLangFilesToDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transedit:addLangFilesToDatabase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds translations from existing Laravel Lang-files into transedit tables';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $translations = collect([
            'php' => $this->phpTranslations(),
            'json' => $this->jsonTranslations(),
        ]);

        $translations->each(function ($item, $key) {
            if($item && !empty($item) && !is_array($item)){
                $item->each(function ($k, $trans) {
                    if($trans && !empty($k)){
                        info($trans);
                        $lang = explode(".", $trans);
                        foreach($k as $langKey => $langVal) {
                            transEdit($lang[1] . '.' . $langKey, $langVal, $lang[0]);
                        }
                    }
                });
            }
        });
    }

    private function phpTranslations()
    {
        $path = resource_path('lang');

        return collect(File::allFiles($path))->flatMap(function ($file) {
            if($file->getBasename('.php') == 'auth' || $file->getBasename('.php') == 'validation' || $file->getExtension() != 'php') {
                return [];
            }
            $key = ($translation = $file->getBasename('.php'));

            return [$file->getRelativePath() . '.' . $key => trans($translation, [], $file->getRelativePath())];
        });
    }

    private function jsonTranslations()
    {
        $path = resource_path('lang');

        if (is_string($path) && is_readable($path)) {
            return collect(File::allFiles($path))->map(function ($file) use ($path) {
                if($file->getExtension() == 'json'){
                    return [$file->getRelativePath() . '.' . $file->getBasename('.json') => json_decode(file_get_contents($file->getRealPath()), true)];
                }
            });
        }

        return [];
    }
}
