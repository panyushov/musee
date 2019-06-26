<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class ApplicationInit extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    const DB_CONFIG_KEY = "database.connections.sqlite.database";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify and generates app\'s resources.';

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
        $this->checkDBExists();
        $this->checkEnvFile();
        $this->launchWorker();
    }

    /**
     * Method checks if sqlite database exists.
     * If not - it creates it and launches migration command
     * to populate it with tables.
     */
    public function checkDBExists()
    {
        $dbFilePath = Config::get(self::DB_CONFIG_KEY);
        if (!File::exists($dbFilePath)) {
            touch($dbFilePath);
            Artisan::call("migrate");
        }
    }

    /**
     * Checks if env file exists and contains
     * application encryption key.
     * If not - it creates it and launches artisan
     * key generation procedure.
     *
     */
    private function checkEnvFile()
    {
        $pathToEnv = $this->laravel->environmentFilePath();
        if (!File::exists($pathToEnv)) {
            touch($pathToEnv);
            File::append($pathToEnv, 'APP_KEY=');
            Artisan::call("key:generate");
        }
    }

    /**
     *  Launches one background worker in non-blocking way.
     */
    public function launchWorker()
    {
        exec("php artisan queue:work >/dev/null 2>&1 &");
    }
}
