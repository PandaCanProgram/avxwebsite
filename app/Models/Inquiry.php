<?php

namespace App\Models;

use Database\Factories\InquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

#[Fillable(['submission_id', 'kind', 'name', 'email', 'organization', 'phone', 'interest', 'subject', 'message'])]
class Inquiry extends Model
{
    /** @use HasFactory<InquiryFactory> */
    use HasFactory, HasUlids;

    protected static function booted(): void
    {
        static::creating(function (Inquiry $inquiry): void {
            for ($attempt = 0; $attempt < 20; $attempt++) {
                $referenceCode = '';

                for ($letter = 0; $letter < 4; $letter++) {
                    $referenceCode .= chr(random_int(65, 90));
                }

                if (! static::where('reference_code', $referenceCode)->exists()) {
                    $inquiry->reference_code = $referenceCode;

                    return;
                }
            }

            throw new RuntimeException('Unable to allocate a unique inquiry reference.');
        });
    }

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
        return 'AVX-'.($this->reference_code ?? $this->getKey());
    }
}
