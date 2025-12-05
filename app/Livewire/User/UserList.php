<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Enums\System\SystemLanguageEnum;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public string $tab = 'teachers';

    public string $language_filter = '';

    /** @var SystemLanguageEnum[] */
    public array $languages = [];

    public function mount(): void
    {
        $this->languages = SystemLanguageEnum::all();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function updatedLanguageFilter(): void
    {
        $this->resetPage();
    }

    public function getUsers(): LengthAwarePaginator
    {
        $query = User::query()
            ->where('id', '!=', Auth::id())
            ->whereHas('roles');

        if ($this->tab === 'teachers') {
            $query->where('is_accepting_students', '=', true)
                ->whereHas('roles', function ($q) {
                    $q->where('name', '=', 'teacher');
                });

            if ($this->language_filter) {
                $query->whereHas('teachingLanguages', function ($q) {
                    $q->where('language', '=', $this->language_filter);
                });
            }
        } else {
            $query->where('is_looking_for_teacher', '=', true)
                ->whereHas('roles', function ($q) {
                    $q->where('name', '=', 'student');
                });

            if ($this->language_filter) {
                $query->where('learning_lang', '=', $this->language_filter);
            }
        }

        return $query->latest()->paginate(12);
    }

    public function render(): Factory|View|\Illuminate\View\View
    {
        return view('livewire.user.user-list', [
            'users' => $this->getUsers(),
        ]);
    }
}
