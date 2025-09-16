<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    protected $fillable = [
        'name',
        'code',
        'deadline',
        'author_id',
        'is_closed',
        'closed_at',
        'color',
        'icon',
        'description',
        'closed_at',
        'isClosed',
    ];

    protected $casts = [
        'deadline'   => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'code', 'code');
    }

    public function getIsActiveAttribute(): bool
    {
        return !$this->is_closed && ($this->deadline === null || now()->lessThan($this->deadline));
    }

    public function getDeadlineFormattedAttribute(): ?string
    {
        return $this->deadline ? $this->deadline->format('d.m.Y H:i') : null;
    }
}
