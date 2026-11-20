<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

#[Signature('admin:create
    {--email=admin@avxengineering.com : Email address for the administrator}
    {--password-stdin : Read the password from standard input}')]
#[Description('Create an administrator without changing existing accounts')]
class CreateAdminUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = strtolower(trim((string) $this->option('email')));
        $emailValidator = Validator::make(['email' => $email], [
            'email' => ['required', 'email:rfc', 'max:254', Rule::unique('users', 'email')],
        ]);

        if ($emailValidator->fails()) {
            $this->error($emailValidator->errors()->first());

            return self::FAILURE;
        }

        if (! $this->option('password-stdin') && ! $this->input->isInteractive()) {
            $this->error('Supply --password-stdin or run interactively to enter a password.');

            return self::FAILURE;
        }

        $password = $this->option('password-stdin')
            ? rtrim((string) stream_get_contents(STDIN), "\r\n")
            : $this->secret('Password');
        $passwordValidator = Validator::make(['password' => $password], [
            'password' => ['required', 'string', 'min:12', 'max:1024'],
        ]);

        if ($passwordValidator->fails()) {
            $this->error($passwordValidator->errors()->first());

            return self::FAILURE;
        }

        $user = new User(['name' => 'AVX Administrator', 'email' => $email, 'password' => $password]);
        $user->is_admin = true;
        $user->save();

        $this->info('Administrator created: '.$email);

        return self::SUCCESS;
    }
}
