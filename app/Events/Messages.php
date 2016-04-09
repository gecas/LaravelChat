<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Messages extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $user)
    {
        $this->data = json_encode(array(
            'message'=> $message,
            'user'=>$user,
        ));

        //return response()->json(array('message' => $message, 'users'=>$user));
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['chat-messages'];
    }
}
