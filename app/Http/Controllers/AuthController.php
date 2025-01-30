<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cookie;
use DB;


// use Tymon\JWTAuth\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // return 1;
        $data = $request->only(['name', 'email', 'password']);
        $validator = Validator::make($data, [
            'name' => [
                'required',
                'string'
            ],
            'email' => [
                'required',
                'email',
                'unique:users'
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:50'
            ]
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()
                    ->toArray()
            ], Response::HTTP_BAD_REQUEST);
        }
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'user_type' => 'b'
        ]);
        $credentials = $request->only(['email', 'password']);
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'error' => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $this->respondWithToken($token);
    }

    // public function login(Request $request)
    // {
    //     return 1;
    //     $credentials = $request->only(['email', 'password']);
    //     if (!$token = auth('api')->attempt($credentials)) {
    //         return response()->json([
    //             'error' => 'Unauthorized'
    //         ], Response::HTTP_UNAUTHORIZED);
    //     }

    //     // return auth()->user();

    //     return $this->respondWithToken($token);
    // }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
    
        // Fetch user from log_info table
        $user = DB::table('log_info')
            ->where('email', $credentials['email'])
            ->first();
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }
    
        // CodeIgniter-style password check (MD5)
        $inputPasswordHash = md5($credentials['password']);

        // return $inputPasswordHash;
    
        if ($user->password != $inputPasswordHash) {
            return response()->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }
    
        // Generate JWT Token
        $token = JWTAuth::customClaims(['user_id' => $user->row_id])->fromUser($user);
    
        // Store auth token in a cookie
        $cookie = cookie('auth_token', $token, 1440, '/', '.vindotest.com', false, false, false, 'Lax');
    
        return response()->json([
            'message' => 'Login successful',
            'token' => $token
        ])->withCookie($cookie);
    }

    public function user()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function refresh()
    {
        // return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'data' => auth('api')->user(),
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }

    function Unauthenticated()
    {

        return  response()->json(['error' => 'Unauthenticated'], 401);
    }


    public function auto_login($userId)
    {
        try {
            // Retrieve auth token from cookie
            $authToken = Cookie::get('auth_token');

            // dd($authToken);

            // Verify user credentials
            $check_user = User::where('row_id', $userId)
                ->join('user_info', 'user_info.id', '=', 'log_info.user_id')
                ->where('user_info.login_token', $authToken)
                ->first();
                
               

            if (!$check_user) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            // Log the user in
            Auth::login($check_user);
            // Generate a new JWT token for the user
            $jwt_token = JWTAuth::fromUser($check_user);
             
            // $cookie = cookie('userToken', json_encode($jwt_token), 1440, null, null, false, false);
            // // Construct the full URL with the port and path
            // $url = sprintf('http://%s:8023/new-order', request()->getHost());
        // dd($jwt_token);
            // Desired domain without the port
            $domain = '.vindotest.com';
            // $cookie = cookie('userToken', json_encode($jwt_token), 1440, null, $domain, false, false, false, 'None');
            $cookie = cookie('userToken', json_encode($jwt_token), 1440, '/', $domain, false, false, false, 'Lax');
            $url = sprintf('https://%s:8024/new-order', 'decor.vindotest.com');
            // return $url;
 
            // Redirect with the generated URL and cookie
            return redirect()->to($url)->withCookie($cookie);
        } catch (\Exception $e) {
            // Handle exception and return error response
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
