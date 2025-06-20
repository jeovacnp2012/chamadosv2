<?php

namespace App\Livewire;

use App\Models\Called;
use App\Models\Interaction;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class CalledMessages extends Component
{
    use WithPagination;

    public Called $called;
    public $showMore = false;
    public $editingMessageId = null;
    public $editingMessageText = '';
    public $showEditModal = false;

    // Livewire 3 usa dispatch em vez de listeners
    protected function getListeners()
    {
        return ['message-sent' => '$refresh'];
    }

    public function mount(Called $called)
    {
        $this->called = $called;
    }

    // Certifique-se de que este método é público
    public function openEditModal($id)
    {
        Log::info('openEditModal chamado com ID: ' . $id);

        try {
            $message = Interaction::findOrFail($id);

            // Verificar se a mensagem ainda pode ser editada
            if ($message->created_at->diffInHours(now()) >= 8) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Não é mais possível editar esta mensagem. Prazo de 8h expirado.'
                ]);
                return;
            }

            $this->editingMessageId = $message->id;
            $this->editingMessageText = $message->message;
            $this->showEditModal = true;

            Log::info('Modal deve ser exibido agora', [
                'showEditModal' => $this->showEditModal,
                'editingMessageId' => $this->editingMessageId
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao abrir modal: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erro ao abrir o modal. Por favor, tente novamente.'
            ]);
        }
    }

    public function saveEditedMessage()
    {
        $this->validate([
            'editingMessageText' => 'required|string|min:2',
        ]);

        try {
            $message = Interaction::findOrFail($this->editingMessageId);

            // Verificar novamente se a mensagem ainda pode ser editada
            if ($message->created_at->diffInHours(now()) >= 8) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Não é mais possível editar esta mensagem. Prazo de 8h expirado.'
                ]);
                return;
            }

            $message->message = $this->editingMessageText;
            $message->save();

            $this->reset(['editingMessageId', 'editingMessageText', 'showEditModal']);
            $this->dispatch('message-sent');

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Mensagem atualizada com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar mensagem editada: ' . $e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erro ao salvar a mensagem. Por favor, tente novamente.'
            ]);
        }
    }

    public function showMoreMessages()
    {
        $this->showMore = true;
    }

    public function render()
    {
        // Query base com eager loading forçado dos relacionamentos
        $baseQuery = $this->called->interactions()
            ->with([
                'user' => function($query) {
                    $query->with(['sectors', 'departaments']);
                }
            ])
            ->latest();

        // Para debug - vamos verificar se os dados estão sendo carregados
        $recentMessages = $baseQuery->paginate(2);

        // Log para debug (remover depois)
        if (config('app.debug')) {
            foreach ($recentMessages as $message) {
                Log::info('Debug Message', [
                    'user_id' => $message->user->id,
                    'user_name' => $message->user->name,
                    'sectors_count' => $message->user->sectors->count(),
                    'departaments_count' => $message->user->departaments->count(),
                    'sectors' => $message->user->sectors->pluck('name')->toArray(),
                    'departaments' => $message->user->departaments->pluck('name')->toArray(),
                ]);
            }
        }

        return view('livewire.called-messages', [
            'recentMessages' => $recentMessages,
            'olderMessages' => $this->showMore
                ? $baseQuery->skip(3)->paginate(10)
                : collect(),
        ]);
    }

    public function deleteMessage($id)
    {
        $message = $this->called->interactions()->findOrFail($id);
        $message->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Mensagem excluída com sucesso.',
        ]);
    }
}
