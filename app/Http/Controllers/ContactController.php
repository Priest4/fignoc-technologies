<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'service' => ['required', 'string'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        Mail::raw(
            "Name: {$validated['name']}\nEmail: {$validated['email']}\nService: {$validated['service']}\n\nMessage:\n{$validated['message']}",
            static function ($message) use ($validated) {
                $message
                    ->to(config('fignoc.brand.email'))
                    ->subject("New enquiry from {$validated['name']}")
                    ->replyTo($validated['email'], $validated['name']);
            }
        );

        return redirect()->route('contact')->with('success', true);
    }

    public function quote(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'business' => ['required', 'string', 'max:100'],
            'phone'    => ['required', 'string', 'max:20'],
            'email'    => ['required', 'email', 'max:100'],
            'service'  => ['required', 'string'],
            'message'  => ['nullable', 'string', 'max:1000'],
        ]);

        Mail::raw(
            "New Quote Request\n\n" .
            "Name: {$validated['name']}\n" .
            "Business: {$validated['business']}\n" .
            "Phone: {$validated['phone']}\n" .
            "Email: {$validated['email']}\n" .
            "Service: {$validated['service']}\n" .
            "Message: " . ($validated['message'] ?? 'None') . "\n\n" .
            "Submitted: " . now()->format('d M Y H:i') . "\n" .
            "Source: Fignoc Website Quote Form",
            static function ($msg) use ($validated) {
                $msg->to(config('fignoc.brand.email'))
                    ->subject("New Quote Request: {$validated['service']} — {$validated['business']}");
            }
        );

        return back()->with('quote_success', true);
    }
}
