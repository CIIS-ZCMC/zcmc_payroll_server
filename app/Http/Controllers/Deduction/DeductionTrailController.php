<?php

namespace App\Http\Controllers\Deduction;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionTrailRequest;
use App\Http\Resources\DeductionTrailResource;
use App\Models\DeductionTrail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeductionTrailController extends Controller
{

    private $CONTROLLER_NAME = 'Deduction Trail';
    private $PLURAL_MODULE_NAME = 'deduction trails';
    private $SINGULAR_MODULE_NAME = 'deduction trail';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return response()->json(['responseData' => DeductionTrailResource::collection(DeductionTrail::all())], Response::HTTP_OK);

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
            $data = DeductionTrail::create($request->all());

            // Helpers::registerSystemLogs($request, $data->id, true, 'Success in creating ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['data' => new DeductionTrailResource($data), 'message' => "Data Successfully saved", 'statusCode' => Response::HTTP_OK]);

        } catch (\Throwable $th) {

            // Helpers::errorLog($this->CONTROLLER_NAME, 'store', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
