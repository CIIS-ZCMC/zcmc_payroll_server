<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeductionGroupRequest;
use App\Http\Resources\DeductionGroupResource;
use App\Models\Deduction;
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
    public function index(Request $request)
    {
        if ($request->import_selection) {
            return $this->importSelection($request);
        }

        return response()->json([
            'data' => DeductionGroupResource::collection(DeductionGroup::whereNull('deleted_at')->get()),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
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
            'message' => "Data Successfully saved",
            'statusCode' => 200
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
                'message' => 'No record found.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
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
                'message' => "Data not found",
                'statusCode' => 404
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
                    'message' => 'Code already exist',
                    'statusCode' => 302
                ], Response::HTTP_FOUND);
            }
        }

        $data->update($request->all());

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Successfully update",
            'statusCode' => 200
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

        return response()->json([
            'message' => "Data Successfully deleted",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    public function importSelection(Request $request)
    {
        $payroll_period_id = $request->payroll_period_id;
        $deduction_id = $request->deduction_id;

        $deduction = Deduction::find($deduction_id);

        if (!$deduction) {
            return response()->json([
                'message' => 'Deduction not found.',
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $data = DeductionGroup::with([
            'deductions' => function ($q) use ($payroll_period_id, $deduction_id) {
                $q->with([
                    'employeeDeductions' => function ($query) use ($payroll_period_id) {
                        $query->where('payroll_period_id', $payroll_period_id);
                    },
                    'employeeDeductions.employee'
                ]);
            }
        ])->where('id', $deduction->deduction_group_id)
            ->whereNull('deleted_at')
            ->first();

        return response()->json([
            'data' => new DeductionGroupResource($data),
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }
}
