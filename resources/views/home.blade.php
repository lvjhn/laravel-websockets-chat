@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <global-chat-room user='<?= json_encode(auth()->user()) ?>' />
    </div>
</div>
@endsection  
