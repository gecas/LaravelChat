<nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Chat App</a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li><a href="/chats">Chats</a></li>
              <li class="socket-info"></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              @if(Auth::user())
              <li><a href="/logout">Logout</a></li>
              @else
              <li><a href="/register">Register</a></li>
              <li><a href="/login">Login</a></li>
              @endif
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>