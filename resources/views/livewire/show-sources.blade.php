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
<div class="md:px-32 py-8 w-full" x-data="sources()" @keydown.escape="hideModal">
    <div class="shadow overflow-hidden rounded border-b border-gray-200">
        <table class=" table-fixed min-w-full bg-white stripped">
            <thead class="bg-gray-800 text-white">
                <tr>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Clave</th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm" >Año</th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Título</th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Editorial</th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Ciudad</th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm"></th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach ($sources as $source)
                <tr>
                    <td class="text-left py-3 px-4"> {{ $source->key }}</td>
                    <td class="text-left py-3 px-4">{{ $source->data['year'] }}</td>
                    <td class="text-left py-3 px-4"> {{ $source->data['title'] }}</td>
                    <td class="text-left py-3 px-4">{{ $source->data['editorial'] }}</td>
                    <td class="text-left py-3 px-4">{{ $source->data['city'] }}</td>
                    <td class="text-left py-3 px-4">
                        <div class="flex flex-col">
                            <x-link base="" active="" class="hover:text-gray-500 pb-3" href="{{ route('sources.edit', ['source' => $source->id]) }}">
                                <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                  </svg>
                            </x-link>
                            <x-link button @click="showModal" base="" active="" class="hover:text-gray-500 focus:outline-none">
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

  <!-- This example requires Tailwind CSS v2.0+ -->
<div class="fixed z-10 inset-0 overflow-y-auto" x-show="modal" x-cloak


>
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!--
        Background overlay, show/hide based on modal state.
  
        Entering: "ease-out duration-300"
          From: "opacity-0"
          To: "opacity-100"
        Leaving: "ease-in duration-200"
          From: "opacity-100"
          To: "opacity-0"
      -->
      <div class="fixed inset-0 transition-opacity " x-show="modal" aria-hidden="true"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
      >
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
      </div>
  
      <!-- This element is to trick the browser into centering the modal contents. -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      <!--
        Modal panel, show/hide based on modal state.
  
        Entering: "ease-out duration-300"
          From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          To: "opacity-100 translate-y-0 sm:scale-100"
        Leaving: "ease-in duration-200"
          From: "opacity-100 translate-y-0 sm:scale-100"
          To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      -->
      <div x-show="modal" @click.away="hideModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      >
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <!-- Heroicon name: exclamation -->
              <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                Deactivate account
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Are you sure you want to deactivate your account? All of your data will be permanently removed. This action cannot be undone.
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
            Deactivate
          </button>
          <button @click="hideModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>

  @push('head-script')
    <script>  
    function sources() {
        return {
            modal: false,
            showModal: function () {
                this.modal = true;
            },
            hideModal: function () {
                this.modal = false;
            }
        }
    }
    
    </script>
  @endpush

</div>
