<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\form;
use function Laravel\Prompts\outro;

class CreateNewUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Данная команда позволяет создать нового пользователя';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inputs = form()
            ->text(
                label: 'Имя нового пользователя',
                placeholder: 'Например: Иван',
                validate: ['first_name' => ['required', 'string', 'max:255']],
                name: 'first_name',
            )
            ->text(
                label: 'Фамилия нового пользователя',
                placeholder: 'Например: Иванов',
                validate: ['last_name' => ['required', 'string', 'max:255']],
                name: 'last_name',
            )
            ->text(
                label: 'Отчество нового пользователя',
                placeholder: 'Например: Иванович',
                validate: ['middle_name' => ['nullable', 'string', 'max:255']],
                hint: 'Если нет отчества, оставьте поле пустым',
                name: 'middle_name',
            )
            ->text(
                label: 'Адрес электронной почты нового пользователя',
                placeholder: 'Например: test@example.com',
                validate: ['email' => ['required', 'string', 'email', 'max:255', 'unique:users']],
                name: 'email'
            )
            ->password(
                label: 'Пароль нового пользователя',
                validate: ['password' => ['required', Password::defaults()]],
                hint: 'Постарайтесь придумать сложный пароль',
                name: 'password'
            )
            ->confirm(
                label: 'Является ли новый пользователь администратором?',
                default: false,
                yes: 'Да',
                no: 'Нет',
                name: 'is_admin'
            )
            ->submit();

        $inputs['password'] = Hash::make($inputs['password']);

        User::create($inputs);

        outro('Пользователь был успешно создан!');

        return 0;
    }
}
