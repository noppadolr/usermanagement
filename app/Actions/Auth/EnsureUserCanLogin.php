<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Validation\ValidationException;

final class EnsureUserCanLogin
{
    public function handle(User $user): void
    {
        if (! $user->canLogin()) {
            throw ValidationException::withMessages([
                'email' => 'บัญชีนี้ยังไม่สามารถเข้าสู่ระบบได้ กรุณาติดต่อผู้ดูแลระบบ',
            ]);
        }
    }
}
