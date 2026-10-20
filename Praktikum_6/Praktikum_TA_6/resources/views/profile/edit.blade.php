<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil Petugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Informasi Akun Petugas') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Data akun petugas yang sedang login.') }}
                    </p>

                    <dl class="mt-4 grid gap-2 text-sm sm:grid-cols-3">
                        <dt class="font-medium text-gray-700">{{ __('Nama') }}</dt>
                        <dd class="text-gray-900 sm:col-span-2">{{ Auth::user()->name }}</dd>

                        <dt class="font-medium text-gray-700">{{ __('Email') }}</dt>
                        <dd class="text-gray-900 sm:col-span-2">{{ Auth::user()->email }}</dd>

                        <dt class="font-medium text-gray-700">{{ __('Akun Dibuat') }}</dt>
                        <dd class="text-gray-900 sm:col-span-2">
                            {{ Auth::user()->created_at->format('d-m-Y H:i') }}
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
