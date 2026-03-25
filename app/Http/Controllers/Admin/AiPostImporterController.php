<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Blog\AiPostImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AiPostImporterController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/PostImporter/Index');
    }

    public function store(Request $request, AiPostImportService $importer): RedirectResponse
    {
        $validated = $request->validate([
            'package' => ['required', 'string', 'max:100000'],
        ]);

        $post = $importer->import($validated['package'], $request->user());

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Post imported as draft.');
    }
}
