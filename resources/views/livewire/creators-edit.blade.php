<div x-data="CreatorsComponent()" x-init="init">
    <template x-for="creator in creators" :key="creator.key">
        <div x-text="creator.key"></div>
    </template>
<script>
    function CreatorsComponent() {
        return {
            creators: @entangle('arrCreators'),
            init: function () {
                this.creators.forEach(creator => {
                    console.log(creator.key);
                });
            }

        }
    }    
</script>
</div>
