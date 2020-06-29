<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;

class MakeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new User';

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
      // Disable in Production
      if(env('APP_ENV') == 'production') $this->error('Warning! Production-Mode');

      // Go Through Model
      $attributes = [];
      $model = new User();
      foreach ( $model->getFillable() as $i => $field) {
        // code...
        switch ($field) {
          case 'password':
            $attr = bcrypt($this->secret('Password?'));
            break;

          default:
            $attr = $this->ask($field);
            break;
        }

        $attributes[$field] = $attr;
      }

      // Ask and Create User
      $user = \App\User::create($attributes);
      $user->email_verified_at = mb_strtolower($this->ask("VerifyEmail? (Y/n)")) === 'n' ? null : date('Y-m-d H:i:s');
      $user->save();
      $this->info('Account created -> '. $user->id);

    }
}
