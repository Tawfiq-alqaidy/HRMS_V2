<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Requests\UpdateJobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use App\Models\JobPosting;

class JobApplicationController
{

    public function index()
    {
        $applications = JobApplication::with('jobPosting')->get();
        if ($applications->isEmpty()) {
            return response()->json(['message' => 'No job applications found'], 404);
        }
        return JobApplicationResource::collection($applications);
    }


    public function store(StoreJobApplicationRequest $request)
    {
        $validated = $request->validated();

        $jobPosting = JobPosting::find($validated['job_posting_id']);
        if (!$jobPosting) {
            return response()->json(['message' => 'Job posting not found'], 404);
        }

        $validated['status'] = 'pending';
        $application = JobApplication::create($validated);

        return response()->json(['message' => 'Job application created successfully.',], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $application = JobApplication::with('jobPosting')->find($id);
        if (!$application) {
            return response()->json(['message' => 'Job application not found'], 404);
        }
        return new JobApplicationResource($application);
    }


    public function update(UpdateJobApplicationRequest $request, string $id)
    {
        $application = JobApplication::find($id);
        if (!$application) {
            return response()->json(['message' => 'Job application not found'], 404);
        }

        $validated = $request->validated();
        $application->update($validated);

        return response()->json(['message' => 'Job application updated successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $application = JobApplication::find($id);
        if (!$application) {
            return response()->json(['message' => 'Job application not found'], 404);
        }

        $application->delete();
        return response()->json(['message' => 'Job application deleted successfully.'], 200);
    }
}
