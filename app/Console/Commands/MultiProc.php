<?php

namespace App\Console\Commands;

trait MultiProc
{
    protected function fork(array $files, int $proc_limit, string $command): bool
    {
        $pids = [];
        $statues = [];
        $max_procs = (int) system('nproc --all', $ret);
        if($ret != 0 || $max_procs > $proc_limit)
            $max_procs = $proc_limit;
        printf("Run %s in %d processes\n", $command, $max_procs);
        foreach($files as $file)
        {
            $pid = pcntl_fork();
            if($pid == -1)
                throw new \Exception('Could not fork');
            if(!$pid)
            {
                $this->call($command, ['file' => $file]);
                exit();;
            }
            $pids[$pid] = $file;
            if(count($pids) >= $max_procs)
                $this->wait($pids, $statues, false);
        }
        $this->wait($pids, $statues, true);

        $success = true;
        foreach($statues as $file => $status)
        {
            if($status != 0)
            {
                printf("%s - FAILED\n", $file);
                $success = false;
            }
            else
                printf("%s - OK\n", $file);
        }
        return $success;
    }

    protected function wait(array &$pids, array &$statues, bool $all): void
    {
        foreach($pids as $pid => $file)
        {
            pcntl_waitpid($pid, $status);
            $statues[$file] = $status;
            unset($pids[$pid]);
            if(!$all)
                return;
        }
    }
}
