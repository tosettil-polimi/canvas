<?php

namespace Canvas\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;

class UserMeta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'canvas_user_meta';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be casted.
     *
     * @var array
     */
    protected $casts = [
        'digest' => 'boolean',
        'dark_mode' => 'boolean',
    ];

    /**
     * Get the user relationship.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('canvas.user', User::class));
    }

    /**
     * Scope a query to only include meta for a given user.
     *
     * @param $query
     * @param $user
     * @return Builder
     */
    public function scopeForUser($query, $user): Builder
    {
        return $query->where('user_id', $user->id);
    }
}