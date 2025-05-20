<?php

namespace App\Http\Livewire;

use App\Models\Called;
use Livewire\Component;
use Livewire\WithFileUploads;

class CalledChat extends Component
{
    use WithFileUploads;

    public $record;
    public $newMessage;
    public $newAttachment;

    public function mount(Called $record): void
    {
        $this->record = $record;
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required_without:newAttachment|string|max:1000',
            'newAttachment' => 'nullable|file|max:10240',
        ]);

        $path = null;
        if ($this->newAttachment) {
            $path = $this->newAttachment->store('attachments', 'public');
        }

        $this->record->interactions()->create([
            'user_id' => auth()->id(),
            'message' => $this->newMessage,
            'attachment' => $path,
        ]);

        $this->reset(['newMessage', 'newAttachment']);
    }

    public function render()
    {
        return view('livewire.called-chat');

    }
}
