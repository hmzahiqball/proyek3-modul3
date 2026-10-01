<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Category;
use App\Services\ActivityService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $categories = Category::all();

        $activities = Activity::with('category')
            ->search($request->query('search'))
            ->filterByCategory($request->query('category_id'))
            ->filterByStatus($request->query('status'))
            ->sortByStart($request->query('sort'))
            ->paginate(10)
            ->withQueryString();

        return view('activities.index', compact('activities', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = Category::all();

        return view('activities.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request, ActivityService $service): RedirectResponse
    {
        $activity = $service->create($request->validated(), $request->file('poster'));

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity): View
    {
        $activity->load('category');

        return view('activities.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity): View
    {
        $categories = Category::all();

        return view('activities.edit', compact('activity', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity, ActivityService $service): RedirectResponse
    {
        try {
            $service->update($activity, $request->validated(), $request->file('poster'));
        } catch (DomainException $exception) {
            return back()
                ->withErrors(['status' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Kegiatan berhasil diubah!');
    }

    public function publish(Activity $activity, ActivityService $service): RedirectResponse
    {
        try {
            $service->publish($activity);
        } catch (DomainException $exception) {
            return back()->withErrors(['status' => $exception->getMessage()]);
        }

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Kegiatan berhasil dipublish!');
    }

    public function complete(Activity $activity, ActivityService $service): RedirectResponse
    {
        try {
            $service->complete($activity);
        } catch (DomainException $exception) {
            return back()->withErrors(['status' => $exception->getMessage()]);
        }

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Kegiatan berhasil diselesaikan!');
    }

    public function trash(): View
    {
        $activities = Activity::onlyTrashed()->with('category')->latest('deleted_at')->get();

        return view('activities.trash', compact('activities'));
    }

    public function restore(int $activity): RedirectResponse
    {
        $deletedActivity = Activity::onlyTrashed()->findOrFail($activity);
        $deletedActivity->restore();

        return redirect()->route('activities.index')
            ->with('success', 'Kegiatan berhasil dipulihkan.');
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
