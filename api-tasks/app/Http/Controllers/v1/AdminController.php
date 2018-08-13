<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Role;
use App\Task;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Class AdminController
 *
 * @package App\Http\Controllers\v1
 */
class AdminController extends Controller
{
    /**
     * Get users list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers()
    {
        try {
            $users = User::all();

            return $this->returnSuccess($users);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Create an user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser(Request $request)
    {
        try {
            $rules = [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if (!$validator->passes()) {
                return $this->returnBadRequest('Please fill all required fields');
            }

            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->status = $request->has('status') ? $request->status : User::STATUS_ACTIVE;
            $user->role_id = $request->has('role') ? $request->role : Role::ROLE_USER;

            $user->save();

            return $this->returnSuccess($user);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Update an user
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if ($request->has('name')) {
                $user->name = $request->name;
            }

            if ($request->has('email')) {
                $emailUser = User::where('email', $request->email)->where('id', '!=', $id)->first();

                if ($emailUser) {
                    return $this->returnBadRequest('Email is registered with another user');
                }

                $user->email = $request->email;
            }

            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }

            if ($request->has('status')) {
                $user->status = $request->status;
            }

            if ($request->has('role')) {
                $user->role_id = $request->role;
            }

            $user->save();

            return $this->returnSuccess($user);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Delete an user
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser($id)
    {
        try {
            $user = User::find($id);

            $user->delete();

            return $this->returnSuccess();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }


    /**
     * Create a task
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTask(Request $request)
    {
        try {
            $user = $this->validateSession();

            $rules = [
                'name' => 'required',
                'description' => 'required',
                'id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if (!$validator->passes()) {
                return $this->returnBadRequest('Please fill all required fields');
            }

            $task = new Task();

            $task->name = $request->name;
            $task->description = $request->description;
            $task->status = Task::STATUS_NEW;
            $task->user_id = $user->id;


            //verifying user exists
            $idUser = User::where('id', $request->id)->first();

            if (!$idUser) {
                return $this->returnBadRequest('User does not exist');
            } else {
                $task->assign = $request->id;
            }

            $task->save();

            return $this->returnSuccess($task);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Get tasks list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTasks()
    {
        try {
            //$tasks = Task::all();
            $tasks = Task::paginate(10);

            return $this->returnSuccess($tasks);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Update a task
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTask(Request $request, $id)
    {
        try {
            $task = Task::find($id);

            if ($request->has('name')) {
                $task->name = $request->name;
            }

            if ($request->has('description')) {
                $task->description = $request->description;
            }

            if ($request->has('status')) {
                $task->status = $request->status;
            }

            if ($request->has('id')) {
                $idUser = User::where('id', $request->id)->first();

                if (!$idUser) {
                    return $this->returnBadRequest('User does not exist');
                } else {
                    $task->assign = $request->id;
                }
            }

            $task->save();

            return $this->returnSuccess($task);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Delete a task
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTask($id)
    {
        try {
            $task = Task::find($id);

            $task->delete();

            return $this->returnSuccess();
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
}