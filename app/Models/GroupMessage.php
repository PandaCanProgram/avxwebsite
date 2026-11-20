<?php

namespace App\Models;

use Database\Factories\GroupMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['body', 'client_id', 'user_id'])]
class GroupMessage extends Model
{
    /** @use HasFactory<GroupMessageFactory> */
    use HasFactory;

    /** @return BelongsTo<MessageGroup, $this> */
    public function group(): BelongsTo
    {
        return $this->belongsTo(MessageGroup::class, 'message_group_id');
    }

    /** @return BelongsTo<User, $this> */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
