<?php

namespace App\Http\Controllers;

use App\Models\ResetPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{

    public function sendOTP(Request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email',
        ]);

        if($validator->fails()){
            return $this->api_response(false,'Vlaidator Error',['error' => $validator->errors()],422);
        }

        $otp_code = rand(1000,9999);

        DB::table('password_reset_otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp_code,
                'is_used' => false,
                'expires_at' => Carbon::now()->addMinutes(2),
                'updated_at' => now(),
            ]);

            // Mail::raw("Your password reset OTP is: $otp_code", function ($message) use ($request) {
            //     $message->to($request->email)->subject('Password Reset OTP');
            // });

            return $this->api_response(true, 'OTP send successfully',['OTP code' => $otp_code]);
    }

    public function verifyOtp(Request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:password_reset_otps,email',
            'otp' => 'required|digits:4'
        ]);

        if($validator->fails()){
            return $this->api_response(false,'Validator Error',['error' => $validator->errors()],422);
        }

        $otpRecord = DB::table('password_reset_otps')
        ->where('email',$request->email)
        ->where('otp',$request->otp)
        ->where('is_used',false)
        ->where('expires_at','>',now())
        ->first();

        if(!$otpRecord){
            return $this->api_response(false,'Invalid or expired OTP.',[],400);
        }

        return $this->api_response(true,'Correct and valid OTP code',$otpRecord);
    }

    public function resetPassword(Request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:password_reset_otps,email',
            'password' => [
                'required',
                'confirmed',
                'min:6',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/',]
            ]);

            if($validator->fails()){
                return $this->api_response(false,'Validation Error',['error' => $validator->errors()],422);
            }

            $user = User::where('email',$request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            DB::table('password_reset_otps')
            ->where('email',$user->email)
            ->update(['is_used' => true]);

            return $this->api_response(true,'Password Reset Success',['New Password' => $request->password]);
        }

    }

