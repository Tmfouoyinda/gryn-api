<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Envoie un message de contact par email.
     */
    public function send(ContactRequest $request): JsonResponse
    {
        Mail::to(config('mail.contact_address'))
            ->send(new ContactMail($request->validated()));

        return response()->json(['message' => 'Message envoyé avec succès.'], 201);
    }
}
