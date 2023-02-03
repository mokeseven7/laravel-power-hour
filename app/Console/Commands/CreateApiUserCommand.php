<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateApiUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a user whos credentials can be exchanged for API Tokens';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->ask('enter a name');
        $username = $this->ask('enter an email/username (username for authentication) ');
        $password = $this->secret('Choose A Password (password for authentication) ');
        $password_confirm = $this->secret('Confirm The Password');

        if($password !== $password_confirm){
            $this->output->error('Passwords Do Not Match, Aborting');
            return Command::FAILURE;
        }

        if ($this->confirm("Username: {$username} | Password: {$password}", true)) {
            $user = new User;
            $user->fill(['name' => $name, 'email' => $username, 'password' => Hash::make($password)]);
            $user->save();
            $user->markEmailAsVerified();

            $this->output->success('Success! ' . 'Basic ' . base64_encode($username . ':' . $password));
            
            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }
}
