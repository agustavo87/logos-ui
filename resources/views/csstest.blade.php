<x-layout.master
  title="Logos"
>
@push('head-script')
    <style>
      #main-container {
        position: relative;
        top: 2rem;
        width:auto;
        height: 300px;
        margin: 2rem;
        border: 1px solid gray;
      }
      #tooltip {
        position: absolute;
        background-color: blueviolet;
        border-radius: 5px;
        padding: 5px;
        color: lightblue;
        top: 60px;
        left:-50px;
      }
    </style>
@endpush
<div id="main-container">
  <div id="tooltip">Un tooltip</div>
</div>
</x-layout.master>