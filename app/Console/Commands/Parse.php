<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Parse extends Command
{
    use MultiProc;

    const MAX_PROCS = 8;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse {dir}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse all xml files in {dir}';

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
        $files =  glob($this->argument('dir').'/import*.xml');
        $res = $this->fork($files, self::MAX_PROCS, 'parse:product');
        if(!$res)
            return;
        $files =  glob($this->argument('dir').'/offers*.xml');
        $res = $this->fork($files, self::MAX_PROCS, 'parse:offer');
        if(!$res)
            return;
    }
}
