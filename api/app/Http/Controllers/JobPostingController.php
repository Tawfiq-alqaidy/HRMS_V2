<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Http\Resources\JobPostingResource;
use App\Http\Requests\StoreJobPostingRequest;
use App\Http\Requests\UpdateJobPostingRequest;

class JobPostingController
{

    public function index()
    {
        $today = now()->toDateString();
        $jobPostings = JobPosting::where('isActive', 1)
            ->whereDate('application_deadline', '>=', $today)
            ->get();
        if ($jobPostings->isEmpty()) {
            return response()->json(['message' => 'No job postings found.'], 404);
        }
        return JobPostingResource::collection($jobPostings);
    }

    // theres a pug here
    public function store(StoreJobPostingRequest $request)
    {
        $validated = $request->validated();
        $jobPosting = JobPosting::create($validated);
        return response()->json([
            'message' => 'Job posting created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jobPosting = JobPosting::findOrFail($id);
        if (!$jobPosting) {
            return response()->json(['message' => 'Job posting not found.'], 404);
        }
        return new JobPostingResource($jobPosting);
    }


    public function update(UpdateJobPostingRequest $request, string $id)
    {
        $jobPosting = JobPosting::findOrFail($id);
        if (!$jobPosting) {
            return response()->json(['message' => 'Job posting not found.'], 404);
        }
        $validated = $request->validated();
        $jobPosting->update($validated);
        return response()->json([
            'message' => 'Job posting updated successfully.',
        ], 200);
    }


    public function destroy(string $id)
    {
        $jobPosting = JobPosting::findOrFail($id);
        if (!$jobPosting) {
            return response()->json(['message' => 'Job posting not found.'], 404);
        }
        $jobPosting->delete();
        return response()->json([
            'message' => 'Job posting deleted successfully.',
        ], 200);
    }
}
