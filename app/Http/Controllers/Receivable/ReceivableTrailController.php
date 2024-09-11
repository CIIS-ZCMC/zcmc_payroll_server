<?php

namespace App\Http\Controllers\Receivable;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReceivableTrailRequest;
use App\Http\Resources\ReceivableTrailResource;
use App\Models\ReceivableTrail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReceivableTrailController extends Controller
{

    private $CONTROLLER_NAME = 'Receivable Trail';
    private $PLURAL_MODULE_NAME = 'receivable trails';
    private $SINGULAR_MODULE_NAME = 'receivable trail';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return response()->json(['responseData' => ReceivableTrailResource::collection(ReceivableTrail::all())], Response::HTTP_OK);

        } catch (\Throwable $th) {

            // Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = ReceivableTrail::create($request->all());

            // Helpers::registerSystemLogs($request, $data->id, true, 'Success in creating ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['data' => new ReceivableTrailResource($data), 'message' => "Data Successfully saved", 'statusCode' => Response::HTTP_OK]);

        } catch (\Throwable $th) {

            // Helpers::errorLog($this->CONTROLLER_NAME, 'store', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
