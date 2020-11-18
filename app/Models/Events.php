<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;
	public $fillable = ['record_id', 'date', 'name', 'number_of_events'];


	public function Records()
	{
		$this->belongsTo(Records::class,'id');
	}
}
