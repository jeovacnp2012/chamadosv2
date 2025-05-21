<?php

namespace App\Livewire;

use App\Models\Interaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class SendMessageForm extends Component
{
    use WithFileUploads;

    public $called;
    public $message;
    public $attachment;

    public function save()
    {
        $this->validate([
        'message' => 'required|string|min:2',
        'attachment' => 'nullable|file|max:5120',
    ]);

        $path = $this->attachment
        ? $this->attachment->store('attachments', 'public')
            : null;

        Interaction::create([
            'called_id' => $this->called->id,
            'user_id' => Auth::id(),
            'message' => $this->message,
            'attachment_path' => $path,
        ]);

        $this->reset(['message', 'attachment']);
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.send-message-form');
    }
}
