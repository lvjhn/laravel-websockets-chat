<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\User;
use Auth;

use App\Events\NewMessage;

class MessageController extends Controller
{
    public function index() {
        if(Auth::check()) {
            $messages = Message::with(["user"])->get();
            return $messages;
        }
        return "Unauthorized";
    }

    public function send(Request $request) {
        if(Auth::check()) {
            $user_id = Auth::id(); 
            $message = new Message();
            $message->user_id = $user_id;
            $message->message = $request->input('message');
            $message->save();
            $message = $message->load('user');
            broadcast(new NewMessage($message->load('user')))->toOthers();
            return $message;
        }
    }
}
