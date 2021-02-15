<script>
    function sourceFiller(schema, dataModel, bootDataFunc) {
        return {
            display: false,
            schema: schema,
            data: dataModel,
            handleInput: function ($e, $d) {
                this.data[$e.target.name] = $e.target.value
                $d('cambio', this.data);
            },
            handleSetSchema: function (e) {
                console.log('manejando set-schema', e)
                console.log(e.detail.schema, '===', this.schema, '?')
                if (e.detail.schema !== this.schema) {
                    this.display = false;
                    return;
                };
                this.bootData(e.detail.data)
                this.display = true
            },
            bootData: bootDataFunc
        }
    }
</script>