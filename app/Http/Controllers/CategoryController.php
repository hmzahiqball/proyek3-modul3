<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $categories = Category::withCount('activities')->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'unique:categories,slug'],
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Remove the specified category from storage.
     *
     * Eksperimen 1 - Langkah 6:
     * Penanganan penghapusan kategori yang masih digunakan oleh Activity.
     * Foreign key constraint (restrictOnDelete) akan mencegah penghapusan di level database.
     * Kita tangkap QueryException dan berikan pesan yang lebih jelas ke user.
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            $category->delete();
        } catch (QueryException $e) {
            // SQLSTATE[23000]: Integrity constraint violation
            // Kegagalan terjadi di level DATABASE karena restrictOnDelete().
            // MySQL menolak penghapusan karena masih ada baris di tabel activities
            // yang mereferensikan category_id ini.
            return redirect()->route('categories.index')
                ->with('error', "Kategori \"{$category->name}\" tidak dapat dihapus karena masih digunakan oleh {$category->activities()->count()} kegiatan.");
        }

        return redirect()->route('categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil dihapus!");
    }
}
