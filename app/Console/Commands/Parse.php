<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Parse extends Command
{
    const MAX_PROCS = 4;

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
        $pids = [];
        $statues = [];
        $max_procs = (int) system('proc --all', $ret);
        if($ret != 0 || $max_procs > self::MAX_PROCS)
            $max_procs = self::MAX_PROCS;
        foreach(glob($this->argument('dir').'/import*.xml') as $file)
        {
            $pid = pcntl_fork();
            if($pid == -1)
                throw new \Exception('Could not fork');
            if(!$pid)
            {
                $this->call('parse:product', ['file' => $file]);
                exit();;
            }
            $pids[$pid] = $file;
            if(count($pids) >= $max_procs)
                $this->wait($pids, $statues);
        }
        $this->wait($pids, $statues);

        foreach($statues as $file => $status)
        {
            if($status != 0)
                printf("%s - FAILED\n", $file);
            else
                printf("%s - OK\n", $file);
        }

    }

    private function wait(array &$pids, array &$statues): void
    {
        foreach($pids as $pid => $file)
        {
            pcntl_waitpid($pid, $status);
            $statues[$file] = $status;
        }
        $pids = [];
    }
}
