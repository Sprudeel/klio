<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Submission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'assignment_id',
        'student_name',
        'student_email',
        'code',
        'original_filename',
        'storage_path',
        'file_size',
        'mime_type',
        'checksum',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    /* =========================
     |  Relationships
     ==========================*/
    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'code', 'code');
    }

    /* =========================
     |  Accessors / Helpers
     ==========================*/
    public function getUrlAttribute(): ?string
    {
        return $this->storage_path
            ? Storage::disk('private')->url($this->storage_path)
            : null;
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('submissions.download', $this);
    }

    // Late if assignment has a deadline and submitted after it
    public function getIsLateAttribute(): bool
    {
        $deadline = optional($this->assignment)->deadline;
        return $deadline ? $this->submitted_at?->greaterThan($deadline) ?? false : false;
    }

    // Human status: "pünktlich" / "verspätet"
    public function getStatusLabelAttribute(): string
    {
        return $this->is_late ? 'verspätet' : 'pünktlich';
    }

    // Small, shareable code (fallback if not set)
    public static function generateCode(int $length = 6): string
    {
        // Base32-ish (no confusing chars)
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        return collect(range(1, $length))
            ->map(fn () => $alphabet[random_int(0, strlen($alphabet)-1)])
            ->implode('');
    }

    /* =========================
     |  Scopes
     ==========================*/
    public function scopeForAssignment($query, int $assignmentId)
    {
        return $query->where('assignment_id', $assignmentId);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('student_name', 'like', "%{$term}%")
                ->orWhere('student_email', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%")
                ->orWhere('original_filename', 'like', "%{$term}%");
        });
    }

    /* =========================
     |  Mutators
     ==========================*/
    public function setStudentEmailAttribute(?string $value): void
    {
        $this->attributes['student_email'] = $value ? Str::of($value)->trim()->lower() : null;
    }

    /* =========================
     |  Model events
     ==========================*/
    protected static function booted(): void
    {
        static::creating(function (Submission $s) {
            // default submitted_at
            $s->submitted_at ??= now();

            // assign code if empty
            $s->code = $s->code ?: static::generateCode();

            // ensure uniqueness within an assignment if you have a unique index
            // (assignment_id, code). If a collision occurs, regenerate once.
        });
    }
}
