<div class="show-article">
    <div class="data">
        <h1 class="title">
            {{$article->title}}
        </h1>
        <div class="author">
            <strong>Author:</strong> {{$article->user->name}}
        </div>
    </div>
    <article>
        {!!$article->html!!}
    </article>
    <footer>
        @if ($article->sources->count())
        <h2>Referencias</h2>
        <ul class="references">
            @foreach ($article->sources as $source)
            @php
                $data = (object) $source->data;
                $creators = $source->creators;

                $creatorsSection = '';
                foreach ($creators as $key => $creator) {
                    $creatorsSection .=  ($key === 0 ? '' : ', ') . $creator->data['last_name'] .', ' . strtoupper($creator->data['name'][0]);
                }
            @endphp
                <li>{!!" $creatorsSection ($data->year). <i>{$data->title}</i> {$data->city}: {$data->editorial}. "!!}</li>
            @endforeach
        </ul>
        @endif
    </footer>
    @push('head-script')
    <style>
        .show-article {
            max-width: 800px;
            margin: 0 auto;
        }

        .show-article .data {
            margin: 5rem auto 2rem auto;
        }

        .show-article .data  .author {
            color: #555;
            margin-top:0.25rem;
        }

        .show-article .data h1.title {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1em;
        }

        .show-article h2 {
            font-size: 1.5rem;
            font-weight: 500;
            line-height: 1em;
            margin-bottom: 1rem;
        }

        .show-article footer {
            margin-top: 2rem;
        }
        
        .show-article footer h2 {
            margin-top: 2rem;
            font-size: 1.5rem;
            font-weight: 500;
        }

        .show-article footer .references li {
            font-size: 0.85rem;
            margin-left:1rem;
            text-indent: -1rem;
        }
    </style>
        
    @endpush
</div>
