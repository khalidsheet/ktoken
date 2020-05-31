<?php

namespace Khalidmsheet\Ktoken\Commands;

use Illuminate\Console\Command;
use Khalidmsheet\Ktoken\Ktoken;

class GenerateKtokenPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ktoken:password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new password for KToken';
    /**
     * @var Ktoken
     */
    private $ktoken;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->ktoken = new Ktoken();
    }

    /**
     * Execute the console command.
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {

        $this->warn('Generating Password Key for KToken.');

        $password = $this->ktoken->generateKey();

        $this->warn("Updating ENV file");

        $this->writeKey($password);

        $this->info("KToken is ready to use.");
    }

    private function writeKey($key)
    {
        $path = base_path('.env');

        if ( file_exists($path) ) {
            file_put_contents($path, str_replace(
                'KTOKEN_PASSWORD=' . env('KTOKEN_PASSWORD'), 'KTOKEN_PASSWORD=' . $key, file_get_contents($path)
            ));
        }
    }
}
