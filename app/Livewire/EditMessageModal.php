<?php

namespace App\Livewire;

use App\Models\Interaction;
use Carbon\Carbon;
use Livewire\Component;

class EditMessageModal extends Component
{
    public $messageId;
    public $message;
    public $showModal = false;

    protected $listeners = ['open-edit-modal' => 'handleEditRequest'];

    public function mount()
    {
        // Deixe vazio. Não aceite parâmetros aqui.
    }

    public function handleEditRequest($id)
    {
        $msg = Interaction::findOrFail($id);

        $this->messageId = $msg->id;
        $this->message = $msg->message;

        if ($msg->created_at->diffInHours(now()) >= 8) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Não é mais possível editar esta mensagem. Prazo de 8h expirado.'
            ]);
            return;
        }

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'message' => 'required|string|min:2',
        ]);

        $msg = Interaction::findOrFail($this->messageId);

        if ($msg->created_at->diffInHours(now()) >= 8) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Não é mais possível editar esta mensagem. Prazo de 8h expirado.'
            ]);
            return;
        }

        $msg->message = $this->message;
        $msg->save();

        $this->reset(['messageId', 'message', 'showModal']);
        $this->dispatch('message-sent');
    }

    public function render()
    {
        return view('livewire.edit-message-modal');
    }
}
