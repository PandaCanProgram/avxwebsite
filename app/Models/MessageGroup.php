<?php

namespace App\Models;

use Database\Factories\MessageGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name'])]
class MessageGroup extends Model
{
    /** @use HasFactory<MessageGroupFactory> */
    use HasFactory;

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, $this> */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'direct_user_id');
    }

    public function isDirect(): bool
    {
        return $this->direct_user_id !== null;
    }

    public function displayName(): string
    {
        return $this->isDirect() ? ($this->recipient?->name ?? 'Team Member') : $this->name;
    }

    /** @return BelongsToMany<User, $this> */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps()->orderBy('users.name');
    }

    /** @return HasMany<GroupMessage, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class);
    }

    /** @return HasOne<GroupMessage, $this> */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(GroupMessage::class)->latestOfMany();
    }

    public function isOwnedBy(User $user): bool
    {
        return $user->is_admin && $this->created_by === $user->id;
    }
}
