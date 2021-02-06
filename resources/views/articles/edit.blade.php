<x-layout.default :title="$title">
<x-container>
    <x-main-heading>
        {{ $title }}
    </x-main-heading>
    <livewire:logos-create :id="$id"/>
</x-container>

</x-layout.default>