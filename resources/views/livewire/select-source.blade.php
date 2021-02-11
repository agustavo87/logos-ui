<div
    x-data="modalCitation()" 
    x-on:{{ $listen }}.window="handleInvocation"
    x-show="display"
  >
  <p class="p-4">Elige una fuente</p>
  <button @click="solve" class="border p-3 m-1">Retornar</button>
  <script>
    function modalCitation() {
      return {
        key: null, 
        display: false,
        respond: null,
        handleInvocation: function (e) {
          this.display = true;
          console.log('tetaaaa!');
          this.respond = e.detail.resolve
        },
        solve: function () {
          this.display= false;
          this.respond('te retorno esta');
        }
      }
    }
  </script>

</div>