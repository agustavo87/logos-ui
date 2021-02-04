{{-- <div class="h-96 flex flex-col justify-between"> --}}
<div>

    {{-- <ul>
        @foreach ($articles as $article)
            <li class=" list-disc ml-2">{{$article->title}} <x-link href="{{ route('articles.show', ['article' => $article->id]) }}">Ver</x-link> </li>
        @endforeach
    </ul>
    <div class="bg-gray-100 rounded px-1 py-1 mt-2 border-t ">
        {{ $articles->links() }}
    </div> --}}

    <div class="shadow overflow-hidden rounded border-b border-gray-200">
        <table class=" table-fixed min-w-full bg-white stripped">
            <thead class="bg-gray-800 text-white">
                <tr>
                  <th class="text-left py-3 px-4 w-2 uppercase font-semibold text-sm">id</th>
                  <th class="text-left py-3 px-4 uppercase font-semibold text-sm" >{{ __('articles.title') }}</th>
                  <th class="text-center py-3 px-4 uppercase font-semibold text-sm" > {{ ucfirst(__('ui.options')) }} </th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach ($articles as $article)
                <tr x-data="{article: {{ $article->id }}}">
                    <td class="text-left py-3 px-4"> {{ $article->id }}</td>
                    <td class="text-left py-3 px-4">{{ $article->title }}</td>
                    <td class="text-left py-3 px-4" >
                        <div class="flex flex-row justify-center align-middle">
                            <x-link base="" active=""  class="hover:text-blue-500 pb-3 h-1" href="{{ route('articles.show', ['article' => $article->id]) }}">
                                <svg class="h-5 w-5 fill-current"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </x-link>
                            <x-link base="" active="" class="hover:text-blue-500 pb-3 h-1 ml-1" href="{{ route('logos', ['article' => $article->id]) }}">
                                <svg class="h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                  </svg>
                            </x-link>
                            <x-link button 
                            active="" class="hover:text-red-500 focus:outline-none ml-1" base="" 
                           
                            x-on:click="
                              $dispatch( 'show-alert', {
                                name: 'article-delete',
                                accept: () => $wire.destroy(article)
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
            {{ $articles->links() }}
        </div>
    </div>
    <div wire:loading>
        <div   class=" flex items-center content-center mt-1" >
            <span class="ring-loader-xs"></span>
            <span class=" ml-2 text-xs text-gray-600">{{ ucfirst(__('ui.processing')) }}...</span>
        </div>
    </div>

<x-modal.alert name="article-delete" title="{{ ucfirst(__('articles.delete')) }}">
    {{ __('articles.delete-warn') }}
</x-modal.alert>



</div>
