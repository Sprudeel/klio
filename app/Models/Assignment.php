<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'deadline'   => 'datetime',
        'closed_at'  => 'datetime',
        'is_closed'  => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
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
