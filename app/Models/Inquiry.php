<?php

namespace App\Models;

use Database\Factories\InquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['submission_id', 'kind', 'name', 'email', 'organization', 'phone', 'interest', 'subject', 'message'])]
class Inquiry extends Model
{
    /** @use HasFactory<InquiryFactory> */
    use HasFactory, HasUlids;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'team_notified_at' => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function reference(): string
    {
        return 'AVX-'.$this->getKey();
    }
}
