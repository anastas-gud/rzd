<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Services\BookingService;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected ProfileService $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = Auth::user();
        $profile = $this->service->getProfile($user->id);
        $tickets = $this->service->getUserTickets($user->id);
        $bookings = (new BookingService())->getUserBookings();

        return view('profile.index', compact('user', 'profile', 'tickets', 'bookings'));
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $this->service->getProfile($user->id);
        return view('profile.edit', compact('profile'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $this->service->updateProfile($user->id, $request->validated());
        return redirect()->route('profile')->with('success', 'Профиль успешно обновлён.');
    }

    public function cancelTicket($ticketId)
    {
        $user = Auth::user();
        $success = $this->service->cancelTicket($user->id, $ticketId);

        if ($success) {
            return back()->with('success', 'Билет успешно отменён и возвращён.');
        }

        return back()->withErrors('Не удалось отменить билет.');
    }
}

