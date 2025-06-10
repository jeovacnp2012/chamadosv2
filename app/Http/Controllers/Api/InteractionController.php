<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Called;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('interactions', 'public');
        }
        // ⬇️ INSIRA AQUI:
        Log::info('Salvando interação', [
            'user_id' => $user->id,
            'message' => $request->input('message'),
            'attachment_path' => $path,
        ]);
        $interaction = $called->interactions()->create([
            'user_id' => $user->id,
            'message' => $request->input('message', ''),
            'attachment_path' => $path, // ✅ Nome correto
        ]);

        return response()->json(['success' => true, 'interaction' => $interaction->load('user')], 201);
    }


}
