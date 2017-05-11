<?php

namespace Yuanben\CommandOnce\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CommandOnce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:once';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute commands only once.';

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
        $execs = config('command.execs');

        if (empty($execs)) {
            $this->info('The execs array is empty now, we have nothing to do.');
            return true;
        }

        $this->process($execs);
        $this->info('All command executed.');
    }

    /**
     * Execute the listed command, if it's never executed.
     *
     * @param $execs
     */
    public function process($execs)
    {
        $processed = $this->getProcessed();
        foreach ($execs as $command => $version) {
            if (array_key_exists($command, $processed)) {
                if ($processed[$command] == $version) {
                    continue;
                }
            }

            try {
                $this->info('');
                $this->info('Start execute command: ' . $command);

                list($commandName, $arguments) = $this->splitCommand($command);
                $this->call($commandName, $arguments);

                $this->info('Command: '. $commandName);
                logger($arguments);
                $this->info('Arguments: '. json_encode($arguments));

                $this->info('Execute command ' . $command . ' Success.');
                $this->info('');
                $this->saveLog($command, $version);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

    /**
     * List command name and arguments from command string.
     *
     * @param $command
     * @return array
     */
    protected function splitCommand($command)
    {
        $collect = collect(explode(' ', $command));
        $commandName = $collect->first();
        $collect->forget(0);

        $filtered = $collect->filter(function ($item) {
            return $item !== '';
        })->toArray();

        $filtered = array_values($filtered);

        $arguments = [];
        foreach ($filtered as $argument) {
            $argument = trim($argument, '{');
            $argument = trim($argument, '}');
            $argument = explode(':', $argument);
            $argument = [$argument[0] => $argument[1]];
            $arguments = array_merge($arguments, $argument);
        }

        return [$commandName, $arguments];
    }

    /**
     * Get executed commands from database.
     *
     * @return array
     */
    public function getProcessed()
    {
        $items = $this->db()->get();
        $processed = [];

        foreach ($items as $item) {
            $processed = array_merge($processed, [$item->command => $item->version]);
        }

        return $processed;
    }

    /**
     * Save the executed command and its version to database.
     *
     * @param $command
     * @param $version
     */
    public function saveLog($command, $version)
    {
        $query = $this->db()->where('command', $command);
        $log = $query->first();

        if (! $log) {
            $this->db()->insert([compact('command', 'version')]);
        } else {
            $query->update(['version' => $version]);
        }
    }

    /**
     * Get a fresh query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function db()
    {
        return DB::table('command_once');
    }
}
