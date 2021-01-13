@auth
    @if (auth()->user()->isAdministrator())
        <div class=" w-full h-1 bg-green-500 z-50 absolute top-0 left-0"></div>
    @endif
@endauth
<header
  class="bg-gray-900 sm:flex sm:justify-between sm:px-4 sm:py-2 sm:items-center"
  x-data="{ open: true }"
>
    <div
    class="flex justify-between items-center px-4 py-3 sm:p-0"
    >
        <!-- Logo -->
        <img class="h-8" src="/images/logo-cuadrado.png" alt="Mandala">
    
        <!-- xs Menú Button -->
        <div class="sm:hidden" >
            <button @click="open = !open" class="block h-6 w-6 text-white opacity-75 hover:opacity-100 focus:opacity-100 focus:outline-none">
            <svg x-show="!open" lass="h-6 w-6 fill-current" x-description="Heroicon name: menu" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg x-show="open" class="h-6 w-6 fill-current" x-description="Heroicon name: x" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            </button> 
        </div>

    </div>

    <nav class="p-1 hidden sm:block" :class="{'hidden':!open}" >
        <div class="text-sm px-2 pt-2 pb-4 sm:flex sm:items-center sm:p-0">
            
            {{-- Nav Items --}}
            {{ $slot }}

            {{-- Locale Menu --}}
            <x-dropdown>
                <x-slot name="caption">
                    <div class="flex justify-between text-white py-1 mx-auto" >
                        <span class="sm:hidden">{{__('messages.header.links.lang')}}</span>
                        <svg class="fill-current h-4 w-4 sm:mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M7 2a1 1 0 011 1v1h3a1 1 0 110 2H9.578a18.87 18.87 0 01-1.724 4.78c.29.354.596.696.914 1.026a1 1 0 11-1.44 1.389c-.188-.196-.373-.396-.554-.6a19.098 19.098 0 01-3.107 3.567 1 1 0 01-1.334-1.49 17.087 17.087 0 003.13-3.733 18.992 18.992 0 01-1.487-2.494 1 1 0 111.79-.89c.234.47.489.928.764 1.372.417-.934.752-1.913.997-2.927H3a1 1 0 110-2h3V3a1 1 0 011-1zm6 6a1 1 0 01.894.553l2.991 5.982a.869.869 0 01.02.037l.99 1.98a1 1 0 11-1.79.895L15.383 16h-4.764l-.724 1.447a1 1 0 11-1.788-.894l.99-1.98.019-.038 2.99-5.982A1 1 0 0113 8zm-1.382 6h2.764L13 11.236 11.618 14z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </x-slot>
                    @foreach ($languages['supported'] as $language)
                        
                        @if ($language === $currentLanguage) 
                        <x-dropdown-item  disabled button >
                            {{ $languages['names'][$language] }}
                        </x-dropdown-item>
                        @else 
                        <x-dropdown-item   @click="changeLanguage('{{$language}}')"  button  >
                            {{ $languages['names'][$language] }}
                        </x-dropdown-item>
                        @endif 
                        

                    @endforeach
                </form>

{{--     
                <x-dropdown-item disabled>
                    ES
                </x-dropdown-item>
                <x-dropdown-item href="#">
                    EN
                </x-dropdown-item>
     --}}
            </x-dropdown>

            {{-- Account Menu Dropdown sm+  --}}
            <x-header.account-dropdown class="hidden sm:block sm:ml-6"/>

        </div>

        {{-- Account Menu sm- --}}
        <div class="px-4 py-5 border-t border-gray-800 sm:hidden">
            
            {{-- Imagen de perfil --}}
            <div class="flex items-center">
                <img src="{{ asset('images/panther-profile.png') }}" alt="Gustavo" class="h-8 w-8 rounded-full border-2 border-gray-600 object-cover">
                <span class="ml-3 text-white font-semibold">
                    @auth
                        {{auth()->user()->name}}
                    @endauth

                    @guest
                        {{ ucfirst(__('messages.guest')) }}
                    @endguest
                </span>
            </div>

            {{-- Items --}}
            <div class="mt-4 text-sm">
                @guest
                <x-header.account-mobile-item href="{{ route('auth.show') }}" class="mt-3">
                    {{__('messages.header.account.start')}}
                </x-header.account-mobile-item>
                <x-header.account-mobile-item href="{{ route('users.create') }}">
                    {{__('messages.header.account.register')}}
                </x-header.account-mobile-item>
                @endguest
                {{-- Poner de hecho el usuario logeado cuando se establezca --}}
                @auth
                <x-header.account-mobile-item href="{{ route('users.show', auth()->user()->id) }}">
                    {{__('messages.header.account.profile')}}
                </x-header.account-mobile-item>
                <x-header.account-mobile-item href="{{ route('users.edit', auth()->user()->id) }}">
                    {{__('messages.header.account.edit-profile')}}
                </x-header.account-mobile-item>
                <x-header.account-mobile-item href="{{ route('auth.logout') }}">
                    {{__('messages.header.account.logout')}}
                </x-header.account-mobile-item>
                @endauth
            </div>


        </div>



    </nav>

</header>

@push('foot-script')
<script>
    function changeLanguage(lang) {
        axios.put('/locale', {language: lang})
            .then(response => {
                if (Number(response.status) === 200) {
                    window.location = response.data.redirect;
                }
            })
            .catch(e => console.log(e.response));
    }
</script>
@endpush

