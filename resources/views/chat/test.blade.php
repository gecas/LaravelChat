@extends('layout')

@section('content')
    <p id="power">0</p>
@stop

@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.19/vue.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.5/socket.io.min.js"></script>
    <script>
        var socket = io('http://localhost:3000');
        //var socket = io('http://192.168.10.10:3000');
        socket.on("test-channel:App\\Events\\ChatMessages", function(message){
            // increase the power everytime we load test route
            console.log(message);
            $('#power').text(parseInt($('#power').text()) + parseInt(message.data.power));
        });
    </script>
@stop