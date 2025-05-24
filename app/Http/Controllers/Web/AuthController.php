<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login()
    {
        return view('pages.auth.login');
    }

    public function submitLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username is required',
            'password.required' => 'Password is required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $customer = Customer::where('username', $request->username)->first();
        if (!$customer) {
            return back()->withErrors(['login' => 'Username atau password salah'])->withInput();
        }

        if (!Hash::check($request->password, $customer->password)) {
            return back()->withErrors(['login' => 'Username atau password salah'])->withInput();
        }

        $token = JWTAuth::fromUser($customer);
        if (!$token) {
            return back()->withErrors(['login' => 'Unauthorized'])->withInput();
        } else {
            Session::put('jwt_token', $token);
            Session::put('username', $customer->username);
            return redirect()->route('dashboard')->with(['token' => $token, 'username' => $customer->username, 'success' => 'Login Berhasil!']);
        }
    }

    public function register()
    {
        return view('pages.auth.register');
    }

    private function validateForm(Request $request)
    {
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

    public function submitRegister(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = $this->validateForm($request);
            if ($validator->fails()) {
                DB::rollBack();
                return back()->withErrors($validator)->withInput();
            }

            $email = Customer::where('email_perusahaan', $request->email_perusahaan)->first();
            if ($email) {
                return back()->withErrors(['email_perusahaan' => 'Email perusahaan sudah terdaftar'])->withInput();
            }

            $customer = new Customer();
            $customer->nama_perusahaan = $request->nama_perusahaan;
            $customer->email_perusahaan = $request->email_perusahaan;
            $customer->no_telp_perusahaan = $request->no_telp_perusahaan;
            $customer->npwp_perusahaan = $request->npwp_perusahaan;
            $customer->username = $request->username;
            $customer->password = bcrypt($request->password);
            $customer->alamat = $request->alamat;
            $customer->provinsi_id = $request->nama_provinsi;
            $customer->kota_id = $request->nama_kota;
            $customer->kecamatan_id = $request->nama_kecamatan;
            $customer->kelurahan_id = $request->nama_kelurahan;
            $customer->otp_code = rand(100000, 999999);
            $customer->email_verified_at = now();

            if ($customer->save()) {
                DB::commit();
                return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
            } else {
                DB::rollBack();
                $token = JWTAuth::fromUser($customer);
                Session::put('jwt_token', $token);
                Session::put('username', $customer->username);

                return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat registrasi'])->withInput();
        }
    }
}
