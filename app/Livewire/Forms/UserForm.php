<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\User;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;

class UserForm extends Form
{
    public ?User $user;

    #[Locked]
    public $id;

    #[Validate('required|min:3', as:'Nama')]
    public $name;
    
    #[Validate('required|min:3|max:3', as:'Inisial')]
    public $initial;
    
    #[Validate('required|email', as:'Email')]
    public $email;

    public 
        $status = [],
        $roles = [],
        $permissions = [];

    public function setUser(User $user)
    {
        $userPermissions = [];
        foreach (\Spatie\Permission\Models\Permission::select('id','name')->get() as $permission) {
            if ($user->hasDirectPermission($permission->name)) {
                $userPermissions[] = $permission->name; // Or $permission->name for just names
            }
        }
        
        $this->fill([
            'user' => $user,
            'id' => $user->id,
            'name' => $user->name,
            'initial' => $user->initial,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name'),
            'permissions' => $userPermissions,
            'status' => $user->status
        ]);
    }

    public function setRoleOptions(): array
    {
        $setRoles = [];

        $roles = \Spatie\Permission\Models\Role::select('id','name')->get();

        foreach ($roles as $key => $role) {
            $setRoles[$key] = ['id' => $role->id, 'name' => $role->name];
        }

        return $setRoles;
    }

    public function setPermissionOptions(): array
    {
        $setPermissions = [];

        $permissions = \Spatie\Permission\Models\Permission::select('id','name')->get();

        foreach ($permissions as $key => $permission) {
            $setPermissions[$key] = ['id' => $permission->id, 'name' => $permission->name];
        }

        return $setPermissions;
    }

    public function setStatusOptions(): array
    {
        return [
            ['id' => 1 , 'label' => 'Aktif'],
            ['id' => 0 , 'label' => 'Non Aktif']
        ];
    }

    public function update()
    {
        $this->user->update([
            'name' => $this->name,
            'initial' => $this->initial,
            'status' => $this->status,
        ]);

        
        $this->user->syncRoles($this->roles);
        $this->user->syncPermissions($this->permissions);
    }
}
