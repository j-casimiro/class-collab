<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoteController extends Controller
{
  public function index(): View
  {
    $notes = Note::with('user')->latest()->paginate(12);

    return view('notes.index', compact('notes'));
  }

  public function create(): View
  {
    return view('notes.create');
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'title'       => 'required|string|max:255',
      'subject'     => 'required|string|max:255',
      'description' => 'nullable|string|max:2000',
      'content'     => 'required|string',
    ]);

    $request->user()->notes()->create($validated);

    return redirect()->route('notes.index')
      ->with('success', 'Note created successfully.');
  }

  public function show(Note $note): View
  {
    $this->authorize('view', $note);

    return view('notes.show', compact('note'));
  }

  public function edit(Note $note): View
  {
    $this->authorize('update', $note);

    return view('notes.edit', compact('note'));
  }

  public function update(Request $request, Note $note): RedirectResponse
  {
    $this->authorize('update', $note);

    $validated = $request->validate([
      'title'       => 'required|string|max:255',
      'subject'     => 'required|string|max:255',
      'description' => 'nullable|string|max:2000',
      'content'     => 'required|string',
    ]);

    $note->update($validated);

    return redirect()->route('notes.show', $note)
      ->with('success', 'Note updated successfully.');
  }

  public function destroy(Note $note): RedirectResponse
  {
    $this->authorize('delete', $note);

    $note->delete();

    return redirect()->route('notes.index')
      ->with('success', 'Note deleted.');
  }
}
