<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveJobOrderRequest;
use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminJobOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin-job-orders', [
            'jobOrders' => JobOrder::with('assignees')->latest('id')->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin-job-order-form', [
            'jobOrder' => null,
            'users' => User::where('is_admin', false)->orderBy('name')->orderBy('id')->get(['id', 'name', 'email', 'position']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveJobOrderRequest $request): RedirectResponse
    {
        $jobOrder = DB::transaction(function () use ($request): JobOrder {
            $jobOrder = new JobOrder($request->safe()->only(['title', 'description', 'due_date']));
            $jobOrder->creator()->associate($request->user());
            $jobOrder->save();
            $jobOrder->assignees()->sync($request->validated('assignees'));

            return $jobOrder;
        });

        return redirect()->route('admin.job-orders.index')->with('jobOrderStatus', $jobOrder->reference().' created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobOrder $jobOrder): View
    {
        return view('admin-job-order-form', [
            'jobOrder' => $jobOrder->load('assignees'),
            'users' => User::where('is_admin', false)->orderBy('name')->orderBy('id')->get(['id', 'name', 'email', 'position']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveJobOrderRequest $request, JobOrder $jobOrder): RedirectResponse
    {
        DB::transaction(function () use ($request, $jobOrder): void {
            $jobOrder->fill($request->safe()->only(['title', 'description', 'due_date']))->save();
            $jobOrder->assignees()->sync($request->validated('assignees'));
        });

        return redirect()->route('admin.job-orders.index')->with('jobOrderStatus', $jobOrder->reference().' updated successfully.');
    }
}
