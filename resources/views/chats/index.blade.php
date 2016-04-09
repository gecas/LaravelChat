@extends('layout')

@section('content')
<style>
	.users-list:hover{
		cursor: pointer;
	}
	.user-image{
		background-size: cover;
		width: 35px;
		height: 35px;
		position: relative;
		display: inline-block;
	}
	.messages-list{
		height: 200px;
		overflow-y:scroll;
		bottom: 0;
	}
</style>

{{--*/ $user_id = Auth::user()->id /*--}}
	<div class="col-md-12">

		<div class="col-md-3" style="background-color: #e3e3e3;height: 800px;">
			<ul class="list-group">
					
				<li class="list-group-item users-list" v-for="current_user in current_users" v-on:click="updateChat( current_user.id )">
				<div>
				<div class="user-image"  :style="{ backgroundImage: 'url(' + current_user.avatar_path+current_user.avatar_name + ')', height:35+ 'px'}"></div>

				<span style="line-height: 35px;">@{{ current_user.name+' '+current_user.lastname }}</span>
				</div>
				</li>

			</ul>
		</div>
		<div class="col-md-5" style="height: 800px;">

		<div class="col-md-12" style="margin: 10px;">
		<ul class="list-group messages-list" v-if="messages.length > 0">
			
			<li class="list-group-item" v-for="message in messages">
			<div v-if="message.send_to != current_user.id" class="text-left alert alert-info">
			<em>@{{ message.name+' '+message.lastname }}</em>
			<p>@{{ message.message | json }}</p>
			</div>
			<div v-else class="text-right alert alert-warning">
			<em>@{{ message.name+' '+message.lastname }}</em>
			<p>@{{ message.message | json }}</p>
			</div>
			</li>
		</ul>	
		
		<h5 v-else class="text-center" >No current chats available</h5>
		<div v-show="active" class="col-md-12">
			<textarea name="message" v-model="message" cols="45" class="form-control" rows="10" style="margin-bottom: 10px;"></textarea>
			<button v-on:click="postMessage(post_user)" class="btn btn-danger form-control">Send message</button>
		</div>	
		</div>	

		</div>

		<div class="col-md-4" style="background-color: #e3e3e3;height: 800px;">
		<ul class="list-group">

			<li class="list-group-item users-list" v-for="user in users" v-on:click="postChat( user.id )" >
				<div>

				<div class="user-image"  :style="{ backgroundImage: 'url(' + user.avatar_path+user.avatar_name + ')', height:35+ 'px'}"></div>

				<span style="line-height: 35px;">@{{ user.name+' '+user.lastname }}</span>
				</div>
				</li>

				<li>No new users</li>

			</ul>

			<ul class="list-group">
				<h4 class="text-center">Currently logged in users</h4>
				<li class="list-group-item users-list" v-for="user in nicknames" v-on:click="postChat( user.id )" >
				<div>

				<span style="line-height: 35px;">@{{ user.name+' '+user.lastname }}</span>
				</div>
				</li>
			</ul>
		</div>

	</div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	 <script>
	 var messages_list = $('.messages-list').attr("data-length");
	 //console.log(messages_list);
    //   $(function() {
    //   var wtf    = $('.messages-list');
    //   var height = wtf[0].scrollHeight;
    //   wtf.scrollTop(height);
    // });
    </script>
@endsection