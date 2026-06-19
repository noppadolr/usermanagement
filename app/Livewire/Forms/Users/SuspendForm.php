<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Users;

use App\DTOs\Users\SuspendUserData;
use Livewire\Form;

final class SuspendForm extends Form
{
    public string $reason = '';

    public string $adminKeyPass = '';

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
            'adminKeyPass' => ['required', 'string'],
        ];
    }

    public function toDto(): SuspendUserData
    {
        $this->validate();

        return new SuspendUserData(
            reason: $this->reason,
            adminKeyPass: $this->adminKeyPass,
        );
    }

    public function clear(): void
    {
        $this->reason = '';
        $this->adminKeyPass = '';
    }
}
