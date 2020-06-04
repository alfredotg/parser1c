<?php

namespace App\Console\Commands;

trait MultiProc
{
    protected function resolveMaxProcs(int $proc_limit): int
    {
        $max_procs = (int) system('nproc --all', $ret);
        if ($ret != 0 || $max_procs > $proc_limit) {
            $max_procs = $proc_limit;
        }
        return $max_procs;
    }

    protected function fork(array $files, int $proc_limit, string $command): bool
    {
        $pids = [];
        $statuses = [];
        $max_procs = $this->resolveMaxProcs($proc_limit);

        printf("Run %s in %d processes\n", $command, $max_procs);

        foreach ($files as $file) {
            $pid = pcntl_fork();
            if ($pid == -1) {
                throw new \Exception('Could not fork');
            }
            if (!$pid) {
                $this->call($command, ['file' => $file]);
                exit();
            }
            $pids[$pid] = $file;
            if (count($pids) >= $max_procs) {
                $this->waitFirst($pids, $statuses);
            }
        }
        $this->waitAll($pids, $statuses);

        $success = true;
        foreach ($statuses as $file => $status) {
            if ($status != 0) {
                printf("%s - FAILED\n", $file);
                $success = false;
            } else {
                printf("%s - OK\n", $file);
            }
        }
        return $success;
    }

    protected function waitAll(array &$pids, array &$statuses): void
    {
        $this->wait($pids, $statuses, count($pids));
    }

    protected function waitFirst(array &$pids, array &$statuses): void
    {
        $this->wait($pids, $statuses, 1);
    }

    protected function wait(array &$pids, array &$statuses, int $count): void
    {
        foreach ($pids as $pid => $file) {
            if ($count <= 0) {
                return;
            }
            $count--;
            pcntl_waitpid($pid, $status);
            $statuses[$file] = $status;
            unset($pids[$pid]);
        }
    }
}
