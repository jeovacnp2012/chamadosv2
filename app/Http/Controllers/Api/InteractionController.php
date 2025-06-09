<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Called;
use App\Models\Interaction;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function index(Called $called)
    {
        return response()->json([
            'data' => $called->interactions()->with('user:id,name')->latest()->get()
        ]);
    }

    public function store(Request $request, Called $called)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:10240',
        ]);

        $user = $request->user();
        $path = null;

        // Diagnóstico para testar o envio
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('interactions', 'public');
        }

        $interaction = $called->interactions()->create([
            'user_id' => $user->id,
            'message' => $request->input('message', ''),
            'file_path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'data' => $interaction,
        ]);
    }
}
