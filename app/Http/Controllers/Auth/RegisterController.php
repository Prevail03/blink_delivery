<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class RegisterController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'userFullNames' => 'required|string|max:255',
            'userEmail' => 'required|email|unique:sysusers',
            'userPhoneNumber' => 'required|string|max:13|unique:sysusers',
            'userPassword' => 'required|string|min:8',
            'userType' => 'required|string|max:20',
            'userBikeRegistrationNumber' => 'nullable|unique:sysusers',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'operation' => 'failure',
                'message' => 'Missing data/invalid data. Please try again.',
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $user = [
            'userFullNames' => $request->input('userFullNames'),
            'userEmail' => $request->input('userEmail'),
            'userPhoneNumber' => $request->input('userPhoneNumber'),
            'userBikeRegistrationNumber' => $request->input('userBikeRegistrationNumber'),
            'userPassword' => Hash::make($request->input('userPassword')),
            'userType' => $request->input('userType'),
        ];

        $inserted = DB::connection('mysql')->table('sysusers')->insert($user);

        if (!$inserted) {
            return response()->json([
                'status' => 500,
                'operation' => 'failure',
                'message' => 'Failed to register user. Please try again later.'
            ], 500);
        }

        return response()->json([
            'status' => 200,
            'operation' => 'success',
            'message' => 'User registered successfully'
        ], 200);
    }

    // upload national ID and license
    public function uploadDocuments(Request $request){
        $validator = Validator::make($request->all(), [
            'userID' => 'required|integer',
            'nationalID' => 'required|file|mimes:pdf',
            'drivingLicense' => 'file|mimes:pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'operation' => 'failure',
                'message' => 'Missing data/invalid data. Please try again.',
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $userID = $request->input('userID');
        $nationalID = $request->file('nationalID');
        $drivingLicense = $request->file('drivingLicense');

        // Upload national ID file to "NationalID" folder
        $nationalIDPath = $nationalID->store('NationalID', 'public');

        $drivingLicensePath = null;
        if ($drivingLicense) {
            // Upload driving license file to "Licenses" folder
            $drivingLicensePath = $drivingLicense->store('Licenses', 'public');
        }

        // Update sysusers table with the file paths
        DB::connection('mysql')->table('sysusers')
            ->where('userID', $userID)
            ->update([
                'userNationalIDPath' => $nationalIDPath,
                'userDrivingLicensePath' => $drivingLicensePath
            ]);

        return response()->json([
            'status' => 200,
            'operation' => 'success',
            'message' => 'Files uploaded and sysusers table updated successfully'
        ], 200);
    }

}
