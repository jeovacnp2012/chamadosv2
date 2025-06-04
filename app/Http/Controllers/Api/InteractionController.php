<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Called;
use App\Models\Interaction;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function store(Request $request, Called $called)
    {
        $user = $request->user();

        if (! $user->hasAnyRole(['Executor', 'Solicitante'])) {
            return response()->json(['error' => 'Não autorizado.'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $interaction = new Interaction([
            'user_id' => $user->id,
            'message' => $validated['message'],
        ]);

        $called->interactions()->save($interaction);

        return response()->json(['success' => true, 'interaction' => $interaction], 201);
    }
}
