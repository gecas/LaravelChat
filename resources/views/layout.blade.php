<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

        <meta id="token" name="token" value="{{csrf_token()}}">
    </head>
    <body style="padding: 70px;">
        @include('partials.nav')
        <div class="container">
          <pre id="socket"></pre>
        </div>
        <div class="container">
        @yield('content')
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.19/vue.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/0.7.0/vue-resource.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.5/socket.io.min.js"></script>
        <script>

         Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');

        new Vue({

            el: 'body',

            data: {

            active: false,
            currentChat:true,
            user: '',
            user_id:'',
            message:'',
            messages: [],
            message_to: '',
            users: [],
            current_user:'',
            current_users:[],
            nicknames:[],
            post_user:'',
            send_to:'',
            chats: []

            },

            ready: function(){
              <?php if(Auth::user()):?>

              
               this.getAllUsers();

               var that = this;

               var socket = io('http://localhost:3000');
               socket.emit('join', {id: "<?= Auth::user()->id ?>", name: "<?= Auth::user()->name ?>", 'lastname': "<?= Auth::user()->lastname ?>"});             

              /*socket.on('chat.users', function (nicks) { 
                that.nicknames = nicks;
              });*/

              socket.on('logged_users', function (nicks) { that.nicknames = nicks; });
              socket.on('send_to', function (message) {
                $("<div/>").appendTo("body").css({
                  "position": "fixed",
                  "width": "200px",
                  "height": "50px",
                  "background": "red",
                  "top": 0,
                  "right": 0,
                  "z-index": 10000,
                }).text("message from "+message.name).on('click', function(event) {
                  var that_msg =  $(this);
                  that_msg.fadeOut(1000, function(){
                    that.updateChat( message.send_from );
                    that_msg.remove();
                  });
                });

                if(that.post_user == message.send_from){
                  that.messages.push(message);
                }
              });

              /*socket.on("chat-messages:App\\Events\\Messages", function(message){
                that.messages.push(message);
              });*/

              <?php endif; ?>

            },

            methods: {

                getAllUsers: function()
                {
                    this.$http.get('users/getAll').then(function (results) {
                         if (!results.error) {
                          this.$set('users', results.data.users);
                          this.$set('current_users', results.data.current_users);
                         }
                      }, function (response) {
                          console.log(error);
                      });
                },

                updateChat: function(user_id){
                this.$http.post('/chats/current/'+user_id).then(function (results){
                       this.$set('post_user', user_id);
                       this.$set('users', results.data.users);
                       this.$set('current_users', results.data.current_users);
                       this.$set('current_user', results.data.current_user);
                       this.$set('messages', results.data.messages);
                       this.active = true;
                      }, function (response) {
                          console.log(error);
                      });
                },

                postChat: function(user_id){
                this.$http.post('/chats/current/'+user_id).then(function (results){
                       this.$set('post_user', user_id);
                       this.$set('users', results.data.users);
                       this.$set('current_users', results.data.current_users);
                       this.$set('messages', results.data.messages);
                       this.active = true;
                      }, function (response) {
                          console.log(error);
                      });
                },

            postMessage: function(user_id, message){
                this.$http.post('/chats/message/'+user_id,{"message":this.message}).then(function (results){
                    this.message = '';
                    this.$set('users', results.data.users);
                    this.$set('current_users', results.data.current_users);
                    this.$set('messages', results.data.messages);
                  }, function (response) {
                      console.log(error);
                  });
            },
            }

            
        });
    </script>
      <script>
        // var socket = io('http://localhost:3000');
        // //var socket = io('http://192.168.10.10:3000');
        // //socket.on("chat-messages:App\\Events\\Messages", function(channel, data){

        //   socket.on("chat-messages:App\\Events\\Messages", function(message){
        //     // increase the power everytime we load test route
        //    $('#socket').text(message.message);
        // });
    </script>
   
        @yield('footer')
    </body>
</html>
