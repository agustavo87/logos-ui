<div>
    <p>soy un storage dinámico.</p>
    <pre>
        @json($data)
    </pre>
    <button
        wire:click="change"
        class="border bg-blue-100 p-2"
    >
        Cambiar
    </button>
    <div>
        <ul>
            <li x-data>Valor de datos:
                <span x-text="JSON.stringify(datos)"></span>
            </li>
        </ul>
    </div>
    <hr>
    <div class="mt-4">
        <h3 class="font-medium text-xl">Conclusión</h3>
        <p>
            Se puede pasar datos iniciales con javascript, que estos no son modificados
            por livewire al actualizarse las variables.
        </p>
    </div>
</div>
@push('head-script')
<script>
    let datos = @json($data);
    document.addEventListener('livewire:load', function () {
        window.tetas = @this.data;
        console.log({tetas})
    })
</script>
@endpush
