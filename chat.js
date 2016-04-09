var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();

redis.subscribe('chat-messages', function(err, count) {
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

     io.sockets.on('connection', function (socket) {
        console.log('User has connected' + socket);
  });
        io.emit(channel + ':' + message.event, msg);

});

http.listen(3000, function(){
    console.log('Listening on Port 3000');
});
