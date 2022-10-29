<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

#Installed packages - start
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Http\Range\Range;
#Installed packages - end

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Route::get('/', function(Request $request){
Route::get('/downloadFile', function(ServerRequestInterface $request){
    //FILE PATH
    $fullPath = dirname(__DIR__,1)."/resources/addresses.csv";
    $fileToTransfer = fopen($fullPath,'r');
    //GET TOTAL SIZE OF FILE IN BYTES
    $total_size = fileSize($fullPath);
    //DESTRUCT RANGES FROM REQUEST HEADER
    $range = new Range($request,$total_size);
    $parsed_range = $range->getUnit()->getRanges();
    $range_start = $parsed_range[0]->getStart();
    $range_end = $parsed_range[0]->getEnd();
   //STREAM FILE RANGE
    $file_stream = stream_get_contents($fileToTransfer, $range_end, $range_start);
    //CONTENT-LENGTH OF TRANSFERRED RANGE
    $content_length = $range_end - $range_start;
    //MOUNT HEADERS ARRAY
    $headersArray = array(
        'Accept-Ranges'=> 'bytes',
        'Content-Type'=> 'text/csv',
        'Content-Range'=> "bytes {$range_start}-{$range_end}/{$total_size}",
        'Content-Length'=> $content_length,
    );
    $callbackFn = function() use($file_stream)  {
        echo($file_stream);
    };
    //STREAM CONTENT AS RESPOSE
    return response()->stream($callbackFn, 206, $headersArray);
});
