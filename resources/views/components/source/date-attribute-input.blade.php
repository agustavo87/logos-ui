<div x-data="dateAttribute">
    <input type="date"
        x-bind:name="'attribute.' + attribute.code"
        x-bind:id="'input-' + attribute.code"
        x-effect="_date = $store.source.attributes[attribute.code] ? $store.source.attributes[attribute.code] : ($store.source.attributes[attribute.base] ? $store.source.attributes[attribute.base] : null)"
        x-bind:value="mydate"
        x-on:input="$store.source.attributes[attribute.code] = $event.target.value"
        {{ $attributes }}
    >
</div>

@once
    @push('head-script')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('dateAttribute', () => {
                    return {
                        _date: null,
                        get mydate() {
                            if (this._date == null) return this._date
                            let d = new Date(this._date)
                            let m = (d.getMonth() + 1).toString()
                            m = m.length < 2 ? '0' + m : m;
                            return d.getFullYear() + '-' + m + '-' + d.getDate().toString()
                        }
                    }
                })
            })
        </script>
    @endpush
@endonce
