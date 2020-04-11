@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <global-chat-room user='<?= json_encode(auth()->user()) ?>' />
    </div>
</div>
@endsection  
