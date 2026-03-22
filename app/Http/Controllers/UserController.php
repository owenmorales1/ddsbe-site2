<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserJob;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    use ApiResponser;

    public function index()
    {
        $users = User::all();
        return $this->successResponse($users);
    }

    public function add(Request $request)
    {
        $rules = [
            'username' => 'required|max:20',
            'password' => 'required|max:20',
            'gender'   => 'required|in:Male,Female',
            'jobid'    => 'required|numeric|min:1|not_in:0'
        ];

        $this->validate($request, $rules);

        UserJob::findOrFail($request->jobid);

        $user = User::create([
            'username' => $request->username,
            'password' => $request->password,
            'gender'   => $request->gender,
            'jobid'    => $request->jobid
        ]);

        return $this->successResponse($user, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return $this->successResponse($user);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'username' => 'sometimes|max:20',
            'password' => 'sometimes|max:20',
            'gender'   => 'sometimes|in:Male,Female',
            'jobid'    => 'sometimes|numeric|min:1|not_in:0'
        ];

        $this->validate($request, $rules);

        $user = User::findOrFail($id);

        if ($request->has('jobid')) {
            UserJob::findOrFail($request->jobid);
            $user->jobid = $request->jobid;
        }

        if ($request->has('username')) {
            $user->username = $request->username;
        }

        if ($request->has('password')) {
            $user->password = $request->password;
        }

        if ($request->has('gender')) {
            $user->gender = $request->gender;
        }

        if ($user->isClean()) {
            return $this->errorResponse(
                'At least one value must change',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user->save();

        return $this->successResponse($user);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return $this->successResponse($user);
    }
}