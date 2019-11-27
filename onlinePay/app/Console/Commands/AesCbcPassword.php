<?php

namespace App\Console\Commands;

use Illuminate\Encryption\Encrypter;
use Illuminate\Console\Command;

class AesCbcPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:env {key} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make ENV KEY';

    /**
     * Create a new command instance.
     *
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
        $password = $this->argument('password');
        $key = $this->argument('key');
        $crypt = new Encrypter(hex2bin(config('app.cbc')), config("app.cipher"));
        $pwd = $crypt->encrypt($password);
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->keyReplacementPattern($key),
            "{$key}={$pwd}",
            file_get_contents($this->laravel->environmentFilePath())
        ));

        $this->info(" {$key} " . PHP_EOL . " [$pwd] " . PHP_EOL . " set successfully." );
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */

    /**
     * Get a regex pattern that will match env $key with any random key.
     *
     * @param $key
     * @return string
     */
    protected function keyReplacementPattern($key)
    {
        $escaped = preg_quote('='.env($key), '/');

        return "/^{$key}{$escaped}/m";
    }
}
