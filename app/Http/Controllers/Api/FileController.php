<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CsvColumnCountNotEqualException;
use App\Exceptions\CsvReadFailedException;
use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Records;
use Illuminate\Http\Request;


class FileController extends Controller
{

	public function records(Request $request)
	{
		return $this->store($request, 2, function($row) {
			Records::updateOrCreate(
				['id' => $row[0]],
				['id' => $row[0],'name' => $row[1]]
			);
		});

	}

	public function events(Request $request)
	{
		return $this->store($request, 4, function($row) {
			$date =  date('Y-m-d',strtotime($row[1]));
			Events::updateOrCreate(
				['record_id' => $row[0],'date' => $date,'name' => $row[2]],
				['record_id' => $row[0],'date' => $date,'name' => $row[2],'number_of_events' => $row[3]]
			);
		});
	}


	private function store(Request $request, $columnCount, \Closure $callback)
	{
		if ($request->file === null){
			return response()->json(['error'=>"No file uploaded"]);
		} elseif (!$request->file('file')->isValid()) {
			return response()->json(['error'=>"File not valid"]);
		}

		try {
			$errorRows = $this->saveCsvWithValidation($request, $columnCount, $callback);

			if ($errorRows !== []){
				return response()->json([
					"error_rows" => $errorRows
				]);
			}

		} catch (CsvReadFailedException $e) {
			return response()->json(['error'=>"File is broken"]);
		} catch(CsvColumnCountNotEqualException $e) {
			return response()->json(['error'=>"Column count doesn't match"]);
		}

		return response()->json([
			"success" => True
		]);
	}


	/**
	 * @throws CsvReadFailedException
	 * @throws CsvColumnCountNotEqualException
	 */
	private function readCSV($csvFile, $delimiter)
	{
		$file_handle = fopen($csvFile, 'r');
		$columnCount = Null;
		while (!feof($file_handle)) {
			$row = fgetcsv($file_handle, 0, $delimiter);
			if ($row !== Null && $row !== False) {
				if ($columnCount === null) {
					$columnCount = count($row);
				} elseif ($columnCount != count($row)) {
					echo $columnCount." ".count($row)."\n";
					$columnCount = false;
				}
				$line_of_text[] = $row;
			}
		}
		fclose($file_handle);
		if (isset($line_of_text))
			return ['data' => $line_of_text, 'columnCount' => $columnCount];

		throw new CsvReadFailedException(sprintf('File: "%s" could not read.', $csvFile));
	}


	/**
	 * @throws CsvColumnCountNotEqualException
	 */
	private function saveCsvWithValidation(Request $request, $columnCount, \Closure $callback) {
		$csv = $this->readCSV($request->file('file')->getRealPath(), ',');

		if ($csv['columnCount'] != $columnCount) {
			throw new CsvColumnCountNotEqualException();
		}
		return $this->saveCsv($csv['data'], $callback);
	}


	private function saveCsv($csv, \Closure $callback) {
		$errorRows = [];
		foreach ($csv as $row){
			try {
				$callback($row);
			} catch (\Illuminate\Database\QueryException $e) {
				$errorRows[] = [$e->errorInfo[2], $row];
			}
		}

		return $errorRows;
	}
}
