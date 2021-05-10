<div>
    <div class="shadow overflow-hidden rounded border-b border-gray-200">
        <table class=" table-fixed min-w-full bg-white stripped">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="text-left py-3 px-4 w-2 uppercase font-semibold text-sm">id</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm" >{{ __('messages.users.name') }}</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm" >{{ __('messages.users.mail') }}</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm" >{{ __('messages.users.lang') }}</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm" >{{ __('messages.users.country') }}</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm" > {{ ucfirst(__('ui.options')) }} </th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach ($users as $user)
                <tr x-data="{user: {{ $user->id }}}">
                    <td class="text-left py-3 px-4"> {{ $user->id }}</td>
                    <td class="text-left py-3 px-4"> {{ $user->name }}</td>
                    <td class="text-left py-3 px-4">{{ $user->email }}</td>
                    <td class="text-left py-3 px-4">{{ $languages['names'][$user->language] }}</td>
                    <td class="text-left py-3 px-4">{{ $countries[$user->country]['name'] }}</td>
                    <td class="text-left py-3 px-4" >
                        <div class="flex flex-row justify-center align-middle">
                            <x-link button 
                            active="" class="hover:text-red-500 focus:outline-none ml-1" base="" 
                            
                            x-on:click="
                                $dispatch('show-alert', {
                                    name: 'user-delete',
                                    accept: () => $wire.destroy(user)
                                })
                            "> 
                                <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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
            {{ $users->links() }}
        </div>
    </div>
    <div wire:loading>
        <div   class=" flex items-center content-center mt-1" >
            <span class="ring-loader-xs"></span>
            <span class=" ml-2 text-xs text-gray-600">{{ ucfirst(__('ui.processing')) }}...</span>
        </div>
    </div>

<x-modal.alert name="user-delete" title="{{ ucfirst(__('messages.users.delete')) }}">
    {{ __('messages.users.delete-warn') }}
</x-modal.alert>



</div>
    