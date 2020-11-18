<?php

namespace App\Http\Controllers\Api;

use App\Models\Events;
use App\Models\Records;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatasetController extends Controller
{

	public function getRecordWithEvents($id)
	{
		$record = Records::find($id);

		if ($record !== Null){
			$data = $record;
			$data['events'] = $record->events;
			return json_encode($data);
		}
		return json_encode(["error" => "Record $id not found."]);




    }
}
