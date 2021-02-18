<script>
    function sourceFiller(schema, dataModel, bootDataFunc) {
        return {
            display: false,
            schema: schema,
            data: dataModel,
            bootData: bootDataFunc,
            handleSetSchema: function ($event) {
                if ($event.detail.schema !== this.schema) {
                    this.display = false;
                    return;
                };
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