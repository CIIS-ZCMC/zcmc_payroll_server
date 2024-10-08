<?php

namespace App\Http\Controllers\Receivable;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReceivableRequest;
use App\Http\Resources\ReceivableResource;
use App\Models\Receivable;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    private $CONTROLLER_NAME = 'Receivable';
    private $PLURAL_MODULE_NAME = 'receivables';
    private $SINGULAR_MODULE_NAME = 'receivable';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return response()->json(['responseData' => ReceivableResource::collection(Receivable::all())], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReceivableRequest $request)
    {
        try {
            $data = Receivable::create($request->all());

            // Helpers::registerSystemLogs($request, $data->id, true, 'Success in creating ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['data' => new ReceivableResource($data), 'message' => "Successfully saved", 'statusCode' => Response::HTTP_OK]);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'store', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $data = Receivable::findOrFail($id);
            return response()->json(['data' => new ReceivableResource($data)], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'show', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = Receivable::findOrFail($id);

            if (!$data) {
                return response()->json(['message' => 'No record found.', 'statusCode' => Response::HTTP_NOT_FOUND]);
            }

            $data->update($request->all());

            // Helpers::registerSystemLogs($request, $id, true, 'Success in updating ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['data' => new ReceivableResource($data), 'message' => "Data Successfully update", 'statusCode' => Response::HTTP_OK]);


        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'update', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $data = Receivable::findOrFail($id);
            $data->delete();

            // Helpers::registerSystemLogs($request, $id, true, 'Success in delete ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['message' => "Data Successfully deleted"], Response::HTTP_OK);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'destroy', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function stop(Request $request, $id)
    {
        try {
            $data = Receivable::findOrFail($id);
            $data->status = $request->status;
            $data->stopped_at = Carbon::now();
            $data->update();

            if ($data != null) {
                $receivableTrailController = new ReceivableTrailController();
                $receivableTrailController->store($request);
            }

            // Helpers::registerSystemLogs($request, $id, true, 'Success in delete ' . $this->SINGULAR_MODULE_NAME . '.');
            return response()->json(['message' => "Data Successfully stop", 'statusCode' => Response::HTTP_OK]);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'stop', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
