<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidatePrescriptionAccess
{
    public function handle(Request $request, Closure $next)
    {
        $prescription = $request->route('prescription');
        $user = $request->user();

        if ($user->role === 'doctor' && $prescription->examination->doctor_id !== $user->id) {
            abort(403, 'You do not have access to this prescription.');
        }

        return $next($request);
    }
}
