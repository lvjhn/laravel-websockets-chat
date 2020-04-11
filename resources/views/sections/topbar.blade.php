<div class="topbar-container container">
    <div class="row">
        <div class="title column column-20">
            <h1 onclick="window.location='/'">Websockets-Chat</h1>
        </div>
        <div class="nav column column-80" align="right">
            <div class="nav-container">
                <ul>
                    @guest
                        <li><a href="/register">Sign Up</a></li>
                        <li><a href="/login">Sign In</a></li>
                    @else 
                        <li><b>{{auth()->user()->name}}</b></li>
                        <li><a href="/logout">Log Out</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
