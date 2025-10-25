<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Booking;

class CheckExpiredBookings
{
    public function handle(Request $request, Closure $next)
    {
        Booking::where('status', 'BOOKED')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'CANCELLED']);

        return $next($request);
    }
}
