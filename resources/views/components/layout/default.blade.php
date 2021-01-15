@props(['title'])
<x-layout.master :title=$title>
    <x-top-header>
        <x-header.nav-item href="{{ route('landing') }}" >{{ __('messages.header.links.portada')}}</x-header.nav-item>
        <x-header.nav-item href="{{ route('home') }}">{{ __('messages.header.links.inicio')}}</x-header.nav-item>
        <x-header.nav-item href="{{ route('logos') }}">Logos</x-header.nav-item>
    </x-top-header>
        
    {{ $slot }}

</x-layout.master>