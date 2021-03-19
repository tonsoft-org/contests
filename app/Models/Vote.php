<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;
    protected $primaryKey = ['jury_id', 'contest_id'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['submission_id', 'contest_id', 'jury_id', 'type', 'type_text', 'mark', 'comment'];
}
