<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "category_id", "title", "content",
    ];

    protected $perPage = 5;

    protected static function boot() {
        parent::boot();
        self::creating(function (Article $article) {
            $article->user_id = auth()->id();
        });
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function getCreatedAtFormattedAttribute(): string {
        return \Carbon\Carbon::parse($this->created_at)->format('d-m-Y H:i');
    }

    public function getExcerptAttribute(): string {
        return Str::excerpt($this->content);
    }
}
