<?php

namespace App\Http\Controllers;

use App\Models\UserJob;
use App\Traits\ApiResponser;

class UserJobController extends Controller
{
    use ApiResponser;

    public function index()
    {
        $jobs = UserJob::all();
        return $this->successResponse($jobs);
    }

    public function show($id)
    {
        $job = UserJob::findOrFail($id);
        return $this->successResponse($job);
    }
}