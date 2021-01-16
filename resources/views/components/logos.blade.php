
@push('head-script')
    <link rel="stylesheet" href="{{ asset('css/logos.css') }}">
    <style>
      
    </style>
@endpush

@push('foot-script')
<script src="{{ asset('js/logos.js') }}"></script>

<script>
  var quill = new Quill('#quill-container',{
      modules: {
          toolbar: ['bold', 'italic', {'header':2}]
      },
      theme:'snow'
  });
</script>
@endpush

<div {{ $attributes }} id="quill-wrapp">
    <div id="quill-container"></div>
</div>

