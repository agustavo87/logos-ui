@props([
    'name' => 'modal',
    'title' => ucfirst(__('ui.warn')),
    'acceptCaption' => ucfirst(__('ui.delete')),
    'cancelCaption' => ucfirst(__('ui.cancel'))
    ])

{{-- This example requires Tailwind CSS v2.0+ --}}
<div x-data="modal" class="fixed z-10 inset-0 overflow-y-auto" x-show="modal" @keyup.escape.window="cancel" x-cloak
    x-on:show-alert.window="externalCall"
>
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

      {{-- Background overlay --}}
      <div class="fixed inset-0" x-show="modal" aria-hidden="true"
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

      <div x-show="modal"  @click.outside="cancel" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4 "
        x-transition:enter-end="opacity-100 translate-y-0 "
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 "
        x-transition:leave-end="opacity-0 -translate-y-4 "
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
                {{ $title }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                 {{ $slot }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button @click="accept" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
            {{ $acceptCaption }}
          </button>
          <button @click="cancel" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            {{ $cancelCaption }}
          </button>
        </div>
      </div>
    </div>
  </div>

  @push('head-script')
    <script>

document.addEventListener('alpine:init', () => {
        Alpine.data('modal', () => ({
            modal: false,
            name: '{{ $name }}',
            acceptCB: () => null, // console.log('aceptado'),
            cancelCB: () => null, //console.log('cancelado'),
            showModal: function (event, acceptCB =  null, cancelCB = null) {
              if (acceptCB) this.acceptCB = acceptCB;
              if (cancelCB) this.cancelCB = cancelCB;
            //   console.log(event);
            //   console.log(this.acceptCB);
            //   console.log(this.cancelCB);
              this.modal = true;
            },
            hideModal: function () {
                this.modal = false;
            },
            accept: function () {
              this.hideModal();
              this.acceptCB();
            },
            cancel: function () {
              this.hideModal();
              this.cancelCB();
            },
            externalCall: function (event) {
                if (!event.detail.name === this.name) return;
                // console.log('me llaman a mi!');
                // console.log(event.detail)
                this.showModal(
                    event,
                    event.detail.accept ? event.detail.accept : null,
                    event.detail.cancel ? event.detail.cancel : null
                );
            }
        }))
    })

    </script>
  @endpush
