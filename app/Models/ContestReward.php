<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestReward extends Model
{
    use HasFactory;
    protected $primaryKey = ['contest_id', 'rating'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'contest_rewards';
}
