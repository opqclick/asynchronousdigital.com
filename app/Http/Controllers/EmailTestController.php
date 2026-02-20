<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class EmailTestController extends Controller
{
    public function send(Request $request)
    {
        $to = $request->input('to', config('mail.from.address'));
        $status = null;

        if ($request->has('to')) {
            $subject = 'Test Email from ' . config('app.name');
            $body = 'This is a test email to verify your mail configuration.';

            Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject);
            });
            $status = 'Test email sent to ' . $to;
        }

        return view('test-email', [
            'status' => $status,
        ]);
    }
}
