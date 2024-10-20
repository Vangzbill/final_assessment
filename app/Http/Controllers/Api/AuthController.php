<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegisterData;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private function generateResponse($statusMessage, $message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'status' => $statusMessage,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username is required',
            'password.required' => 'Password is required',
        ]);

        if ($validator->fails()) {
            return $this->generateResponse('error', $validator->errors()->first(), null, 400);
        }

        $customer = Customer::where('username', $request->username)->first();
        if (!$customer) {
            return $this->generateResponse('error', 'Invalid username or password', null, 401);
        }

        if (!Hash::check($request->password, $customer->password)) {
            return $this->generateResponse('error', 'Invalid username or password', null, 401);
        }

        $token = JWTAuth::fromUser($customer);
        if (!$token) {
            return $this->generateResponse('error', 'Unauthorized', null, 401);
        } else {
            return $this->generateResponse('success', 'Login success', ['token' => $token], 200);
        }
    }

    public function me()
    {
        try {
            $customer = JWTAuth::parseToken()->authenticate();

            if (!$customer) {
                return $this->generateResponse('error', 'Customer not found', null, 404);
            }

            $customer = Customer::find($customer->id);

            if (!$customer) {
                return $this->generateResponse('error', 'Customer not found', null, 404);
            }

            return $this->generateResponse('success', 'Customer data', $customer, 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->generateResponse('error', 'Token is Invalid', null, 401);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->generateResponse('error', 'Token is Expired', null, 401);

        } catch (Exception $e) {
            return $this->generateResponse('error', 'Authorization Token not found', null, 401);
        }
    }

    public function logout()
    {
        $token = JWTAuth::getToken();
        if ($token) {
            JWTAuth::invalidate($token);
        }
        return $this->generateResponse('success', 'Logout success', null, 200);
    }

    private function validateForm(Request $request){
        return Validator::make($request->all(), [
            'nama_perusahaan' => 'required|string|max:50|min:3',
            'email_perusahaan' => 'required|email|min:6',
            'no_telp_perusahaan' => 'required|string|max:13',
            'npwp_perusahaan' => [
                'required',
                'string',
                'min:15',
                'max:20',
            ],
            'username' => [
                'required',
                'min:6',
                'max:30',
                'regex:/^[a-zA-Z0-9_]+$/'
            ],
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=,.\/\\|{}[\];:\'"])[A-Za-z\d!@#$%^&*()_\-+=,.\/\\|{}[\];:\'"]{8,}$/'
            ],
            'alamat' => 'required|string|min:10',
            'provinsi_id' => 'required|string',
            'kota_id' => 'required|string',
            'kecamatan_id' => 'required|string',
            'kelurahan_id' => 'required|string',
        ], [
            'nama_perusahaan.required' => 'Nama perusahaan harus diisi.',
            'nama_perusahaan.string' => 'Nama perusahaan harus berupa string.',
            'nama_perusahaan.max' => 'Nama perusahaan maksimal 50 karakter.',
            'nama_perusahaan.min' => 'Nama perusahaan minimal 3 karakter.',
            'email_perusahaan.required' => 'Email perusahaan harus diisi.',
            'email_perusahaan.email' => 'Email perusahaan tidak valid.',
            'email_perusahaan.min' => 'Email perusahaan minimal 6 karakter.',
            'no_telp_perusahaan.required' => 'Nomor telepon perusahaan harus diisi.',
            'no_telp_perusahaan.string' => 'Nomor telepon perusahaan harus berupa string.',
            'no_telp_perusahaan.max' => 'Nomor telepon perusahaan maksimal 13 karakter.',
            'npwp_perusahaan.required' => 'NPWP perusahaan harus diisi.',
            'npwp_perusahaan.size' => 'NPWP perusahaan harus 20 karakter.',
            'username.required' => 'Username harus diisi.',
            'username.min' => 'Username minimal 6 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, dan garis bawah.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung setidaknya satu huruf kecil, satu huruf besar, satu angka, dan satu karakter khusus.',
            'alamat.required' => 'Alamat harus diisi.',
            'alamat.string' => 'Alamat harus berupa string.',
            'alamat.min' => 'Alamat minimal 10 karakter.',
            'provinsi_id.required' => 'Provinsi ID harus diisi.',
            'kota_id.required' => 'Kota ID harus diisi.',
            'kecamatan_id.required' => 'Kecamatan ID harus diisi.',
            'kelurahan_id.required' => 'Kelurahan ID harus diisi.',
        ]);
    }

    public function register(Request $request)
    {
        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return $this->generateResponse('error', $validator->errors()->first(), null, 422);
        }

        $customer = new Customer();
        $customer->nama_perusahaan = $request->nama_perusahaan;
        $customer->email_perusahaan = $request->email_perusahaan;
        $customer->no_telp_perusahaan = $request->no_telp_perusahaan;
        $customer->npwp_perusahaan = $request->npwp_perusahaan;
        $customer->username = $request->username;
        $customer->password = bcrypt($request->password);
        $customer->alamat = $request->alamat;
        $customer->provinsi_id = $request->provinsi_id;
        $customer->kota_id = $request->kota_id;
        $customer->kecamatan_id = $request->kecamatan_id;
        $customer->kelurahan_id = $request->kelurahan_id;

        if ($customer->save()) {
            return $this->generateResponse('success', 'Register success', $customer, 201);
        } else {
            return $this->generateResponse('error', 'Register failed', null, 400);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirmed_password' => 'required|same:new_password',
        ], [
            'old_password.required' => 'Old password is required',
            'new_password.required' => 'New password is required',
            'confirmed_password.required' => 'Confirmed password is required',
            'confirmed_password.same' => 'Confirmed password must be the same as new password',
        ]);

        if ($validator->fails()) {
            return $this->generateResponse('error', $validator->errors()->first(), null, 400);
        }

        $customer = JWTAuth::parseToken()->authenticate();

        if (!$customer) {
            return $this->generateResponse('error', 'Customer not found', null, 404);
        }

        if (!Hash::check($request->old_password, $customer->password)) {
            return $this->generateResponse('error', 'Invalid old password', null, 400);
        }

        $customer->password = bcrypt($request->new_password);

        if ($customer->save()) {
            return $this->generateResponse('success', 'Change password success', null, 200);
        } else {
            return $this->generateResponse('error', 'Change password failed', null, 400);
        }
    }
}
