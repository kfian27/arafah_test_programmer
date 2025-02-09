<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePrescriptionEditable
{
    public function handle(Request $request, Closure $next)
    {
        $prescription = $request->route('prescription');

        if (!$prescription->isPending()) {
            return back()->with('error', 'This prescription cannot be modified.');
        }

        return $next($request);
    }
}
