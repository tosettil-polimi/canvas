<?php

namespace Canvas\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PostsTags extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'canvas_posts_tags';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the posts relationship.
     *
     * @return BelongsTo
     */
    public function posts(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the tags relationship.
     *
     * @return BelongsTo
     */
    public function tags(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
