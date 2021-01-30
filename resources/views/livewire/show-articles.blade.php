<div class="h-96 flex flex-col justify-between">
    {{ __('messages.test') }}
    <ul>
        @foreach ($articles as $article)
            <li class=" list-disc ml-2">{{$article->title}} <x-link href="{{ route('articles.show', ['article' => $article->id]) }}">Ver</x-link> </li>
        @endforeach
    </ul>
    <div class="bg-gray-100 rounded px-1 py-1 mt-2 border-t ">
        {{ $articles->links() }}
    </div>
</div>
