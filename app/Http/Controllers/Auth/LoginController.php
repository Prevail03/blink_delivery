<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;

class LoginController extends Controller{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'operation' => 'failure',
                'message' => 'Missing data/invalid data. Please try again.',
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $userExists = DB::connection('mysql')->table('sysusers')->where('userEmail', $email)->where('userStatus', 1)->first();

        if ($userExists && Hash::check($password, $userExists->userPassword)) {
            $userID = $userExists->userID;
            $fullName = $userExists->userFullNames;
            $userEmail = $userExists->userEmail;
            $userPhoneNumber = $userExists->userPhoneNumber;
            $userType = $userExists->userType;
            $userBikeRegistrationNumber = $userExists->userBikeRegistrationNumber;

            $data = [
                'userID' => $userID,
                'fullName' => $fullName,
                'email' => $userEmail,
                'phoneNumber' => $userPhoneNumber,
                'userType' => $userType
            ];

            $key = (string) env('JWT_KEY');
            // Generate the JWT token
            $jwt = JWT::encode($data, $key, 'HS256');
            // Pad the token to the desired length
            $tokenLength = 1000;
            $paddingLength = $tokenLength - strlen($jwt);
            if ($paddingLength > 0) {
                $padding = Str::random($paddingLength);
                $jwt .= $padding;
            } elseif ($paddingLength < 0) {
                // If the JWT length exceeds the desired length, you can truncate the token
                $jwt = substr($jwt, 0, $tokenLength);
            }
            return response()->json([
                'status' => 200,
                'operation' => 'success',
                'message' => 'Login successful',
                'user' => $data,
                'token' => $jwt
            ], 200);
        } else {
            return response()->json([
                'status' => 401,
                'operation' => 'failure',
                'message' => 'Invalid credentials. Please try again.',
            ], 401);
        }
    }
}
