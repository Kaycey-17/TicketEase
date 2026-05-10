<x-app-layout>
    <x-slot name="header">
        Profile
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Account Overview --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                        @php
                            $roleColors = [
                                'admin'         => 'bg-purple-100 text-purple-800',
                                'supervisor'    => 'bg-blue-100 text-blue-800',
                                'support_agent' => 'bg-green-100 text-green-800',
                                'requester'     => 'bg-gray-100 text-gray-800',
                            ];
                            $roleLabels = [
                                'admin'         => 'Administrator',
                                'supervisor'    => 'Supervisor',
                                'support_agent' => 'Support Agent',
                                'requester'     => 'Requester',
                            ];
                        @endphp
                        <span class="mt-1 px-2 py-1 text-xs rounded-full {{ $roleColors[auth()->user()->role] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $roleLabels[auth()->user()->role] ?? auth()->user()->role }}
                        </span>
                    </div>
                </div>

                <dl class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ auth()->user()->created_at->format('M d, Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ auth()->user()->updated_at->format('M d, Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                Active
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Existing Breeze profile form sections go here --}}
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')

        </div>
    </div>
</x-app-layout>