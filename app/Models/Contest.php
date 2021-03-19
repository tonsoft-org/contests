<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id', 'balance', 'title', 'hash', 'last_paid', 'link', 'max_voting_attempts', 'jury_id',
        'start_at', 'end_at', 'voting_end_at'
    ];

    public function juries()
    {
        return $this->belongsToMany(Jury::class, 'contests_juries', 'contest_id', 'jury_id');
    }

    public function contenders()
    {
        return $this->belongsToMany(Contender::class, 'contenders_contests', 'contest_id', 'contender_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'contest_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'contest_id', 'id');
    }

    public function contest_rewards()
    {
        return $this->hasMany(ContestReward::class, 'contest_id', 'id')->orderBy('rating');
    }
}
