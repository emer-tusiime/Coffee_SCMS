<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatMessage;

class ChatMessagesSeeder extends Seeder
{
    public function run()
    {
        $messages = [
            [
                'sender_id' => 2,
                'receiver_id' => 3,
                'message' => 'Hello, do you have any updates on the latest shipment?',
                'timestamp' => now()->subMinutes(30),
            ],
            [
                'sender_id' => 3,
                'receiver_id' => 2,
                'message' => 'Yes, it will arrive tomorrow morning.',
                'timestamp' => now()->subMinutes(25),
            ],
            [
                'sender_id' => 4,
                'receiver_id' => 2,
                'message' => 'Can we get a restock of Arabica Classic next week?',
                'timestamp' => now()->subMinutes(20),
            ],
        ];

        foreach ($messages as $msg) {
            ChatMessage::create($msg);
        }
    }
}