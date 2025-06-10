<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="username">Username</label>
            <input id="username" type="text" class="form-control" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="name">Name</label>
            <input id="name" type="text" class="form-control" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" class="form-control" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" class="form-control" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
        </div>
        

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
