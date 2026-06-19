<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Users;

use App\DTOs\Users\UserData;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

final class UserForm extends Form
{
    public ?int $id = null;

    public string $name = '';

    public string $email = '';

    public string $role = 'user';

    public string $status = 'pending';

    public ?int $positionId = null;

    public ?int $workUnitId = null;

    public ?string $password = null;

    public ?string $keyPass = null;

    public function setUser(User $user): void
    {
        $this->id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
        $this->status = $user->status->value;
        $this->positionId = $user->position_id;
        $this->workUnitId = $user->work_unit_id;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->id),
            ],
            'role' => ['required', Rule::enum(UserRole::class)],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'positionId' => ['nullable', 'integer', 'exists:positions,id'],
            'workUnitId' => ['nullable', 'integer', 'exists:work_units,id'],
            'password' => [$this->id === null ? 'required' : 'nullable', 'string', 'min:3', 'max:255'],
            'keyPass' => ['nullable', 'string', 'min:4', 'max:255'],
        ];
    }

    public function toDto(): UserData
    {
        $this->validate();

        return new UserData(
            name: $this->name,
            email: $this->email,
            role: UserRole::from($this->role),
            status: UserStatus::from($this->status),
            positionId: $this->positionId,
            workUnitId: $this->workUnitId,
            password: $this->password,
            keyPass: $this->keyPass,
        );
    }
}
