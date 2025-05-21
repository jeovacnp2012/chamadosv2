<?php

namespace App\Livewire;

use App\Models\Called;
use Livewire\Component;
use Livewire\WithPagination;

class CalledMessages extends Component
{
    use WithPagination;

    public Called $called;
    public $showMore = false;

    protected $listeners = ['message-sent' => '$refresh'];
    public function showMoreMessages()
    {
        $this->showMore = true;
    }
    public function render()
    {
//        return view('livewire.called-messages', [
//            'messages' => $this->called->interactions()
//                ->with('user')
//                ->latest()
//                ->paginate(5),
//        ]);
        $baseQuery = $this->called->interactions()->with('user')->latest();

        return view('livewire.called-messages', [
            'recentMessages' => $baseQuery->take(3)->get(),
            'olderMessages' => $this->showMore
        ? $baseQuery->skip(3)->paginate(10)
                : collect(),
        ]);
    }
}
