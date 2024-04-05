<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Extension\SmartPunct\ReplaceUnpairedQuotesListener;

class ContactController extends Controller
{
    public function message(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make([
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string'
        ], [
            'email.required' => 'L\'email è obbligatoria',
            'email.email' => 'L\'email non è valida',
            'subject.required' => 'L\'oggetto è obbligatorio',
            'message.required' => 'Il messaggio è obbligatorio'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }



        $mail = new ContactMessageMail(
            subject: $data['subject'],
            sender: $data['email'],
            content: $data['message']
        );

        Mail::to(env('MAIL_TO_ADDRESS'))->send($mail);

        return response(null, 204);
    }
}
