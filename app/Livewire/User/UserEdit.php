<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Gate;

class UserEdit extends Component
{
    use Swalable;

    public \App\Livewire\Forms\UserForm $form;

    public $modal = 'modal-edit-user';

    #[On('edit-user')]
    public function setUser(User $user)
    {
        $this->form->setUser($user);

        $this->dispatch('set-role-edit-options', id: $this->form->roles, data: $this->form->setRoleOptions());
        $this->dispatch('set-permission-edit-options', id: $this->form->permissions, data: $this->form->setPermissionOptions());
        $this->dispatch($this->modal, open: true);
    }

    public function save()
    {
        $this->validate();

        Gate::authorize('manage-user');

        try {
            $this->form->update();
            $this->toastSuccess('Data user berhasil diupdate.');
            $this->dispatch('user-updated')->to(UserTable::class);
            $this->dispatch('set-reset');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.user.user-edit');
    }
}
