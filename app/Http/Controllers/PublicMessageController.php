<?php

namespace App\Http\Controllers;

use App\PublicMessage;
use Illuminate\Http\Request;
use Auth;

use App\Events\NewPublicMessage;

class PublicMessageController extends Controller
{
    public function get($chatroom_id) {
        $messages = PublicMessage::where('chatroom_id', $chatroom_id)
                    ->with(['user'])
                    ->get();
        return $messages;
    }

    public function send(Request $request) {
        if(Auth::check()) {
            $user_id = Auth::id(); 
            $message = new PublicMessage();
            $message->user_id = $user_id;
            $message->message = $request->input('message');
            $message->chatroom_id = $request->input('chatroom_id');
            $message->save();
            $message = $message->load('user');
            broadcast(new NewPublicMessage($message->load('user')))->toOthers();
            return $message;
        }
    }
}
