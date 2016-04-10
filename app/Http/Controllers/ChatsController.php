<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Message;
use Auth;
use DB;
use App\Events\Messages;
use App\Http\Requests;

class ChatsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('chats.index');
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
       // $this->validate($request, ['message'=>'required']);
       
        $message_text = $request->message;

        $user1 = Auth::user();

        $user2 = User::where('id', '=', $id)->first();

        $message = Message::create(['send_from' => $user1->id, 'send_to' => $user2->id, 'message'=>$message_text]);

        $messages = $this->fetchUsers($id);

        $current_users = $this->allUsers()["current_users"];
        $users = $this->allUsers()["users"];

        event(new \App\Events\Messages($message, $user1));

        return response()->json(array('current_users' => $current_users, 'users'=>$users, 'messages'=>$messages));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $send_to)
    {
        $message = Message::findOrFail($id);

        $message->delete();

        $messages = $this->fetchUsers($send_to);

        $current_users = $this->allUsers()["current_users"];
        $users = $this->allUsers()["users"];
    }

    public function createChat($id)
    {
        $user1 = Auth::user();

        $user2 = User::where('id', '=', $id)->first();

        $current_users = DB::select( DB::raw('

        SELECT u.id AS id, u.name AS name, u.lastname AS lastname, u.avatar_path AS avatar_path, u.avatar_name AS avatar_name
        FROM users AS u
        LEFT JOIN messages AS m ON m.send_to = u.id
        WHERE (
        m.send_from = '.$user1->id.'
        AND u.id != '.$user1->id.'
        )
        GROUP BY m.send_from, m.send_to
        ORDER BY m.created_at

        ') );

        array_push($current_users, $user2);

        return response()->json($current_users);


    }

    public function getChats($id)
    {
        $messages = $this->fetchUsers($id);

        $current_users = $this->allUsers()["current_users"];
        $users = $this->allUsers()["users"];

        $current_user = Auth::user();

        return response()->json(array('current_user'=>$current_user,'current_users' => $current_users, 'users'=>$users, 'messages'=>$messages));
    }

    public function getAllUsers()
    {
        if (Auth::user()) {

        $current_users = $this->allUsers()["current_users"];
        $users = $this->allUsers()["users"];

        return response()->json(array('current_users' => $current_users, 'users'=>$users));

        } else{
           return response()->json(array('error' => 'Unauthorized')); 
        }

    }

    private function allUsers()
    {
        $user = Auth::user();

        $idd = $user->id;
        $current_users = DB::select( DB::raw('

         SELECT 
                u.id AS id, 
                u.name AS name, 
                u.lastname AS lastname, 
                u.avatar_path AS avatar_path, 
                u.avatar_name AS avatar_name
            FROM users AS u
            WHERE 
            u.id IN(
                SELECT 
                    t.id 
                FROM (
                    SELECT 
                        m.send_to AS `id`,
                        m.created_at
                    FROM messages AS m 
                    WHERE m.send_from = '.$idd.'
                    UNION
                    SELECT 
                        m.send_from AS `id`,
                        m.created_at
                    FROM messages AS m 
                    WHERE m.send_to = '.$idd.'
                ) AS t 
                GROUP BY t.id 
                ORDER BY t.created_at
            )

        ') );

        $users = DB::select( DB::raw('
            SELECT 
                u.id AS id, 
                u.name AS name, 
                u.lastname AS lastname, 
                u.avatar_path AS avatar_path, 
                u.avatar_name AS avatar_name
            FROM users AS u
            WHERE 
            u.id NOT IN(
                SELECT 
                    t.id 
                FROM (
                    SELECT 
                        m.send_to AS `id`,
                        m.created_at 
                    FROM messages AS m 
                    WHERE m.send_from = '.$idd.'
                    UNION
                    SELECT 
                        m.send_from AS `id`,
                        m.created_at
                    FROM messages AS m 
                    WHERE m.send_to = '.$idd.'
                ) AS t 
                GROUP BY t.id
                ORDER BY t.created_at
            ) AND u.id != '.$idd.'
        ') );

        return array('current_users'=>$current_users, 'users'=>$users);
    }

    private function fetchUsers($id)
    {
        $user1 = Auth::user();

        $user2 = User::where('id', '=', $id)->first();

        $messages = DB::select( DB::raw('

        SELECT m.id, m.send_to, m.send_from, m.message, u.name, u.lastname
        FROM messages AS m
        LEFT JOIN users AS u ON u.id = m.send_from
        WHERE (
        m.send_to = '.$user2->id.'
        AND m.send_from = '.$user1->id.'
        )
        OR (
        m.send_to = '.$user1->id.'
        AND m.send_from = '.$user2->id.'
        )
        ORDER BY m.created_at

        ') );

        return $messages;

        
    }
}
