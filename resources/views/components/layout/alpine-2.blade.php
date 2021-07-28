@props(['title' => "Salud Mental Basada en la Evidencia"])

@php
    $currentLocale = app()->currentLocale()
@endphp

<!DOCTYPE html>
<html lang="{{ $currentLocale }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>{{ 'Sofrosine - ' . $title }}</title>

    @livewireStyles
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

    @stack('head-script')
  </head>
  <body>

    {{ $slot }}

    @livewireScripts
    <script src=" {{ mix('js/prototypes.js') }} "></script>
    @stack('foot-script')
</body>
</html>
