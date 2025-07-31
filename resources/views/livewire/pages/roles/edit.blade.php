<?php

use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Layout;

new class extends Component {
    #[Layout('components.layouts.app')]

    public $roleId;
    public $name = '';
    public $guard_name = 'web';
    public $selectedPermissions = [];
    public $description = '';

    public function mount($id)
    {
        $this->roleId = $id;
        $role = Role::with(['permissions', 'users'])->findOrFail($id);

        $this->name = $role->name;
        $this->guard_name = $role->guard_name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $this->roleId,
            'guard_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $role = Role::findOrFail($this->roleId);
        $role->update([
            'name' => $this->name,
            'guard_name' => $this->guard_name,
        ]);

        $role->syncPermissions($this->selectedPermissions);

        session()->flash('message', 'Role updated successfully.');
        return $this->redirect(route('role.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'permissions' => Permission::orderBy('name')->get(),
            'role' => Role::withCount('users')->findOrFail($this->roleId)
        ];
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1
                        class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                        {{ __('Edit Role') }}
                    </h1>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                        {{ __('Update role information and permissions') }}
                    </p>
                </div>
                <a href="{{ route('role.index') }}"
                    class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                    wire:navigate>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to Roles') }}
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('message') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Form Card -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            <form wire:submit="save" class="p-8 space-y-8">
                <!-- Basic Information -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Basic Information') }}
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                {{ __('Role Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="name" type="text" id="name"
                                class="block w-full px-4 py-3 border-0 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-sm"
                                placeholder="{{ __('Enter role name') }}">
                            @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="guard_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                {{ __('Guard Name') }} <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="guard_name" id="guard_name"
                                class="block w-full px-4 py-3 border-0 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-sm">
                                <option value="web">{{ __('Web') }}</option>
                                <option value="api">{{ __('API') }}</option>
                            </select>
                            @error('guard_name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Current Role Info -->
                <div
                    class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Current Role
                            Information') }}</h4>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                            <div class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">{{ __('Created') }}
                            </div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{
                                $role->created_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                            <div class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">{{ __('Updated') }}
                            </div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{
                                $role->updated_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                            <div class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">{{ __('Users with
                                this role') }}</div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $role->users_count ?? 0
                                }}</div>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                            <div class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">{{ __('Permissions')
                                }}</div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{
                                $role->permissions->count() }}</div>
                        </div>
                    </div>

                    <!-- Users Preview (if any) -->
                    @if($role->users_count > 0)
                    <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Users with this
                                role') }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($role->users->take(6) as $user)
                            <div
                                class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 rounded-lg border border-blue-200 dark:border-blue-700">
                                <div
                                    class="h-6 w-6 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-xs text-white font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $user->name
                                    }}</span>
                            </div>
                            @endforeach
                            @if($role->users_count > 6)
                            <div
                                class="flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">+{{
                                    $role->users_count - 6 }} more</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Permissions -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="h-10 w-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Permissions') }}</h3>
                    </div>

                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('Select the permissions that this role should
                        have') }}</p>

                    @if($permissions->count() > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($permissions as $permission)
                            <div
                                class="relative flex items-start p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-emerald-300 dark:hover:border-emerald-600 transition-colors">
                                <div class="flex h-5 items-center">
                                    <input wire:model="selectedPermissions" type="checkbox"
                                        value="{{ $permission->name }}" id="permission_{{ $permission->id }}"
                                        class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="permission_{{ $permission->id }}"
                                        class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                        {{ $permission->name }}
                                    </label>
                                    @if($permission->guard_name)
                                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ $permission->guard_name
                                        }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-12 text-center">
                        <div
                            class="mx-auto h-16 w-16 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('No permissions
                            available') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('Create permissions first before assigning
                            them to roles.') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('role.index') }}"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200"
                        wire:navigate>
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        {{ __('Update Role') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>