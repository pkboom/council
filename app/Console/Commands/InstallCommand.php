<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'council:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simplify installation process';

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
        $this->welcome();

        $this->createEnvFile();

        if (strlen(config('app.key')) === 0) {
            $this->call('key:generate');

            $this->line('~ Secret key properly generated.');
        }

        $this->updateEnvironmentFile($this->requestDatabaseCredentials());

        if ($this->confirm('Do you want to migrate the database?', false)) {
            $this->call('migrate');

            $this->line('~Database successfully migrated.');
        }

        $this->goodbye();
    }

    public function welcome()
    {
        $this->info('>> Welcome to the Council installation process! <<');
    }

    public function createEnvFile()
    {
        if (!file_exists('.env')) {
            exec('cp .env.example .env');

            $this->line('.env file successfully created');
        }
    }

    public function requestDatabaseCredentials()
    {
        return [
            'DB_DATABASE' => $this->ask('Database name'),
            'DB_USERNAME' => $this->ask('Database user'),
            'DB_PASSWORD' => $this->secret('Database password ("null") for no password)'),
        ];
    }

    public function updateEnvironmentFile($updatedValues)
    {
        $envFile = $this->laravel->environmentFilePath();

        foreach ($updatedValues as $key => $value) {
            file_put_contents($envFile, preg_replace(
                "/{$key}=(.*)/",
                "{$key}={$value}",
                file_get_contents($envFile)
            ));
        }
    }

    public function goodbye()
    {
        $this->info('>> The installation process is complete. Enjoy your new forum! <<');
    }
}
