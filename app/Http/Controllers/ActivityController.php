<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;
use App\Models\Activity;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $activities = Activity::query()
            ->orderBy('activity_date')
            ->get();

        return view('activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('activities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $activity = Activity::create($request->validated());

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity): View
    {
        return view('activities.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity): View
    {
        return view('activities.edit', compact('activity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $activity->update($request->validated());

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Kegiatan berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Kegiatan berhasil dihapus!');
    }
}
