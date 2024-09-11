<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function showContactForm()
    {
        return view('contact');
    }

    public function sendContactEmail(Request $request)
    {
        $details = [
            'num' => $request->num
        ];

        Mail::to($request->emailUser."")->send(new ContactMail($details));

        return back()->with('success', 'Your message has been sent!');
    }
}
