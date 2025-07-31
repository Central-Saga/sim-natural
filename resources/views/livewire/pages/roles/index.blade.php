<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;

new class extends Component {
    use WithPagination;

    #[Layout('components.layouts.app')]

    public $search = '';
    public $showDeleteModal = false;
    public $roleToDelete = null;
    public $expandedPermissions = [];
    public $expandedUsers = [];

    public function deleteRole($roleId)
    {
        try {
            $role = Role::findOrFail($roleId);

            // Check if role has users
            if ($role->users()->count() > 0) {
                session()->flash('error', 'Cannot delete role with assigned users.');
                return;
            }

            $role->delete();
            session()->flash('message', 'Role deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete role.');
        }

        $this->showDeleteModal = false;
        $this->roleToDelete = null;
    }

    public function confirmDelete($roleId)
    {
        $this->roleToDelete = Role::findOrFail($roleId);
        $this->showDeleteModal = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function togglePermissions($roleId)
    {
        if (in_array($roleId, $this->expandedPermissions)) {
            $this->expandedPermissions = array_diff($this->expandedPermissions, [$roleId]);
        } else {
            $this->expandedPermissions[] = $roleId;
        }
    }

    public function toggleUsers($roleId)
    {
        if (in_array($roleId, $this->expandedUsers)) {
            $this->expandedUsers = array_diff($this->expandedUsers, [$roleId]);
        } else {
            $this->expandedUsers[] = $roleId;
        }
    }

    public function with(): array
    {
        $query = Role::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return [
            'roles' => $query->with(['permissions', 'users'])->withCount(['permissions', 'users'])->orderBy('name')->paginate(10)
        ];
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1
                        class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                        {{ __('Role Management') }}
                    </h1>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                        {{ __('Manage user roles and permissions with ease') }}
                    </p>
                </div>
                <a href="{{ route('role.create') }}"
                    class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                    wire:navigate>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Create Role') }}
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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Roles') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $roles->total() }}</p>
                    </div>
                    <div
                        class="h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Active Roles') }}</p>
                        <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">{{
                            $roles->where('users_count', '>', 0)->count() }}</p>
                    </div>
                    <div
                        class="h-12 w-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Users') }}</p>
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-2">{{
                            $roles->sum('users_count') }}</p>
                    </div>
                    <div
                        class="h-12 w-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mb-8">
            <div class="relative max-w-md">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input wire:model.live="search" type="text"
                    class="block w-full pl-12 pr-4 py-3 border-0 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-lg"
                    placeholder="{{ __('Search roles...') }}">
            </div>
        </div>

        <!-- Roles Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($roles as $role)
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300 overflow-hidden group">
                <!-- Role Header -->
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            <div
                                class="h-12 w-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $role->name }}</h3>
                                @if($role->guard_name && $role->guard_name !== 'web')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 mt-1">
                                    {{ $role->guard_name }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('role.edit', $role->id) }}"
                                class="p-2 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors"
                                wire:navigate title="{{ __('Edit Role') }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </a>
                            @if(!$role->users_count || $role->users_count == 0)
                            <button wire:click="confirmDelete({{ $role->id }})"
                                class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                title="{{ __('Delete Role') }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                            @else
                            <span class="p-2 text-gray-400 cursor-not-allowed"
                                title="{{ __('Cannot delete role with assigned users') }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                            </span>
                            @endif
                        </div>
                    </div>
                    @if($role->description)
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($role->description, 80) }}
                    </p>
                    @endif
                </div>

                <!-- Role Stats -->
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $role->permissions_count
                                }}</div>
                            <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">{{ __('Permissions') }}
                            </div>
                        </div>
                        <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-xl">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $role->users_count ??
                                0 }}</div>
                            <div class="text-xs text-green-600 dark:text-green-400 font-medium">{{ __('Users') }}</div>
                        </div>
                    </div>

                    <!-- Permissions Preview -->
                    @if($role->permissions_count > 0)
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Permissions')
                                }}</span>
                            <button type="button" wire:click="togglePermissions({{ $role->id }})"
                                class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 px-2 py-1 rounded-lg transition-all duration-200">
                                <span class="text-xs font-medium">
                                    {{ in_array($role->id, $expandedPermissions) ? __('Hide') : __('Show All') }}
                                </span>
                                <svg class="h-3 w-3 transition-transform duration-200 {{ in_array($role->id, $expandedPermissions) ? 'rotate-180' : '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Preview Permissions (Always Visible) -->
                        <div class="flex flex-wrap gap-1 mb-2">
                            @foreach($role->permissions->take(3) as $permission)
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600">
                                {{ $permission->name }}
                            </span>
                            @endforeach
                            @if($role->permissions_count > 3)
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600">
                                +{{ $role->permissions_count - 3 }}
                            </span>
                            @endif
                        </div>

                        <!-- Expanded Permissions (Toggleable) -->
                        @if(in_array($role->id, $expandedPermissions))
                        <div
                            class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('All
                                    Permissions') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($role->permissions as $permission)
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800">
                                    {{ $permission->name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Users Preview -->
                    @if($role->users_count > 0)
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Users') }}</span>
                            @if($role->users_count > 4)
                            <button type="button" wire:click="toggleUsers({{ $role->id }})"
                                class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 px-2 py-1 rounded-lg transition-all duration-200">
                                <span class="text-xs font-medium">
                                    {{ in_array($role->id, $expandedUsers) ? __('Hide') : __('Show All') }}
                                </span>
                                <svg class="h-3 w-3 transition-transform duration-200 {{ in_array($role->id, $expandedUsers) ? 'rotate-180' : '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            @endif
                        </div>

                        <!-- Preview Users (Always Visible) -->
                        <div class="flex items-center space-x-2">
                            <div class="flex -space-x-2">
                                @foreach($role->users->take(4) as $user)
                                <div
                                    class="h-8 w-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-xs text-white font-semibold border-2 border-white dark:border-gray-800 shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                @endforeach
                                @if($role->users_count > 4)
                                <div
                                    class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-xs text-gray-700 dark:text-gray-300 font-semibold border-2 border-white dark:border-gray-800 shadow-sm">
                                    +{{ $role->users_count - 4 }}
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Expanded Users (Toggleable) -->
                        @if(in_array($role->id, $expandedUsers) && $role->users_count > 4)
                        <div
                            class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                    </path>
                                </svg>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('All Users')
                                    }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($role->users as $user)
                                <div
                                    class="flex items-center gap-2 p-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div
                                        class="h-6 w-6 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-xs text-white font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $user->name
                                        }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Created Date -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Created') }}</span>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{
                            $role->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                    <div
                        class="mx-auto h-24 w-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('No roles found') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('Get started by creating a new role.') }}</p>
                    <a href="{{ route('role.create') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                        wire:navigate>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        {{ __('Create Role') }}
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($roles->hasPages())
        <div class="mt-8 flex justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg px-6 py-4">
                {{ $roles->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if($showDeleteModal && $roleToDelete)
<div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity z-50"
    wire:click="showDeleteModal = false"></div>

<div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div
            class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
            <div class="sm:flex sm:items-start">
                <div
                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">
                        {{ __('Delete Role') }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Are you sure you want to delete the role') }} <strong
                                class="text-gray-900 dark:text-white">{{ $roleToDelete->name }}</strong>? {{ __('This
                            action cannot be undone.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                <button wire:click="deleteRole({{ $roleToDelete->id }})" type="button"
                    class="inline-flex w-full justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors sm:w-auto">
                    {{ __('Delete') }}
                </button>
                <button wire:click="showDeleteModal = false" type="button"
                    class="mt-3 inline-flex w-full justify-center rounded-xl bg-gray-100 dark:bg-gray-700 px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-white shadow-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors sm:mt-0 sm:w-auto">
                    {{ __('Cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endif