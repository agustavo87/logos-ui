@push('head-script')
    <style>
      .logos-container {
        min-height: 500px;
        margin-bottom: 50px;
      }
    </style>

@endpush
<x-layout.default
  title="Logos"
>
  <x-container>
    <form action="/logos" method="POST">
    <div class="flex flex-col mx-auto logos-container mt-3 max-w-screen-lg">
      <input type="text" name="title" id="title" placeholder="Título" class="ml-2 text-3xl font-bold mb-2 focus:outline-none text-gray-800">
      <x-logos  />
    </div>
    </form>
  </x-container>
</x-layout.default>
