<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReceivableRequest;
use App\Http\Resources\ReceivableResource;
use App\Models\Receivable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReceivableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'responseData' => ReceivableResource::collection(Receivable::all()),
            'message' => 'Receivables retrieved successfully.'
        ], Response::HTTP_OK);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReceivableRequest $request)
    {
        $data = Receivable::create($request->all());

        return response()->json([
            'data' => new ReceivableResource($data),
            'message' => "Successfully saved",
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Receivable::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Receivable not found",
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new ReceivableResource($data),
            'message' => "Receivable retrieved successfully",
        ], Response::HTTP_OK);
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
        $data = Receivable::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Receivable not found",
            ], Response::HTTP_NOT_FOUND);
        }

        $data->update($request->all());

        return response()->json([
            'data' => new ReceivableResource($data),
            'message' => "Receivable updated successfully",
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Receivable::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Receivable not found",
            ], Response::HTTP_NOT_FOUND);
        }

        $data->delete();

        return response()->json([
            'message' => "Receivable deleted successfully",
        ], Response::HTTP_OK);
    }
}
