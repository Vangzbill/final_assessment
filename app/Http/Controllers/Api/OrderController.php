<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CpRequest;
use App\Models\CpCustomer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{
    private function generateResponse($status, $message, $data = null, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    private function validateCp($request)
    {
        Validator::make($request->all(), [
            'customer_id' => ['required', 'exists:tbl_customer,id'],
            'nama' => ['required', 'string'],
            'email' => ['required', 'email'],
            'no_telp' => ['required', 'string'],
        ], [
            'customer_id.required' => 'Customer ID is required',
            'customer_id.exists' => 'Customer ID not found',
            'nama.required' => 'Name is required',
            'nama.string' => 'Name must be a string',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'no_telp.required' => 'Phone number is required',
            'no_telp.string' => 'Phone number must be a string',
        ])->validate();
    }

    public function addCp(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $request->merge(['customer_id' => $user->id]);

            $this->validateCp($request);
            $cp = CpCustomer::create($request->all());
            if(!$cp) {
                return $this->generateResponse('error', 'Failed to add data', null, 500);
            }

            return $this->generateResponse('success', 'Data added successfully', $cp, 201);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    private function validateOrder($request){
        Validator::make($request->all(), [
            'produk_id' => ['required', 'exists:tbl_produk,id'],
            'nama_layanan' => ['required', 'exists:tbl_layanan,nama_layanan'],
            'nama_cp' => ['required', 'string'],
            'email_cp' => ['required', 'email'],
            'no_telp_cp' => ['required', 'string'],
        ], [
            'produk_id.required' => 'Product ID is required',
            'produk_id.exists' => 'Product ID not found',
            'nama_layanan.required' => 'Service name is required',
            'nama_layanan.exists' => 'Service name not found',
            'nama_cp.required' => 'Name is required',
            'nama_cp.string' => 'Name must be a string',
            'email_cp.required' => 'Email is required',
            'email_cp.email' => 'Email must be a valid email address',
            'no_telp_cp.required' => 'Phone number is required',
            'no_telp_cp.string' => 'Phone number must be a string',
        ])->validate();
    }

    public function create(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $request->merge(['customer_id' => $user->id]);
            $user_id = $user->id;
            $email_cp = CpCustomer::where('email', $request->email_cp)->first();
            if($email_cp){
                return $this->generateResponse('error', 'Email already exists', null, 400);
            }
            $this->validateOrder($request);
            $data = $request->all();
            $order = Order::createOrder($user_id ,$data);
            if(!$order) {
                return $this->generateResponse('error', 'Failed to add data', null, 500);
            }

            return $this->generateResponse('success', 'Data added successfully', $order, 200);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function created($id){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::getOrder($id, $user->id);
            if(!$order) {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }

            return $this->generateResponse('success', 'Data retrieved successfully', $order);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function history(){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $orders = Order::getListOrder($user->id);
            return $this->generateResponse('success', 'Data retrieved successfully', $orders);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function detail($id){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::getOrderDetail($id, $user->id);
            if(!$order) {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }

            return $this->generateResponse('success', 'Data retrieved successfully', $order);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function summary($id){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::getOrderSummary($id, $user->id);
            if(!$order) {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }

            return $this->generateResponse('success', 'Data retrieved successfully', $order);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }
}
