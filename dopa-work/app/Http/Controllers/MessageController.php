<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $conversations = Conversation::where('client_id', $userId)
            ->orWhere('freelancer_id', $userId)
            ->with(['client', 'freelancer', 'latestMessage', 'service', 'messages' => fn($q) => $q->select('id','conversation_id','sender_id','is_read')])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorizeParticipant($conversation);

        $messages = Message::where('conversation_id', $conversation->id)
            ->with('sender')
            ->orderBy('created_at')
            ->paginate(50);

        // Mark unread messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $conversation->load(['client', 'freelancer', 'service']);

        return view('messages.show', compact('conversation', 'messages'));
    }

    public function send(Request $request, Conversation $conversation)
    {
        $this->authorizeParticipant($conversation);

        $request->validate([
            'body'       => 'nullable|string|max:2000|required_without:attachment',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $attachmentPath = null;
        $attachmentType = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store("messages/{$conversation->id}", 'public');
            $attachmentType = $file->getMimeType();
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $request->body,
            'attachment' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back();
    }

    public function startConversation(Service $service)
    {
        $userId = Auth::id();
        $freelancerId = $service->user_id;

        if ($userId === $freelancerId) {
            return redirect()->route('messages.index');
        }

        $conversation = Conversation::firstOrCreate(
            [
                'client_id' => $userId,
                'freelancer_id' => $freelancerId,
                'service_id' => $service->id,
            ],
            ['last_message_at' => now()]
        );

        return redirect()->route('messages.show', $conversation);
    }

    private function authorizeParticipant(Conversation $conversation): void
    {
        $userId = Auth::id();
        if ($conversation->client_id !== $userId && $conversation->freelancer_id !== $userId) {
            abort(403);
        }
    }
}
