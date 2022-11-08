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
        return view('chat');
    }

    public function users()
    {
        //    return User::has('messages')->where('id','!=',auth('web')->id())->get();
        return User::where('id', '!=', auth('web')->id())->get();
    }

    public function messages()
    {
        return Message::with('user')->get();
    }

    public function contactMessages($receiver)
    {
        return Message::with('receiver', 'sender')->where('sender_id', auth('web')->id())->where('receiver_id', $receiver)->orWhere('sender_id', $receiver)->where('receiver_id', auth('web')->id())->get();
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
