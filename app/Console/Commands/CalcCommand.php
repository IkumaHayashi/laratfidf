<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TermImportance:calc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '各単語のidfを計算します。';

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
        $texts = \App\Models\Text::all();
        foreach ($texts as $text) {

            $importanceTerms = $text->importanceTerms()->get();
            foreach ($importanceTerms as $importanceTerm) {

                $hasTermCount = \App\Models\ImportanceTerm::where('term', $importanceTerm->term)
                                ->distinct()
                                ->count();

                $idf = $hasTermCount > 0 ? log10(count($texts) / $hasTermCount) : log10(0);

                $importanceTerm->idf = $idf;
                $importanceTerm->save();

            }
        }
    }
}
