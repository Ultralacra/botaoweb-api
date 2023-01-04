<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use Validator;
use Illuminate\Support\Facades\Hash;
use Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required|string|min:2|max:100',
            'email'=>'required|string|email|max:100|unique:users',
            'password'=>'required|string|min:6|confirmed'
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);

        return response()->json([
            'msg'=>'User Inserted SuccessFully',
            'user'=>$user
        ]);

    }
    //login api method call
    public function login(request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors());
        }

        if(!$token = auth()->attempt($validator->validated()))
        {   
            return response()->json(['success'=>false,'msg'=>'Username & Password is incorrect']);
        }

        return $this->respondWithToken($token);
        
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    //logout api method
    public function logout()
    {
        try{
            auth()->logout();
            return response()->json(['success'=>true,'msg'=>'User logged out!']);
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        }
    }

    //profile method
    public function profile(){
        try{

            return response()->json(['success'=>true,'data'=>auth()->user()]);
        
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        }
    }
    
    //update profile
    public function updateProfile(Request $request)
    {
        if(auth()->user())
        {
            $validator = Validator::make($request->all(),[
                'id'=>'required',
                'name'=>'required|string',
                'email'=>'required|email|string'
            ]);

            if($validator->fails())
            {
                return response()->json($validator->errors());    
            }
            $user = User::find($request->id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
            return response()->json(['success'=>true,'msg'=>'User Date','data'=>$user]);
        }
        else{
            return response()->json(['success'=>false,'msg'=>'User is not Authenticated']);
        }
    }

    public function sendVerifyMail($email)
    {
        if(auth()->user()){
            $user = User::where('email',$email)->get();
            if(count($user) > 0){
                $random = Str::random(40);
                $domain = URL::to('/');
                $url = $domain.'/verify-mail/'.$random;
                
                $data['url'] = $url;
                $data['email'] = $email;
                $data['title'] = "Email Verification";
                $data['body'] = "Please click here to below to verify your email.";
                
                Mail::send('verifyMail',['data'=>$data],function($message) use ($data){
                    $message->to($data['email'])->subject($data['title']);
                });

                $user = User::find($user[0]['id']);
                $user->remember_token = $random;
                $user->save();

                return response()->json(['success'=>true,'msg'=>'Mail sen successfully.']);

            }
            else{
                return response()->json(['success'=>false,'msg'=>'User is not found!.']);    
            }
        }
        else{
            return response()->json(['success'=>false,'msg'=>'User is not Authenticated.']);
        }
    }

    public function verificationMail($token)
    {
        $user = User::where('remember_token',$token)->get();
        if(count($user) > 0){
            $datetime = Carbon::now()->format('Y-m-d H:i:s');
            $user = User::find($user[0]['id']);
            $user->remember_token = '';
            $user->is_verified = 1;
            $user->email_verified_at = $datetime;
            $user->save();

            return "<h1>Email verified successfully.";
        }
        else{
            return view('404');
        }
    }

    //refresh token
    public function refreshToken(){
        if(auth()->user()){
            return $this->respondWithToken(auth()->refresh());
        }
        else{
            return response()->json(['success'=>false,'msg'=>'User is not Authenticated.']);
        }
    }

    //forget password api method
    public function forgetPassword(Request $request)
    {
        try{
            $user = User::where('email',$request->email)->get();
            if(count($user) > 0){
                $token = Str::random(40);
                $domain = URL::to('/');
                $url = $domain.'/reset-password?token='.$token;

                $data['url'] = $url;
                $data['email'] = $request->email;
                $data['title'] = "Password Reset";
                $data['body'] = "Please click on below link to reset your password.";

                Mail::send('forgetPasswordMail',['data'=>$data],function($message) use ($data){
                    $message->to($data['email'])->subject($data['title']);
                });
                $datetime = Carbon::now()->format('Y-m-d H:i:s');
                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => $datetime
                    ]
                );

                return response()->json(['success'=>true, 'msg'=>'Please check your mail to reset your password.']);

            }
            else{
                return response()->json(['success'=>false, 'msg'=>'User not found!']);
            }
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        }
    }

    //reset password view load
    public function resetPasswordLoad(Request $request){
        $resetData = PasswordReset::where('token',$request->token)->get();
        if(isset($request->token) && count($resetData) > 0){
            $user = User::where('email',$resetData[0]['email'])->get();
            return view('resetPassword',compact('user'));
        }
        else{
            return view('404');
        }
    }

    //Password reset functionality
    public function resetPassword(Request $request){
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::find($request->id);
        $user->password = Hash::make($request->password);
        $user->save();

        PasswordReset::where('email',$user->email)->delete();

        return "<h1>Your password has been reset successfully.</h1>";
    }
}    