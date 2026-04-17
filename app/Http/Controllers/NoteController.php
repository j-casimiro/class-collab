<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
      'file'        => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:20480',
    ]);

    $file = $request->file('file');
    $path = $file->store('notes', 'private');

    $request->user()->notes()->create([
      'title'       => $validated['title'],
      'subject'     => $validated['subject'],
      'description' => $validated['description'] ?? null,
      'file_path'   => $path,
      'file_name'   => $file->getClientOriginalName(),
    ]);

    return redirect()->route('notes.index')
      ->with('success', 'Note uploaded successfully.');
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
      'file'        => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:20480',
    ]);

    $data = [
      'title'       => $validated['title'],
      'subject'     => $validated['subject'],
      'description' => $validated['description'] ?? null,
    ];

    if ($request->hasFile('file')) {
      Storage::disk('private')->delete($note->file_path);

      $file = $request->file('file');
      $data['file_path'] = $file->store('notes', 'private');
      $data['file_name'] = $file->getClientOriginalName();
    }

    $note->update($data);

    return redirect()->route('notes.show', $note)
      ->with('success', 'Note updated successfully.');
  }

  public function destroy(Note $note): RedirectResponse
  {
    $this->authorize('delete', $note);

    Storage::disk('private')->delete($note->file_path);
    $note->delete();

    return redirect()->route('notes.index')
      ->with('success', 'Note deleted.');
  }

  public function download(Note $note)
  {
    $this->authorize('download', $note);

    return Storage::disk('private')->download($note->file_path, $note->file_name);
  }
}
