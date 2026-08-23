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

    /**
     * Enquiry from the /website-design landing page.
     *
     * Email is optional on purpose: most buyers in this market run on WhatsApp,
     * and forcing an address they rarely check loses the lead. The phone number
     * is the one contact field we insist on.
     */
    public function websiteEnquiry(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'business' => ['nullable', 'string', 'max:120'],
            'phone'    => ['required', 'string', 'max:30'],
            'email'    => ['nullable', 'email', 'max:150'],
            'website'  => ['nullable', 'string', 'max:200'],
            'package'  => ['required', 'string', 'max:40'],
            'budget'   => ['nullable', 'string', 'max:40'],
            'goal'     => ['required', 'string', 'min:10', 'max:2000'],
            // Honeypot: bots fill every field they find, people never see this.
            'company_url' => ['prohibited'],
        ]);

        $body = "New website enquiry — /website-design\n\n"
            . "Name:     {$validated['name']}\n"
            . 'Business: ' . ($validated['business'] ?: '— not given') . "\n"
            . "Phone:    {$validated['phone']}\n"
            . 'Email:    ' . ($validated['email'] ?: '— (WhatsApp preferred)') . "\n"
            . 'Website:  ' . ($validated['website'] ?: '— (none yet)') . "\n"
            . "Package:  {$validated['package']}\n"
            . 'Budget:   ' . ($validated['budget'] ?: '— not stated') . "\n\n"
            . "What they want it to do:\n{$validated['goal']}\n\n"
            . 'Submitted: ' . now()->format('d M Y H:i');

        Mail::raw($body, static function ($message) use ($validated) {
            $message
                ->to(config('fignoc.brand.email'))
                ->subject('Website enquiry: ' . ($validated['business'] ?: $validated['name']) . " — {$validated['package']}");

            if ($validated['email']) {
                $message->replyTo($validated['email'], $validated['name']);
            }
        });

        return redirect()
            ->route('landing.website')
            ->with('website_enquiry', true)
            // Which of the two forms it was, so the analytics event can say so.
            ->with('website_enquiry_form', $validated['package'] === 'Free Visibility Check'
                ? 'visibility_check'
                : 'scoping_dialog');
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
