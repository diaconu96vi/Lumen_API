<?php
/**
 * Created by PhpStorm.
 * User: iongh
 * Date: 8/1/2018
 * Time: 3:37 PM
 */

namespace App\Http\Controllers\v1;


use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use GenTux\Jwt\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Login User
     *
     * @param Request $request
     * @param User $userModel
     * @param JwtToken $jwtToken
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GenTux\Jwt\Exceptions\NoTokenException
     */
    public function login(Request $request, User $userModel, JwtToken $jwtToken)
    {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required'
        ];

        $messages = [
            'email.required' => 'Email empty',
            'email.email'    => 'Email invalid',
            'password.required'    => 'Password empty'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnBadRequest();
        }

        $user = $userModel->login($request->email, $request->password);

        if ( ! $user) {
            return $this->returnNotFound('User sau parola gresite');
        }

        $token = $jwtToken->createToken($user);

        $data = [
            'user' => $user,
            'jwt'  => $token->token()
        ];

        return $this->returnSuccess($data);
    }

    public function resetPassword(Request $request, User $userModel, JwtToken $jwtToken)
    {

        $rules = [
            'email_code' => 'required',
            // email ca sa gasim useru
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ];

        $messages = [
            'email_code.required' => 'Email code required',
            'email.required' => 'Email empty',
            'email.email'    => 'Email invalid',
            'password.required'    => 'Password empty',
            'password_confirmation.required' => 'Password confirmation empty',
            'password_confirmation.same:password' => 'Passwords dont match',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $email_value="a";
        if($request->input('email_code') != $email_value){
            return $this->returnBadRequest('Email code not valid');
        }

        if ( ! $validator->passes()) {
            return $this->returnBadRequest('Inputs not good');
        }

        $user = $userModel->resetPassword($request->email, $request->password);

        if ( ! $user) {
            return $this->returnNotFound('User-ul nu exista');
        }
        $data = [
            'user' => $user,
        ];

        return $this->returnSuccess($data);
    }

    public function register(Request $request, User $userModel, JwtToken $jwtToken)
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
            'name' => 'required|unique:users,name'
        ];

        $messages = [
            'email.required' => 'Email empty',
            'email.email'    => 'Email invalid',
            'email.unique:users,email' => 'Email already taken',
            'password.required'    => 'Password empty',
            'password_confirmation.required' => 'Password confirmation empty',
            'password_confirmation.same:password' => 'Passwords dont match',
            'name.required' => 'Name empty'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnSuccess($validator->errors());
        }

        $user = $userModel->register($request->email, $request->password, $request->name);

        $token = $jwtToken->createToken($user);

        $data = [
            'user' => $user,
            'jwt'  => $token->token()
        ];

        return $this->returnSuccess($data);
    }

    public function approve(Request $request, User $userModel)
    {
        $user = $this->validateSession();
        if ($user->role_id == 0){
            return $this->returnError('Not an admin. Cant access');
        }

        $rules = [
            'email'    => 'required|email',
            'status' => 'required|integer'
        ];

        $messages = [
            'email.required' => 'Email empty',
            'email.email'    => 'Email invalid',
            'status.required' => 'Status required',
            'status.integer' => 'Status needs to be integer'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnBadRequest('Inputs not good');
        }

        $user = $userModel->approve($request->email, $request->status);

        if ( ! $user) {
            return $this->returnNotFound('User-ul nu exista');
        }
        $token = $this->jwtToken();
        $data = [
            'user' => $user,
            'jwt'  => $token->token()
        ];

        return $this->returnSuccess($data);

    }

    public function edit(Request $request, User $userModel)
    {
        $user = $this->validateSession();
        if ($user->status == 0){
            return $this->returnError('Account not approved');
        }

        $rules = [
            'email'    => 'email|unique:users,email,'.$user->id.',id',
            'password_confirmation' => 'same:password',
            'name' => 'unique:users,name, '.$user->id.',id',
        ];

        $messages = [
            'email.unique' => 'Email taken',
            'email.email'    => 'Email invalid',
            'name.unique' => 'Name already taken',
            'password_confirmation.same' => 'Passwords dont match',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnBadRequest($validator->errors());
        }

        if(empty($request->email)){
            $request->email = $user->email;
        }
        if(empty($request->name)){
            $request->name = $user->name;
        }
        /*
        if(empty($request->password)){
            $request->password = "1";

        }
        */

        $user = $userModel->edit($user->id, $request->email, $request->password, $request->name);

        $token = $this->jwtToken();
        $data = [
            'user' => $user,
            'jwt'  => $token->token()
        ];

        return $this->returnSuccess($data);

    }

    public function crud(Request $request, User $userModel)
    {
        $user = $this->validateSession();
        if ($user->role_id != 1){
            return $this->returnError('Not an admin');
        }

        $rules = [
            'user_id' => 'required|integer',
            'email'    => 'email|unique:users,email,'.$user->id.',id',
            'password_confirmation' => 'same:password',
            'name' => 'unique:users,name, '.$user->id.',id',
            'status' => 'integer',
            'role_id' => 'integer'
        ];

        $messages = [
            'user_id.integer' => 'User id must be integer',
            'user_id.required' => 'User id required',
            'email.unique' => 'Email taken',
            'email.email'    => 'Email invalid',
            'name.unique' => 'Name already taken',
            'password_confirmation.same' => 'Passwords dont match',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnBadRequest($validator->errors());
        }

        $user = $userModel->edit($request->user_id, $request->email, $request->password, $request->name, $request->status, $request->role_id);

        if ( ! $user) {
            return $this->returnNotFound('User-ul nu exista');
        }

        $token = $this->jwtToken();
        $data = [
            'user' => $user,
            'jwt'  => $token->token()
        ];

        return $this->returnSuccess($data);

    }

}