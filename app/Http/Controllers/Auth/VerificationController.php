<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user)
    {
        // si la url es validado
        if(! URL::hasValidSignature($request)) {
            return response()->json(["errors" => [
                "message" => "Link de Verificación invalido"
            ]], 422);
        }

        if($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" => "El Correo ya esta Verificado"
            ]], 422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(["success" =>["message" => "Correo Verificado Correctamente"]]);

    }

    public function resend(Request $request, User $user)
    {
        $this->validate($request, [
            'email' => ['email', 'required'],
        ]);
        $user = User::where('email', $request->email)->first();

        if(! $user) {
            return response()->json(["errors" => [
                "email" => "El Usuario no se encuentra registrado"
            ]]);
        }

        if($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" => "El Correo ya esta Verificado"
            ]], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => 'Verificación reenviada']);

    }

}
