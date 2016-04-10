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
			
			<a href="#" class="close" data-toggle="modal" data-target="#myMessageDeleteModal" 
			v-on:click="updateData(message)">&times;</a>

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
			<div class="form-group">
				<label>Post on enter</label>
				<input type="checkbox" v-model="enter" v-if="enter" v-on:click="toggleEnter" checked>
				<input type="checkbox" v-model="enter" v-else v-on:click="toggleEnter">
			</div>
			<textarea name="message" v-model="message" v-if="enter" v-on:keyup.enter="postMessage(post_user)" cols="45" class="form-control" rows="10" style="margin-bottom: 10px;"></textarea>
			<textarea name="message" v-model="message" v-else cols="45" class="form-control" rows="10" style="margin-bottom: 10px;"></textarea>
			<button v-on:click="postMessage(post_user)" class="btn btn-danger form-control">Send message</button>
		</div>	
		</div>	

		</div>

		<div class="col-md-4" style="background-color: #e3e3e3;height: 800px;">
		<ul class="list-group">

			<li class="list-group-item users-list" v-for="user in users" v-if="users.length > 0" v-on:click="postChat( user.id )" >
				<div>
				
				<div class="user-image"  :style="{ backgroundImage: 'url(' + user.avatar_path+user.avatar_name + ')', height:35+ 'px'}"></div>

				<span style="line-height: 35px;">@{{ user.name+' '+user.lastname }}</span>
				</div>
				</li>

			</ul>
			<p v-if="users.length == 0" class="text-center">No new users</p>

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

	<!-- Modal -->
<div id="myMessageDeleteModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete message? </h4>
      </div>
      <div class="modal-body">
      <p class="text-center"> Do you really want to delete message ? </p>
      </div>
      <div class="modal-footer">
       <button type="submit" v-on:click="deleteMessage(message_obj)" class="btn btn-danger">Delete</button>
       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

@endsection