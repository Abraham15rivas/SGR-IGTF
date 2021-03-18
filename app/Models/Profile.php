<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profiles';

    protected $fillable = [
        'first_name',
        'second_name',
        'surname',
        'second_surname',
        'image',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
