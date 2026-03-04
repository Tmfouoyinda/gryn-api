<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function contact(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
            'subject' => 'required|string',
            'message' => 'required|string|max:2000'
        ]);

        $recipient = config('mail.contact_address');
        $data = $request->only('name', 'email', 'subject', 'message');

        Mail::to($recipient)->send(new ContactMail($data));

        return response()->json(['Message' => 'Message envoyé avec succès'], 201);
    }
}
