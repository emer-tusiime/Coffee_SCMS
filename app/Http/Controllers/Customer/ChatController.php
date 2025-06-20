<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $messages = ChatMessage::where('receiver_type', 'customer')
            ->where('receiver_id', Auth::user()->customer->id)
            ->orWhere('sender_type', 'customer')
            ->where('sender_id', Auth::user()->customer->id)
            ->with('sender')
            ->latest()
            ->get();

        return view('customer.chat.index', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'receiver_type' => 'required|string',
            'receiver_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $customer = Auth::user()->customer;
        $message = ChatMessage::create([
            'message' => $request->message,
            'sender_type' => 'customer',
            'sender_id' => $customer->id,
            'receiver_type' => $request->receiver_type,
            'receiver_id' => $request->receiver_id,
        ]);

        // Broadcast the message
        broadcast(new \App\Events\NewMessage($message))->toOthers();

        return response()->json(['success' => 'Message sent successfully']);
    }
}
