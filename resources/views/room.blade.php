@extends('layouts.app') 

@section('title', 'Public Chatroom')

@section('content')
<div class="container">
    <div class="row">
        <public-chat-room 
            chatroom_id="{{ Request::route('id') }}"
            user='<?= json_encode(auth()->user()) ?>' />
    </div>
</div>
@endsection