
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
          toolbar: '#toolbar'
        //   toolbar: ['bold', 'italic', {'header':2}]
      },
      theme:'bubble',
      placeholder: "Escribe algo épico..."
  });
</script>
@endpush

<div id="toolbar">
    <span class="ql-formats">
        <select class="ql-header">
            <option value="2"></option>
            <option value="3"></option>
            <option selected></option>
        </select>
    </span>
    <span class="ql-formats">
        <button class="ql-bold"></button>
        <button class="ql-italic"></button>
    </span>
</div>

<div {{ $attributes }} id="quill-wrapp">
    <div id="quill-container"></div>
</div>

