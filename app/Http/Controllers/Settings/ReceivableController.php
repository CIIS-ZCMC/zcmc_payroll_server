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
    public function index(Request $request)
    {
        if ($request->pagination) {
            return $this->pagination($request);
        }

        return response()->json([
            'responseData' => ReceivableResource::collection(Receivable::whereNull('deleted_at')->get()),
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
    public function store(ReceivableRequest $request)
    {
        $validate = $request->validated();
        $validate_code = Receivable::whereNull('deleted_at')->where('code', $validate['code'])->first();

        if ($validate_code) {
            return response()->json(['message' => 'Code already exist'], Response::HTTP_FOUND);
        }

        if ($validate['type'] === null) {
            if ($validate['percent_value'] !== null) {
                $validate['type'] = 'percentage';
            } elseif ($validate['fixed_amount'] !== null) {
                $validate['type'] = 'fixed';
            }
        }

        $data = Receivable::create($validate);

        return response()->json([
            'data' => new ReceivableResource($data),
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
    public function show($id, Request $request)
    {
        $payroll_period_id = $request->payroll_period_id;

        $data = Receivable::with([
            'employeeReceivables' => function ($query) use ($payroll_period_id) {
                $query->where('payroll_period_id', $payroll_period_id);
            },
            'employeeReceivables.employee'
        ])->where('id', $id)->first();

        if (!$data) {
            return response()->json([
                'message' => "Data not found"
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new ReceivableResource($data),
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
        $data = Receivable::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Data not found",
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        // Check if code already exists in other receivables
        if ($request->has('code')) {
            $existing = Receivable::whereNull('deleted_at')
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
            'data' => new ReceivableResource($data),
            'message' => "Data Successfully updated",
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
        $data = Receivable::findOrFail($id);

        if (!$data) {
            return response()->json([
                'message' => "Data not found",
                'statusCode' => 404
            ], Response::HTTP_NOT_FOUND);
        }

        $data->delete();

        return response()->json([
            'message' => "Data Successfully deleted",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }

    public function pagination(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1|max:100'
        ]);

        $perPage = $validated['per_page'] ?? 10;
        $page = $validated['page'] ?? 1;

        $data = Receivable::whereNull('deleted_at')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'responseData' => [
                'data' => ReceivableResource::collection($data),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                ]
            ],
            'message' => "Data Successfully retrieved",
            'statusCode' => 200
        ], Response::HTTP_OK);
    }
}
