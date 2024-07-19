<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticationController extends Controller
{

    /**
    * Handles user login.
    *
    * This function validates the incoming request for 'email' and 'password'.
    * If validation fails, it returns a JSON response with a 422 status code and a validation error message.
    * If validation passes, it attempts to authenticate the user with the provided credentials.
    * If authentication fails, it returns a JSON response with a 401 status code and an error message.
    * If authentication is successful, it retrieves the user record, generates an authentication token,
    * and returns a JSON response with a 200 status code, a success message, and the authentication token.
    *
    * @param Request $request The incoming HTTP request.
    * @return JsonResponse The JSON response to the client.
    */
    public function login(Request $request) : JsonResponse {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);


        if ($validate->fails()) {
            return response()->json([
                'metadata' => [
                    'status' => 422,
                    'message' => 'Validation error'
                ],
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'metadata' => [
                    'status' => 401,
                    'message' => 'Credentials do not match any record'
                ],
            ], 401);
        }

        $user = User::where('email', $credentials['email'])->first();
        $token = $user->createToken('authToken')->plainTextToken;


        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'Login successful'
            ],
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
    * Logout a user.
    *
    * This function is responsible for logging out a user. It does this by deleting the user's personal access token.
    * The function first checks if a bearer token is provided in the request. If not, it returns a 401 Unauthorized response.
    * Next, it tries to find a personal access token that matches the provided bearer token. If it can't find one, it returns a 401 Unauthorized response.
    * Then, it checks if the token belongs to the currently authenticated user. If not, it returns a 401 Unauthorized response.
    * Finally, if all checks pass, it deletes the token and returns a 200 OK response with a message indicating that the logout was successful.
    *
    * @param Request $request The incoming HTTP request.
    * @return JsonResponse The outgoing HTTP response.
    */
    public function logout(Request $request) : JsonResponse {
        // Get the bearer token from the request
        $tokenString = $request->bearerToken();

        // If no token is provided, return a 401 Unauthorized response
        if ($tokenString === null) {
            return response()->json([
                'metadata' => [
                    'status' => 401,
                    'message' => 'Unauthorized'
                ],
            ], 401);
        }

        // Try to find a personal access token that matches the provided bearer token
        $token = PersonalAccessToken::findToken($tokenString);

        // If no matching token is found, return a 401 Unauthorized response
        if (!$token) {
            return response()->json([
                'metadata' => [
                    'status' => 401,
                    'message' => 'Unauthorized'
                ],
            ], 401);
        }

        // If the token does not belong to the currently authenticated user, return a 401 Unauthorized response
        if ($token->tokenable_id !== auth()->id()) {
            return response()->json([
                'metadata' => [
                    'status' => 401,
                    'message' => 'Unauthorized'
                ],
            ], 401);
        }

        // If all checks pass, delete the token
        $token->delete();

        // Return a 200 OK response with a message indicating that the logout was successful
        return response()->json([
            'metadata' => [
                'status' => 200,
                'message' => 'Logout successful'
            ],
        ]);
    }
}
