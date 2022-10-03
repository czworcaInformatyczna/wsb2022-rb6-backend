<?php

namespace App\Console\Commands;

use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DebugPasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:reset {email} {token=foo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Requests password reset';

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
     * @return int
     */
    public function handle()
    {
        PasswordReset::create([
            'email' => $this->argument('email'),
            'token' => Hash::make($this->argument('token')),
            'created_at' => Carbon::now()
        ]);
        $url = '/resetpassword?email='.$this->argument('email').'&token='.$this->argument('token');
        $this->newLine();
        echo ('Adres: '.env('APP_URL').$url);
        $this->newLine(2);
        echo ('Local adress: http://127.0.0.1:8000'.$url);
        $this->newLine();
        return 0;
    }
}
