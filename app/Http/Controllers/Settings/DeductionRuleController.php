<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeductionRuleResource;
use App\Models\DeductionRule;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeductionRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'data' => DeductionRuleResource::collection(DeductionRule::all()),
            'message' => "Data Successfully retrieved"
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = DeductionRule::create($request->all());

        return response()->json([
            'data' => new DeductionRuleResource($data),
            'message' => "Data Successfully saved"
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
        $data = DeductionRule::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new DeductionRuleResource($data),
            'message' => "Data Successfully retrieved"
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
        $data = DeductionRule::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        $data->update($request->all());

        return response()->json([
            'data' => new DeductionRuleResource($data),
            'message' => "Data Successfully updated"
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
        $data = DeductionRule::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        $data->delete();

        return response()->json([
            'message' => "Data Successfully deleted"
        ], Response::HTTP_OK);
    }
}
