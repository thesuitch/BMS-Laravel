<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerCreateRequest;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomerTrait;

class CustomerController extends Controller
{
    use CustomerTrait;

    protected $level_id;
    protected $user_id;


    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->is_admin == 1) {
                $this->level_id = auth()->user()->user_id;
            } else {
                $this->level_id = auth()->user()->userinfo->created_by;
            }
            $this->user_id = auth()->user()->user_id;
            return $next($request);
        });
    }

    public function store(CustomerCreateRequest $request)
    {
        try {

            $customerData = $this->prepareCustomerData($request);
            $userInsertId = $this->insertCustomerUserInfo($customerData);
            $this->insertCustomerLogInfo($userInsertId, $request->input('username'), $request->input('password'));
            $this->insertCompanyProfile($userInsertId, $customerData);
            $customerInsertedId = $this->insertCustomer($userInsertId, $customerData);
            $this->insertShippingAddress($userInsertId, $customerInsertedId, $customerData);
            $this->insert_b_acc_coa($customerData);
            $this->insertCustomerPhone($userInsertId, $customerInsertedId, $customerData);
            $this->insertCustomerTaxId($userInsertId, $customerInsertedId, $customerData);
            $this->insertAccesslog($customerData);

            $queryCustomerInfo = DB::table('customers')
                ->where('level_id', $this->level_id)
                ->where('id', $customerInsertedId)
                ->first();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data inserted successfully',
                'data' => [
                    "customer" => $queryCustomerInfo,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data is not inserted' . $e,
                'data' => [
                    "customer" => [],
                ]
            ], 404);
        }
    }


    function getCustomer()
    {
        try {
            $customers = DB::table('customers')
                ->select('customers.*', 'customers.is_taxable as customer_is_taxable', 'customers.enable_shipping_zone as customer_enable_shipping_zone', 'users.*', 'user_info.*')
                ->join('users', 'customers.customer_user_id', '=', 'users.user_id')
                ->join('user_info', 'customers.customer_user_id', '=', 'user_info.id')
                ->where('customers.level_id', $this->level_id)
                ->where('users.status', 1)
                ->where('user_info.wholesaler_connection', 1)
                ->orderBy('customers.id', 'desc')
                ->get();

            if ($customers->isEmpty()) {
                throw new \Exception();
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => [
                    'customers' => $customers,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found' . $e->getMessage(),
                'data' => [
                    'customers' => [],
                ]
            ], 404);
        }
    }
}
