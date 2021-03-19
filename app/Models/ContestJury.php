<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestJury extends Model
{
    use HasFactory;
    protected $primaryKey = ['jury_id', 'contest_id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'contests_juries';
}
