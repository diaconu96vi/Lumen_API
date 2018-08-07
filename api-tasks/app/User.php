<?php

namespace App;

use GenTux\Jwt\JwtPayloadInterface;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JwtPayloadInterface
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','role_id', 'status', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
   /* protected $hidden = [
        'password',
    ];
   */

    public function getPayload()
    {
        return [
            'id' => $this->id,
            'exp' => time() + 7200,
            'context' => [
                'email' => $this->email
            ]
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Login user
     *
     * @param $userEmail
     * @param $userPassword
     *
     * @return bool
     */
    public function login($userEmail, $userPassword)
    {
        $user = $this->where([
            'email' => $userEmail,
        ])->get()->first();

        if (!$user) {
            return false;
        }
        /*
        if ($userPassword == $user->password){
            return $user;
        }
        */
        $password = $user->password;
        if (app('hash')->check($userPassword, $password)) {
            return $user;
        }

        return false;
    }

    public function resetPassword($userEmail, $userPassword){
        $user = $this->where([
            'email' => $userEmail,
        ])->get()->first();

        if (!$user) {
            return false;
        }

        $user->password = app('hash')->make($userPassword);
        $user->save();

        return $user;
    }

    public function register($userEmail, $userPassword, $userName)
    {
        $user = User::create([
            'email' => $userEmail,
            'password' => app('hash')->make($userPassword),
            'name' => $userName,
        ]);

        return $user;
    }

    public function approve($userEmail, $userStatus){
        $user = $this->where([
            'email' => $userEmail,
        ])->get()->first();

        if (!$user) {
            return false;
        }

        $user->status = $userStatus;
        $user->save();

        return $user;


    }

    public function edit($userId, $userEmail, $userPassword, $userName){
        $user = $this->where([
            'id' => $userId,
        ])->get()->first();


        $user->name = $userName;
        $user->email = $userEmail;
        if(!empty($userPassword)){
            $user->password = app('hash')->make($userPassword);
        }

        $user->save();

        return $user;
    }

    public function crud($userId, $userEmail, $userPassword, $userName, $userStatus, $userRole){
        $user = $this->where([
            'id' => $userId,
        ])->get->first();

        if (!$user) {
            return false;
        }
        if(!empty($userEmail)){
            $user->email = $userEmail;
        }
        if(!empty($userPassword)){
            $user->password = app('hash')->make($userPassword);
        }
        if(!empty($userName)){
            $user->name = $userName;
        }

        if(!empty($userStatus)){
            $user->status = $userStatus;
        }

        if(!empty($userRole)){
            $user->role_id = $userRole;
        }

        $user->save();

        return $user;
    }
}
