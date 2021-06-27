@props(['title' => 'Salud Vital Basada en la Evidencia'])

<!DOCTYPE html>
<html lang="{{ app()->currentLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>{{ 'Sofrosine - ' . $title }}</title>

    @livewireStyles
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @stack('head-script')
  </head>
  <body>

    {{ $slot }}

    @livewireScripts
    <script src="{{ mix('/js/app.js') }}"></script>
    @stack('foot-script')
</body>
</html>
