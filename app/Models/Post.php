<?php

namespace App\Models;

use App\Observers\StatusObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([StatusObserver::class])]
class Post extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'body',
        'cover_image',
        'pinned',
    ];

    protected $with = ['tags'];

    public function tags(): BelongsToMany
    {
        // may bad the name of tag_post is reversed if it post_tag we can remove second arg
        return $this->belongsToMany(Tag::class, 'tag_post');
    }

    protected function casts(): array
    {
        return [
            'pinned' => 'bool',
        ];
    }
}
