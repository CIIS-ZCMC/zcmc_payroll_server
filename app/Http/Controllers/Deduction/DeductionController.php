<?php

namespace App\Http\Controllers\Deduction;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class DeductionController extends Controller
{
    private $CONTROLLER_NAME = 'Deduction';
    private $PLURAL_MODULE_NAME = 'deductions';
    private $SINGULAR_MODULE_NAME = 'deduction';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            return "Test";
        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'assignChiefByEmployeeID', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // Helpers::registerSystemLogs($request, $id, true, 'Success in assigning division chief ' . $this->PLURAL_MODULE_NAME . '.');


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
