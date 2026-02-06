<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Vocabulary;
use Illuminate\Http\Request;
use App\Models\LearningLog;

class VocabularyController extends Controller
{
    public function index()
    {
        $vocabularies = Vocabulary::where('user_id', Auth::id())->get();
        return view('vocab.index', compact('vocabularies'));
    }

    public function create()
    {
        return view('vocab.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'korean' => 'required|string|max:255',
            'meaning' => 'required|string|max:255',
            'example' => 'nullable|string',
        ]);

        Vocabulary::create([
            'user_id' => Auth::id(),
            'korean' => $request->korean,
            'meaning' => $request->meaning,
            'example' => $request->example,
        ]);

        return redirect('/vocab');
    }
    public function learn(Vocabulary $vocab)
    {
        // logic học...

        LearningLog::create([
            'user_id' => auth()->id(),
            'vocabulary_id' => $vocab->id,
            'action' => 'learn',
            'reviewed_at' => now(),
        ]);
    }
}
