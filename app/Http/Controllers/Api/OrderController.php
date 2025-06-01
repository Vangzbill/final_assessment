<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CpCustomer;
use App\Models\Nodelink;
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
            if (!$cp) {
                return $this->generateResponse('error', 'Failed to add data', null, 500);
            }

            return $this->generateResponse('success', 'Data added successfully', $cp, 201);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    private function validateOrder($request)
    {
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
            $this->validateOrder($request);
            $data = $request->all();
            $order = Order::createOrder($user_id, $data);
            if (!$order) {
                return $this->generateResponse('error', 'Failed to add data', null, 500);
            }

            $responseOrder = Order::getOrder($order->id, $user_id);
            if (!$responseOrder) {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }
            return $this->generateResponse('success', 'Data added successfully', $responseOrder, 200);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function created($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::getOrder($id, $user->id);
            if (!$order) {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }

            return $this->generateResponse('success', 'Data retrieved successfully', $order);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }
    public function history(Request $request)
    {
        try {
            if (!$token = JWTAuth::getToken()) {
                return $this->generateResponse('error', 'Token not provided', null, 401);
            }

            $user = JWTAuth::parseToken()->authenticate();

            $statusIds = $request->query('status_id');
            $page = $request->query('page', 1);

            if ($statusIds) {
                $statusIds = explode(',', $statusIds);
            }

            $orders = Order::getListOrder($user->id, $statusIds, $page);

            $pagination = [
                'current_page' => $orders->currentPage(),
                'total_pages' => $orders->lastPage(),
                'total_items' => $orders->total(),
                'per_page' => $orders->perPage(),
            ];

            return $this->generateResponse('success', 'Data retrieved successfully', [
                'orders' => $orders,
                'pagination' => $pagination,
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->generateResponse('error', 'Token has expired', null, 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->generateResponse('error', 'Token is invalid', null, 401);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function detail($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::getOrderDetail($id, $user->id);
            if (!$order) {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }

            return $this->generateResponse('success', 'Data retrieved successfully', $order);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function summary($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::getOrderSummary($id, $user->id);
            if (!$order) {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }

            return $this->generateResponse('success', 'Data retrieved successfully', $order);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function cekPayment($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::cekOrder($id, $user->id);
            if (!$order) {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }

            if ($order == 1) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Order has not been paid',
                    'paid' => 0,
                ], 200);
            } elseif ($order == 2) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Order has been paid',
                    'paid' => 1,
                ], 200);
            }
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function activate(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Nodelink::activateOrder($request->order_id, $user->id, $request->latitude, $request->longitude);
            if (!$order) {
                return $this->generateResponse('error', 'Failed to activate order', null, 500);
            }

            return $this->generateResponse('success', 'Order activated successfully', $order);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function address(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Nodelink::addAddressNode($request->order_id, $user->id, $request->provinsi, $request->kabupaten);
            if (!$order) {
                return $this->generateResponse('error', 'Failed to add address node', null, 500);
            }

            return $this->generateResponse('success', 'Add address node successfully', $order);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function delivered(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::deliveredOrder($request->order_id, $user->id);
            if (!$order) {
                return $this->generateResponse('error', 'Failed to accept order', null, 500);
            }

            return $this->generateResponse('success', 'Order accepted successfully', $order);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function tracking($resi)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::tracking($resi);
            if (!$order) {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }

            return $this->generateResponse('success', 'Data retrieved successfully', $order);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function tracingResi($resi)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::tracingResi($resi);
            if (!$order) {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }

            return $this->generateResponse('success', 'Data retrieved successfully', $order);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }
}
