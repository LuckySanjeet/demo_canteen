<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\HttpResponses;


class AuthController extends Controller
{

    use HttpResponses;
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'mobile' => 'required|string|max:20|unique:users',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation errors', $validator->errors(), 422);
        }

        $profileImage = null;
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $profileImage = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('profile_images'), $profileImage);
        }

        $user = User::create([
            'username' => trim($request->username),
            'first_name' => trim(ucfirst($request->first_name)),
            'last_name' => trim(ucfirst($request->last_name)),
            'email' => trim($request->email),
            'password' => Hash::make(trim($request->password)),
            'mobile' => trim($request->mobile),
            'profile_image' => $profileImage
        ]);



        return $this->success('','User registered successfully', 200);
    }


    public function login(Request $request)
    {
        {

            try{
                if(isset($request->email)){
                    $validation = Validator::make($request->all(), [
                        'email' => 'required|email',
                        'password' => 'required'
                     ]);
                     if ($validation->fails()) {
                        $errors = $validation->errors()->all();
                        $errorMessage = implode(" ", $errors);
                        return $this->error('', $errorMessage, 200);
                    }

                    if (!auth()->attempt($request->only('email', 'password'))) {
                        return $this->error('', 'Credentials do not match', 200);
                    }
                }else{
                    $validation = Validator::make($request->all(), [
                        'username' => 'required',
                        'password' => 'required'
                     ]);
                     if ($validation->fails()) {
                        $errors = $validation->errors()->all();
                        $errorMessage = implode(" ", $errors);
                        return $this->error('', $errorMessage, 200);
                    }

                    if (!auth()->attempt($request->only('username', 'password'))) {
                        return $this->error('', 'Credentials do not match', 200);
                    }
                }


                $user = auth()->user();

                if($user->status == 1){

                $token = $user->createToken('API Token')->plainTextToken;
                $data = User::find($user->id)->toArray();
                parent::replaceNullWithEmptyString($data);
                return $this->success(['token' => $token, 'user'=> $data], 'Login successfully');
                } else{
                    return $this->error('', 'Your account has been deactivated.', 200);
                }

            }catch(\Exception $e){
                return $this->error('', $e->getMessage(), 200);
            }
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->success('','Successfully logged out',200);
    }

}
