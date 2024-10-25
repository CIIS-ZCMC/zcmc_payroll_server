<?php

namespace App\Http\Controllers\Adjustment;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ManagePaymentResource;
use App\Models\EmployeeList;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ManagePaymentController extends Controller
{
    private $CONTROLLER_NAME = 'Manage Payment';
    private $PLURAL_MODULE_NAME = 'manage payments';
    private $SINGULAR_MODULE_NAME = 'manage payment';

    public function index()
    {
        try {

            return response()->json(['responseData' => ManagePaymentResource::collection(EmployeeList::all())], Response::HTTP_OK);
        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
