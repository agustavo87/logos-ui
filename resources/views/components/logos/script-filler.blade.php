<script>
    function sourceFiller(schema, dataModel, bootDataFunc) {
        return {
            display: false,
            schema: schema,
            data: dataModel,
            bootData: bootDataFunc,
            handleSetSchema: function ($event) {
                console.log(this.schema + ': handler:')
                if ($event.detail.schema !== this.schema) {
                    console.log(this.schema + ': it is not for me')
                    this.display = false;
                    return;
                };
                console.log(this.schema + ': it ts for me')
                this.bootData($event.detail.data)
                this.display = true
            },
            handleInput: function ($event, $dispatch) {
                this.data[$event.target.name] = $event.target.value
                $dispatch('data-change', this.data);
            }
        }
    }
</script>