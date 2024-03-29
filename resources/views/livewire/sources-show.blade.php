@push('head-script')
    <style>
        table.stripped tbody tr:nth-child(even) {
            background-color: #eee;
        }
        table.stripped tbody tr:nth-child(odd) {
            background-color: #fff;
        }
    </style>
@endpush
<div class="md:px-32  pt-4 pb-10 w-full">
    <div class="shadow overflow-hidden rounded border-b border-gray-200">
        <table class=" table-fixed min-w-full bg-white stripped">
            <thead class="bg-gray-800 text-white">
                <tr>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm"> ID </th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm">{{ ucfirst(__('sources.key')) }}</th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm"> TIPO </th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm" > Representación </th>
                  {{--<th class="text-left py-3 px-4 uppercase font-semibold text-sm" >{{ ucfirst(__('sources.year')) }}</th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm">{{ ucfirst(__('sources.title')) }}</th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm">{{ ucfirst(__('sources.editorial')) }}</th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm">{{ ucfirst(__('sources.city')) }}</th> --}}
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm">{{ ucfirst(__('ui.options')) }}</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach ($sources as $source)
                <tr x-data="{source: {{ $source->id }}}">
                    <td class="text-left py-3 px-4"> {{ $source->id }}</td>
                    <td class="text-left py-3 px-4"> {{ $source->key }}</td>
                    {{-- <td class="text-left py-3 px-4"> {{ $source->name() }}</td> --}}
                    {{-- <td class="text-left text-sm py-3 px-4"> {{ $source->render() }}</td> --}}
                    {{-- <td class="text-left py-3 px-4">{{ $source->data['year'] }}</td>
                    <td class="text-left py-3 px-4"> {{ $source->data['title'] }}</td>
                    <td class="text-left py-3 px-4">{{ $source->data['editorial'] }}</td>
                    <td class="text-left py-3 px-4">{{ $source->data['city'] }}</td> --}}
                    <td class="text-left py-3 px-4" >
                        <div class="flex flex-row justify-center align-middle">
                            <x-link base="" active="" class="hover:text-blue-500 " href="{{ route('sources.edit', ['source' => $source->id]) }}">
                                <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                  </svg>
                            </x-link>
                            <x-link button 
                            active="" class="hover:text-red-500 focus:outline-none ml-1" base="" 
                           
                            x-on:click="
                              $dispatch( 'show-alert', {
                                name: 'source-delete',
                                accept: () => $wire.destroy(source)
                              })
                            "> 
                                <svg class="h-5 w-5 fill-current"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </x-link>
                        </div>
                        {{-- <button click="console.log('borrar {{$source->id}}')">x</button> --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="rounded-b px-1 py-1 border-t ">
            {{ $sources->links() }}
        </div>
    </div>
    <div wire:loading>
        <div   class=" flex items-center content-center mt-1" >
            <span class="ring-loader-xs"></span>
            <span class=" ml-2 text-xs text-gray-600">Procesando...</span>
        </div>
    </div>

<x-modal.alert name="source-delete" title="{{ ucfirst(__('sources.delete')) }}">
  {{ __('sources.delete-warn') }}
</x-modal.alert>

</div>
