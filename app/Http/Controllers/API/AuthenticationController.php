<?php

namespace App\Http\Controllers\API;

use App\Exceptions\AuthenticationServiceException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthenticationService;
use App\Traits\ErrorResponseTrait;

class AuthenticationController extends Controller
{
    use ErrorResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'login']]);
    }

    public function login(Request $request, AuthenticationService $authenticationService)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        try {
            $token = $authenticationService->login($request->all());
        } catch (AuthenticationServiceException $e) {
            return $this->errorResponse($e);
        }

        return response()->json(["token" => $token], 201);
    } //end method login

    public function register(Request $request, AuthenticationService $authenticationService)
    {
        // To add a role to a user add the field
        // "_role" to the request
        $request->validate([
            "_role" => "string", // to attach user to a role
            "email" => "required|email",
            "password" => "required|string",
            "name" => "required|string",
            "title" => "required|string",
            "phone_number" => "required|string",
            "address" => "required|string",
            "address_2" => "string",
            "city" => "required|string",
            "state" => "required|string",
            "country" => "required|string",
            "zip_code" => "required|string",
            "about" => "required|string",
            "profile_photo" => "required|image|max:1024",
            "landline" => "string|nullable",
            "facebook" => "string|nullable",
            "twitter" => "string|nullable",
            "linkedin" => "string|nullable",
            "google_plus" => "string|nullable",
            "instagram" => "string|nullable",
            "tumbler" => "string|nullable",
        ]);

        try {
            $user = $authenticationService->registerUser($request->all())->getUser();
        } catch (AuthenticationServiceException $e) {
            return $this->errorResponse($e);
        }

        return response()->json(["message" => "Registration sucessful!", "id" => $user->id], 201);
    } //end method this is the register method

    public function logout(AuthenticationService $authenticationService)
    {

        try {
            $authenticationService->logout();
        } catch (AuthenticationServiceException $e) {
            return $this->errorResponse($e);
        }

        return response()->json(["message" => "Logout sucessful!"]);
    } //end method logout

    

    public function changePassword(Request $request, AuthenticationService $authenticationService)
    {
        $request->validate([
            "password" => "required",
            "new_password" => "required|confirmed", // new_password_confirmation
        ]);

        try {
            $authenticationService->changePassword();
        } catch (AuthenticationServiceException $e) {
            return $this->errorResponse($e);
        }

        return response()->json(["message" => "Password changed sucessful!"]);
    } //end method changePassword



    public function me(AuthenticationService $authenticationService)
    {
        try {
            $user = $authenticationService->me();
        } catch (AuthenticationServiceException $e) {
            return $this->errorResponse($e);
        }

        return response()->json(["user" => $user]);
    } //end method me
}//end class AuthenticationController
