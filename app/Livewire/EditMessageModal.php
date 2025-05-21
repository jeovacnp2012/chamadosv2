<?php

namespace App\Livewire;

use App\Models\Interaction;
use Livewire\Component;

class EditMessageModal extends Component
{
    public $editingMessageId;
    public $editingMessageText;
    public $showEditModal = false;

    protected $listeners = ['open-edit-modal' => 'handleEditRequest'];

    public function handleEditRequest($payload = null)
    {
        $id = is_array($payload) ? ($payload['id'] ?? null) : $payload;

        if (! $id) return;

        $msg = \App\Models\Interaction::findOrFail($id);

        $this->editingMessageId = $msg->id;
        $this->editingMessageText = $msg->message;

        if ($msg->created_at->diffInHours(now()) >= 8) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Não é mais possível editar esta mensagem. Prazo de 8h expirado.'
            ]);
            return;
        }

        $this->showEditModal = true;
    }

    public function saveEditedMessage()
    {
        $this->validate([
            'editingMessageText' => 'required|string|min:2',
        ]);

        $msg = Interaction::findOrFail($this->editingMessageId);
        $msg->message = $this->editingMessageText;
        $msg->save();

        $this->reset(['editingMessageId', 'editingMessageText', 'showEditModal']);
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.edit-message-modal');
    }
}
