<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="/sweetalert/dist/sweetalert.css">
        <meta id="token" name="token" value="{{csrf_token()}}">
    </head>
    <style type="text/css">
        .flash {
          background: #F6624A;
          color: #fff;
          width: 200px;
          position: fixed;
          right: 20px;
          bottom: 20px;
          word-break: break-all;
          padding: 1em;
          display: none;
         }
        .flash::after{
        content: '';
        position: absolute;
        left: -20px;
        top: 5px;
        border-left: 10px solid transparent;
        border-top: 10px solid transparent;
        border-right: 10px solid #F6624A;
        border-bottom: 10px solid transparent;
        } 

        .flash:hover{
          cursor: pointer;
        }

        body { padding-top: 150px; width: 100% !important; overflow-x: hidden; }
        </style>
    <body style="padding: 70px;">
        @include('partials.nav')
        <div class="container">
        </div>
        <div class="container">
        @yield('content')
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.19/vue.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/0.7.0/vue-resource.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.5/socket.io.min.js"></script>
        <script src="/sweetalert/dist/sweetalert.min.js"></script>
        <script src="/sweetalert/dist/sweetalert-dev.js"></script>
        <script src="/js/sound.js"></script>
        <script>

         Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');

        new Vue({

            el: 'body',

            data: {

            active: false,
            enter: true,
            currentChat:true,
            user: '',
            user_id:'',
            message:'',
            message_id:'',
            message_send_to:'',
            message_obj:{},
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

              var block = document.createElement("div");
              $(block).addClass("flash").html( message.name + " " + message.lastname + ":" + "<br/>" + message.message );

                var audio = document.createElement('audio');
                audio.src = '/sounds/notif.mp3';
                audio.play();

                  $("body").append(block);

                    $(block).fadeIn(2500);

                    $('.messages-list').animate({ scrollTop: document.getElementsByClassName("messages-list")[0].scrollHeight }, 50);

                    $(block).on('click', function(event) {
                      $(this).fadeOut(1000, function(){
                        that.updateChat( message.send_from );
                      });
                    });

                    setTimeout(function(){
                      $(block).fadeOut(1000, function(){
                        $(this).remove();
                      });
                    }, 10000);


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

                toggleEnter: function()
                {
                  this.enter = !this.enter;
                },

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
                       setTimeout(function(){
                        $('.messages-list').animate({ scrollTop: document.getElementsByClassName("messages-list")[0].scrollHeight }, 50);
                       }, 200);
                      }, function (response) {
                          console.log(error);
                      });
                },

                updateData: function(message) {
                    this.message_obj = message;
                },

                deleteMessage: function(message) {
                this.$http.delete('/chats/'+message.id+'/'+message.send_to).then(function(results){
                      this.messages.$remove(message);
                      $('#myMessageDeleteModal').modal('hide');
                      //flashMessage('Message has been deleted!');
                      flashMessage({message: 'Message has been deleted!', time: 2000});
                  },function(error) {
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
                       setTimeout(function(){
                        $('.messages-list').animate({ scrollTop: document.getElementsByClassName("messages-list")[0].scrollHeight }, 50);
                       }, 200);
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
                    setTimeout(function(){
                        $('.messages-list').animate({ scrollTop: document.getElementsByClassName("messages-list")[0].scrollHeight }, 50);
                       }, 200);
                  }, function (response) {
                      console.log(error);
                  });
            },
            }

            
        });
    </script>
     <script>
    function showMessage(title, message, type){
            swal({   
                title: title,   
                text: message,   
                type: type,   
                timer:1500,
                showConfirmButton:false
            });
        }

         // function flashMessage(message, time = 1000){
         //     var block = document.createElement("div");
         //     $(block).addClass("flash").html(message);

         //     $("body").append(block);

         //     $(block).fadeIn(2500);
         //     setTimeout(function(){
         //         $(block).fadeOut(2500, function(){
         //             $(this).remove();
         //         });
         //     }, time);
         // }

function flashMessage(params){
        var values = {
        message: "",
        time: 1000 
        };
         var config = $.extend(true, values, params);
 
        var block = document.createElement("div");
        $(block).addClass("flash").html(config.message);
        $("body").append(block);
        $(block).fadeIn(2500);
          setTimeout(function(){
          $(block).fadeOut(2500, function(){
          $(this).remove();
          });
        }, config.time);

}
    </script>
   
        @yield('footer')
        @include('flash')
    </body>
</html>
