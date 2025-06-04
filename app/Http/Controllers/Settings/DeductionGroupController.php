<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionGroupRequest;
use App\Http\Resources\DeductionGroupResource;
use App\Models\DeductionGroup;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeductionGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'data' => DeductionGroupResource::collection(DeductionGroup::whereNull('deleted_at')->get()),
            'message' => "Data Successfully retrieved"
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeductionGroupRequest $request)
    {
        $validate = $request->validated();
        $validate_code = DeductionGroup::whereNull('deleted_at')->where('code', $validate['code'])->first();

        if ($validate_code) {
            return response()->json(['message' => 'Code already exist'], Response::HTTP_FOUND);
        }

        $data = DeductionGroup::create($validate);

        return response()->json([
            'data' => new DeductionGroupResource($data),
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
        $data = DeductionGroup::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => 'No record found.'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new DeductionGroupResource($data),
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
        $data = DeductionGroup::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        // Check if code already exists in other deductions group
        if ($request->has('code')) {
            $existing = DeductionGroup::whereNull('deleted_at')
                ->where('code', $request->input('code'))
                ->where('id', '!=', $id)
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Code already exist'
                ], Response::HTTP_FOUND);
            }
        }

        $data->update($request->all());

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Successfully update",
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
        $data = DeductionGroup::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => 'No record found.'
            ], Response::HTTP_NOT_FOUND);
        }

        $data->delete();

        return response()->json(['message' => "Data Successfully deleted"], Response::HTTP_OK);
    }
}
