<?php

namespace App\Rest\Controllers;

use App\Models\Contact;
use App\Mail\ContactSubmitted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Rest\Controller as RestController;
class ContactController extends RestController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'issue' => 'required|string|max:255',
            'category' => 'required|string|in:general,technical,billing,support',
            'description' => 'required|string',
            'email' => 'nullable|email|max:255',
        ]);

        $contact = Contact::create($validated);

        // Send email to support
        Mail::to(env('SUPPORT_EMAIL'))->send(new ContactSubmitted($contact));

        return response()->json([
            'message' => 'Contact submitted successfully and email sent!',
            'data' => $contact,
        ], 201);
    }
}
