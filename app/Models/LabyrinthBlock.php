<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabyrinthBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'labyrinth_id',
        'x',
        'y',
        'passable',
    ];

    protected $casts = [
        'x' => 'integer',
        'y' => 'integer',
        'passable' => 'boolean',
    ];

    public function labyrinth(): BelongsTo
    {
        return $this->belongsTo(Labyrinth::class);
    }
}
