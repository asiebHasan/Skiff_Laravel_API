<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'client_id',
        'status',
        'deadline'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    use HasFactory;
}
