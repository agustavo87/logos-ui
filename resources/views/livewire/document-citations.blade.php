<ol data-article-id="{{$articleId}}" class="list-decimal">
    @forelse ($this->sourceList as $source)
        <li id="ref-{{$source->key()}}" class="{{$liClass}}">{{$source->render()}}</li>
    @empty
        No sources yet..
    @endforelse
</ol>
