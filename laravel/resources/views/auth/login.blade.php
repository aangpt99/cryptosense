<x-guest-layout>

    <form method="POST" action="{{ route('login') }}" class="mt-2">
        @csrf

        <div class="max-w-sm mx-auto space-y-6">

            <!-- Email -->
            <div>
                <x-input-label 
                    for="email" 
                    :value="__('Email')" 
                    class="text-sm font-medium text-gray-600"
                />

                <x-text-input 
                    id="email"
                    name="email"
                    type="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    class="mt-2 block w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-black focus:ring-black"
                    placeholder="you@example.com"
                />

                <x-input-error 
                    :messages="$errors->get('email')" 
                    class="mt-2 text-sm" 
                />
            </div>

            <!-- Password -->
            <div>
                <x-input-label 
                    for="password" 
                    :value="__('Password')" 
                    class="text-sm font-medium text-gray-600"
                />

                <x-text-input 
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="mt-2 block w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-black focus:ring-black"
                    placeholder="••••••••"
                />

                <x-input-error 
                    :messages="$errors->get('password')" 
                    class="mt-2 text-sm" 
                />
            </div>

            <!-- Remember -->
            <div class="flex items-center">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input 
                        type="checkbox" 
                        name="remember"
                        class="rounded border-gray-300 text-black focus:ring-black"
                    >
                    Remember me
                </label>
            </div>

            <!-- Button -->
            <div class="pt-4 flex justify-center">

                <button 
                    type="submit"
                    style="
                        background: #0f172a;
                        color: white;
                        padding: 14px 34px;
                        border-radius: 16px;
                        font-size: 15px;
                        font-weight: 600;
                        border: none;
                        cursor: pointer;
                        box-shadow: 0 8px 20px rgba(15,23,42,.12);
                        transition: all .25s ease;
                    "
                    onmouseover="this.style.background='#111827'; this.style.transform='translateY(-1px)'"
                    onmouseout="this.style.background='#0f172a'; this.style.transform='translateY(0)'"
                >
                    Log In
                </button>

            </div>

        </div>

    </form>

</x-guest-layout>