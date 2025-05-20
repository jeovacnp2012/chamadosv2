<?php

namespace App\Livewire;

use App\Models\Called;
use App\Models\Interaction;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CalledChat extends Component
{
    use WithFileUploads;

    public $record;
    public $newMessage;
    public $newAttachment;
    public $editMessageId;
    public $editMessageText;

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
            'attachment_path' => $path,
        ]);

        $this->reset(['newMessage', 'newAttachment']);

        $this->dispatch('toast', 'Mensagem enviada com sucesso');
        $this->dispatch('scrollToBottom');
    }

    public function editMessage($id)
    {
        $message = Interaction::findOrFail($id);
        if ($message->user_id !== auth()->id()) abort(403);

        $this->editMessageId = $message->id;
        $this->editMessageText = $message->message;
    }

    public function updateMessage()
    {
        $this->validate([
            'editMessageText' => 'required|string|max:1000',
        ]);

        $message = Interaction::findOrFail($this->editMessageId);
        if ($message->user_id !== auth()->id()) abort(403);

        $message->update(['message' => $this->editMessageText]);

        $this->reset(['editMessageId', 'editMessageText']);
        $this->dispatch('toast', 'Mensagem editada com sucesso');
    }

    public function cancelEdit()
    {
        $this->reset(['editMessageId', 'editMessageText']);
    }

    public function deleteMessage($id)
    {
        $message = Interaction::findOrFail($id);
        if ($message->user_id !== auth()->id()) abort(403);

        if ($message->attachment_path && Storage::disk('public')->exists($message->attachment_path)) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();
        $this->dispatch('toast', 'Mensagem excluída');
    }

    public function render()
    {
        return view('livewire.called-chat', [
            'record' => $this->record,
            'messages' => $this->record->interactions()->with('user')->latest()->get(),
        ]);
    }
}

