<x-layout.default
title="Crear Artículo"
>
<x-container>
  <x-main-heading>
    Crear Artículo
  </x-main-heading>


  <x-form.md name="articulo">
    <x-form.field name="title" label="Título" type="text" placeholder="Título" required />
    <textarea name="html" class="my-3 border border-gray-200 text-sm font-mono h-48 p-3 focus:outline-none">
<h1>Mi primer artículo</h1>
<p>Acá va un párrafo </p>
    </textarea>
    <textarea name="delta" class="my-3 border border-gray-200 text-sm font-mono h-48 p-3 focus:outline-none ">
{
    "ops": [
        {
            "insert": "hola"
        }
    ]
}
    </textarea>
    <textarea name="meta" class="my-3 border border-gray-200 text-sm font-mono h-48 p-3 focus:outline-none">
{
    "sources": ["gus2020", "pedro2019", "maría1987"]
}
    </textarea>
    <div class="flex justify-end">
      <x-form.button name="Enviar" type="button" class="m-2">Enviar</x-form.button>
      <x-form.button type="reset" class="m-2">Borrar</x-form.button>
    </div>
  </x-form.md>
</x-container>

@push('foot-script')
<script>


    function showAxiosError(error) {
      if (error.response) {
      // The request was made and the server responded with a status code
      // that falls out of the range of 2xx
      console.log(error.response.data);
      console.log(error.response.status);
      console.log(error.response.headers);
      } else if (error.request) {
      // The request was made but no response was received
      // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
      // http.ClientRequest in node.js
      console.log(error.request);
      } else {
      // Something happened in setting up the request that triggered an Error
      console.log('Error', error.message);
      }
      console.log(error.config);
  }

  let routes = {
      store: @json(route('articles.store'))
  }
  console.log(routes);


  let myForm = document.forms['articulo']
  
  let btnEnviar = myForm['Enviar']
  let getButton = myForm['get']
  let echoButton = myForm['echo']

  btnEnviar.addEventListener('click', (e) => {
      let myData = new FormData(myForm);
      axios.post(routes.store, myData)
      .then(r => console.log(r))
      .catch(showAxiosError);
  });
</script>

@endpush

</x-layout.default>