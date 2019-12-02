<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StoreTextCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TermImportance:store {text}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '計算対象の文字列と各単語のtfをDBに保管します。';

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
        $text = new \App\Models\Text();
        $text->text = $this->argument("text");
        $text->save();
        $text->setImportanceTerms();
    }
}
