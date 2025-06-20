<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $supplier = Auth::user()->supplier;
        $messages = ChatMessage::where('supplier_id', $supplier->id)
            ->orWhere('receiver_type', 'supplier')->where('receiver_id', $supplier->id)
            ->with('sender', 'receiver')
            ->latest()
            ->get();

        return view('supplier.chat.index', compact('messages'));
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

        $supplier = Auth::user()->supplier;
        $message = ChatMessage::create([
            'message' => $request->message,
            'sender_type' => 'supplier',
            'sender_id' => $supplier->id,
            'receiver_type' => $request->receiver_type,
            'receiver_id' => $request->receiver_id,
        ]);

        // Broadcast the message
        broadcast(new \App\Events\NewMessage($message))->toOthers();

        return response()->json(['success' => 'Message sent successfully']);
    }

    public function getMessages()
    {
        $supplier = Auth::user()->supplier;
        $messages = ChatMessage::where('supplier_id', $supplier->id)
            ->orWhere('receiver_type', 'supplier')->where('receiver_id', $supplier->id)
            ->with('sender', 'receiver')
            ->latest()
            ->get();

        return response()->json($messages);
    }
}
