@props(['title'])
<x-layout.master :title=$title>
    <x-top-header>
        <x-header.nav-item href="{{ route('landing') }}" >{{ __('messages.header.links.portada')}}</x-header.nav-item>
        <x-header.nav-item href="{{ route('home') }}">{{ __('messages.header.links.inicio')}}</x-header.nav-item>
        @auth
        <x-dropdown>
            <x-slot name="caption">
                {{ __('articles.articles') }}
            </x-slot>
                <x-dropdown-item href="{{ route('logos') }}" >
                    Logos
                </x-dropdown-item>
                <x-dropdown-item href="{{ route('articles.mine') }}">
                    {{ __('articles.my') }}
                </x-dropdown-item>
                <x-dropdown-item href="{{ route('sources.index') }}">
                    Fuentes
                </x-dropdown-item>
            </form>
        </x-dropdown>
        @endauth
    </x-top-header>
        
    {{ $slot }} 

</x-layout.master>