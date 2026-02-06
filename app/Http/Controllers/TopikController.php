<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topik;
class TopikController extends Controller
{
    public function index()
    {
        $topiks = Topik::with('topics.vocabularies')->get();

        return view('topiks.index', compact('topiks'));
    }
}