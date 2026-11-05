<?php

namespace App\Models;

use Database\Factories\AlphaRegistrationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'consented_at'])]
class AlphaRegistration extends Model
{
    /** @use HasFactory<AlphaRegistrationFactory> */
    use HasFactory, HasUlids;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['consented_at' => 'datetime', 'team_notified_at' => 'datetime'];
    }
}
