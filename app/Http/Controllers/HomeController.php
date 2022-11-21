<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function chat()
    {
        // dd(User::withCount('messages','unread_messages')->where('id', '!=', auth('web')->id())->get()->toArray());
        return view('chat');
    }

    public function users()
    {
        //    return User::has('messages')->where('id','!=',auth('web')->id())->get();
        return User::withCount('messages','unread_messages')->orderBy('unread_messages_count','DESC')->where('id', '!=', auth('web')->id())->get();
    }

    public function messages()
    {
        return Message::with('user')->get();
    }

    public function contactMessages($receiver)
    {
        return Message::with('receiver', 'sender')->where('sender_id', auth('web')->id())->where('receiver_id', $receiver)->orWhere('sender_id', $receiver)->where('receiver_id', auth('web')->id())->get();
    }

    public function read($sender)
    {
        $messages = Message::where('sender_id', $sender);
        $messages->where('read',0)->update(['read'=>1]);
    }

    public function fetchUnread($sender_id)
    {
        $user = User::withCount('messages','unread_messages')->find($sender_id);
        return response()->json($user);
    }

    public function messageStore(Request $request)
    {
        $user = User::find(Auth::id());
        $messages = Message::create([
            'receiver_id' => $request->receiver,
            'sender_id' => $request->user,
            'message' => $request->message,
        ]);
        $messages = Message::with('receiver', 'sender')->find($messages->id);
        broadcast(new SendMessage($messages))->toOthers();

        return response()->json($messages);
    }
}
