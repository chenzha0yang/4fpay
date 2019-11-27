<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class CbcGenerateCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cbc:generate
                    {--show : Display the key instead of modifying files}
                    {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the cbc key';

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
        $crypt = $this->generateRandomCbc();

        if ($this->option('show')) {
            return $this->line('<comment>' . $crypt . '</comment>');
        }

        if (!$this->setKeyInEnvironmentFile($crypt)) {
            return;
        }

        $this->laravel['config']['app.cbc'] = $crypt;

        $this->info("CBC key [$crypt] set successfully.");
    }

    /**
     * Generate a random key for the cbc.
     *
     * @return string
     */
    protected function generateRandomCbc()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Set the application key in the environment file.
     *
     * @param  string $key
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $currentKey = $this->laravel['config']['app.cbc'];

        if (strlen($currentKey) !== 0 && (!$this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param  string $crypt
     * @return void
     */
    protected function writeNewEnvironmentFileWith($crypt)
    {
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->keyReplacementPattern(),
            'CBC_KEY=' . $crypt,
            file_get_contents($this->laravel->environmentFilePath())
        ));
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern()
    {
        $escaped = preg_quote('=' . $this->laravel['config']['app.cbc'], '/');

        return "/^CBC_KEY{$escaped}/m";
    }
}
