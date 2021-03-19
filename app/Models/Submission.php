<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;
    protected $primaryKey = ['id', 'contest_id'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'contest_id', 'address', 'contact', 'applied_at', 'file_link', 'forum_link', 'hash', 'total_mark',
        'avg_mark', 'accepted', 'abstained', 'rejected'
    ];
}
