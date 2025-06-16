<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
            ],
            'token' => $request->bearerToken(),
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $result = $this->apiService->login(
            $request->email,
            $request->password
        );

        if ($result['success']) {
            session([
                'user' => $result['data']['user'],
                'api_token' => $result['token'],
                'authenticated' => true,
            ]);

            return redirect()->intended('/dashboard')
                ->with('success', 'Login realizado com sucesso!');
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->with('error', $result['message'] ?? 'Erro ao autenticar');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = $request->user();
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Senha atualizada com sucesso']);
    }

}
