@php
    $current =  url()->current() == url($attributes->get('href'));
    $activeStyle =  'text-white opacity-75 cursor-pointer hover:bg-gray-800 hover:opacity-100 active:opacity-100 active:bg-gray-700';
    $inactiveStyle = 'text-white' ;
    $cClass = $current ? $inactiveStyle: $activeStyle;
    $cClass .= ' '. $attributes->get('class');
    $filteredAttrs = $current
      ? $attributes->filter(fn ($value, $key) => ($key != 'href' && $key != 'class')  )
      : $attributes;

@endphp
<a 
  class="block px-2 py-1 mt-1 sm:mt-0 sm:ml-2 font-semibold rounded {{ $cClass }}"
  {{ $filteredAttrs }}
>
  {{ $slot }}
</a>