<div>
    Mostrando artículos
    <ul>
        @foreach ($articles as $article)
            <li class=" list-disc ml-2">{{$article->title}}</li>
        @endforeach
    </ul>
    <div class="bg-gray-100 rounded px-1 py-1 mt-2 border-t">
        {{ $articles->links() }}
    </div>


</div>
