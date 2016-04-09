var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();
var joined_users = {};

http.listen(3000, function(){
    console.log('Listening on Port 3000');
});

var users_connections = {};
io.on('connection', function (socket) {
    socket.on('join', function(user){
      users_connections[user.id] = socket.id;
      joined_users[user.id] = user;
      
      socket.on('disconnect', function() {
        delete users_connections[user.id]; 
        delete joined_users[user.id]; 
        io.emit('logged_users', joined_users);
      });
      redis.subscribe('chat-messages', function(err, count) {});

      io.emit('logged_users', joined_users);
    });

    
});

redis.on('message', function(channel, data) {
  message = JSON.parse(data);
  ms = JSON.parse(message.data.data);
   
  var msg = {
    'message': ms.message.message,
    'send_from': ms.message.send_from,
    'send_to': ms.message.send_to,
    'name':ms.user.name,
    'lastname':ms.user.lastname
  };

  io.sockets.to(users_connections[ms.message.send_to]).emit("send_to", msg);
});