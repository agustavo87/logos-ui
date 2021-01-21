<x-layout.master>
    <x-form.sm id="consulta">
        <x-form.field name="dato" label="Dato" type="text" value="default" placeholder="Escribí un dato"/>
        <div>
            <x-form.button name="set" type="button" class="m-2">Insertar</x-form.button>
            <x-form.button name="get" type="button" class="m-2">Obtener</x-form.button>
            <x-form.button name="echo" type="button" class="m-2">Echo</x-form.button>
        </div>
    </x-form.sm>

    @push('foot-script')
    <script>
        let myForm = document.forms['consulta']
        
        let setButton = myForm['set']
        let getButton = myForm['get']
        let echoButton = myForm['echo']

        setButton.addEventListener('click', (e) => {
            let myData = new FormData(myForm);
            axios.post('/api/test', {
                action: 'set',
                data: myData.get('dato')
            }).
            then(r => console.log(r));
        });
        getButton.addEventListener('click', (e) => {
            let myData = new FormData(myForm);
            axios.post('/api/test', {
                action: 'get',
                data: myData.get('dato')
            }).
            then(r => console.log(r));
        });
        echoButton.addEventListener('click', (e) => {
            let myData = new FormData(myForm);
            axios.post('/api/test', new FormData(myForm)).
            then(r => console.log(r));
        });
        
    </script>
        
    @endpush
</x-layout.master>


        