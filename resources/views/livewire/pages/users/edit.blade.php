<?php

use Livewire\Volt\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;

new class extends Component {
    #[Layout('components.layouts.app')]

    public $userId;
    public $name = '';
    public $email = '';
    public $status = 'active';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRoles = [];

    public function mount($id)
    {
        $this->userId = $id;
        $user = User::with(['roles'])->findOrFail($id);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->status = $user->status;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
    }

    public function save()
    {
                $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->userId,
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($this->userId);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
        ]);

        if ($this->password) {
            $user->update(['password' => bcrypt($this->password)]);
        }

        // Sync roles
        $user->syncRoles($this->selectedRoles);

        session()->flash('message', 'User updated successfully.');
        return $this->redirect(route('user.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'user' => User::with(['roles'])->withCount('roles')->findOrFail($this->userId),
            'roles' => Role::withCount('users')->orderBy('name')->get()
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
                        {{ __('Edit User') }}
                    </h1>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                        {{ __('Update user information and roles') }}
                    </p>
                </div>
                <a href="{{ route('user.index') }}"
                    class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                    wire:navigate>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to Users') }}
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

        <!-- Current User Information -->
        <div
            class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800 mb-8">
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Current User Information') }}
                </h4>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                    <div class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">{{ __('Created') }}</div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->created_at->format('M d,
                        Y H:i') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                    <div class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">{{ __('Updated') }}</div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->updated_at->format('M d,
                        Y H:i') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                    <div class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">{{ __('Roles') }}</div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->roles_count }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-blue-200 dark:border-blue-700">
                    <div class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">{{ __('Status') }}</div>
                    <div
                        class="text-sm font-semibold {{ $user->status === 'active' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                        {{ $user->status === 'active' ? __('Active') : __('Inactive') }}
                    </div>
                </div>
            </div>

            <!-- Current Roles Preview -->
            @if($user->roles_count > 0)
            <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Current Roles') }}</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($user->roles as $role)
                    <div
                        class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 rounded-lg border border-blue-200 dark:border-blue-700">
                        <div
                            class="h-5 w-5 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-xs text-white font-semibold">
                            {{ strtoupper(substr($role->name, 0, 1)) }}
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $role->name }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

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
                                {{ __('Full Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="name" type="text" id="name"
                                class="block w-full px-4 py-3 border-0 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-sm"
                                placeholder="{{ __('Enter full name') }}">
                            @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                {{ __('Email Address') }} <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="email" type="email" id="email"
                                class="block w-full px-4 py-3 border-0 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-sm"
                                placeholder="{{ __('Enter email address') }}">
                            @error('email')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                {{ __('Status') }} <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="status" id="status"
                                class="block w-full px-4 py-3 border-0 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-sm">
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                            @error('status')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Password (Optional) -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="h-10 w-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Password') }}</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">({{ __('Leave blank to keep current
                            password') }})</span>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                {{ __('New Password') }}
                            </label>
                            <input wire:model="password" type="password" id="password"
                                class="block w-full px-4 py-3 border-0 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-sm"
                                placeholder="{{ __('Enter new password') }}">
                            @error('password')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                {{ __('Confirm New Password') }}
                            </label>
                            <input wire:model="password_confirmation" type="password" id="password_confirmation"
                                class="block w-full px-4 py-3 border-0 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-sm"
                                placeholder="{{ __('Confirm new password') }}">
                        </div>
                    </div>
                </div>

                <!-- Role Assignment -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="h-10 w-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Role Assignment') }}</h3>
                    </div>

                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('Select the roles that this user should
                        have') }}</p>

                    @if($roles->count() > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($roles as $role)
                            <div
                                class="relative flex items-start p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-emerald-300 dark:hover:border-emerald-600 transition-colors">
                                <div class="flex h-5 items-center">
                                    <input wire:model="selectedRoles" type="checkbox" value="{{ $role->name }}"
                                        id="role_{{ $role->id }}"
                                        class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="role_{{ $role->id }}"
                                        class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                        {{ $role->name }}
                                    </label>
                                    @if($role->users_count > 0)
                                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ $role->users_count }}
                                        users</p>
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
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('No roles available')
                            }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('Create roles first before assigning them to
                            users.') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('user.index') }}"
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
                        {{ __('Update User') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>