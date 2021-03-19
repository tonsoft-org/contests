<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContenderContest extends Model
{
    use HasFactory;
    protected $primaryKey = ['contender_id', 'contest_id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'contenders_contests';
}
