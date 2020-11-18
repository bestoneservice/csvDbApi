<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Records extends Model
{
    use HasFactory;

	protected $fillable = ['id', 'name'];

	protected $dateFormat = 'm/d/Y';

	protected $dates = [
		'date' => 'Y-m-d'
	];

	public function Events()
	{
		return $this->hasMany(Events::class, 'record_id');

    }

}
