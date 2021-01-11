<form
class="mx-auto max-w-sm rounded-lg bg-gray-100 p-4 flex flex-col justify-start shadow-lg"
{{ $attributes }}
>
  @csrf
  {{ $slot }}
</form>