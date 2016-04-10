var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();
var users = nicknames = {};

http.listen(3000, function(){
    console.log('Listening on Port 3000');
});


io.on('connection', function (socket) {

  socket.on('join', function (user) {
        console.info('New client connected (id=' + user.id + ' (' + user.name + ') => socket=' + socket.id + ').');

        

  function updateNicknames() {
            // send connected users to all sockets to display in nickname list
            io.sockets.emit('chat.users', nicknames);
  }

  updateNicknames();

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

     // save socket to emit later on a specific one
        socket.userId   = msg.send_to;
        socket.name = msg.name;
        socket.lastname = msg.lastname; 


        // store connected nicknames
        nicknames = {
            'id':msg.send_to,
            'send_from':msg.send_from,
            'name': msg.name,
            'lastname':msg.lastname,
            'socketId': socket.id,
        };
        users[msg.send_to] = socket;

       // io.sockets.on('connection', function (socket) {
        //console.log('User has connected' + socket);
       //});

        console.log("test "+socket.id);
        //io.to(socket.id).emit(channel + ':' + message.event, msg);
        //io.to(socketid).emit('message', 'for your eyes only');

    });

    socket.on('disconnect', function() {
            if( ! socket.nickname) return;
            delete users[user.id];
            delete nicknames[user.id];

            updateNicknames();

            console.info('Client gone (id=' + user.id+ ' => socket=' + socket.id + ').');
    });
  });
});