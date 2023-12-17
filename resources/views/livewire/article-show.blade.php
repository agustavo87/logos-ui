<div class="article-show">
    <div class="data">
        <h1 class="title">
            {{$this->article->title}}
        </h1>
        <div class="author">
            <strong>Author:</strong> {{$this->article->user->name}}
        </div>
    </div>
    <article>
        {!!$this->article->html!!}
    </article>
    <footer>
        <div class="max-w-screen-md mx-auto mt-t mb-12">
            <h1 class="text-xl font-bold text-gray-700 mb-2">References</h1>
            <div class="text-sm">
                <livewire:document-citations :article-id="$articleId" li-class="py-2" />
            </div>
        </div>
    </footer>
    @push('head-script')
    <style>
        .article-show {
            max-width: 800px;
            margin: 0 auto;
        }

        .article-show .data {
            margin: 5rem auto 2rem auto;
        }

        .article-show .data  .author {
            color: #555;
            margin-top:0.25rem;
        }

        .article-show .data h1.title {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1em;
        }

        .article-show h2 {
            font-size: 1.5rem;
            font-weight: 500;
            line-height: 1em;
            margin: 1rem auto;
        }

        .article-show h3 {
            font-size: 1rem;
            font-weight: 500;
            line-height: 1em;
            margin: 1rem auto;
        }

        .article-show footer {
            margin-top: 2rem;
        }

        .article-show footer h2 {
            margin-top: 2rem;
            font-size: 1.5rem;
            font-weight: 500;
        }

        .article-show footer .references li {
            font-size: 0.85rem;
            margin-left:1rem;
            text-indent: -1rem;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            inyectReferences({{ Illuminate\Support\Js::from($this->rendered_sources_list)}})
        })
    </script>

    @endpush
</div>
