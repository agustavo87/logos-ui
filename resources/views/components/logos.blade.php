@props(['initialDelta' => null])

@push('head-script')
  <link rel="stylesheet" href="{{ asset('css/logos.css') }}">
  <script src="{{ asset('js/logos.js') }}"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/KaTeX/0.7.1/katex.min.js" defer></script>
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/KaTeX/0.7.1/katex.min.css">
@endpush

@push('foot-script')
  <script>
    const LogosUI = {
      newEventCall: (name, resolve, params = []) => new CustomEvent(name, {
        detail: {
          resolve:resolve,
          ...params
        }
      }),
      dialogGet: function (dialogName, params) {
        return new Promise((resolve, reject) => {
          window.dispatchEvent(this.newEventCall(dialogName, resolve, params))
        })
      }
    }

    const myLogos =  new Logos({
      initialDelta: @json($initialDelta),
      sideControls: '#sidebar-controls',
      quillContainer: '#quill-container',
      btnShowSideControls: '#show-controls',
      toolbar: '#toolbar'
    });
  </script>
@endpush

<div wire:ignore class="logos" x-cloak>
  <div id="toolbar">
    <span class="ql-formats">
        <select class="ql-header">
            <option value="2"></option>
            <option value="3"></option>
            <option selected></option>
        </select>
    </span>
    <span class="ql-formats">
      <button class="ql-bold"></button>
      <button class="ql-italic"></button>
      <button class="ql-list" value="ordered"></button>
      <button class="ql-list" value="bullet"></button>
      <button class="ql-script" value="sub"></button>
      <button class="ql-script" value="super"></button>
      <button class="ql-formula"></button>
      <button class="ql-blockquote"></button>

      {{-- Logos Icons --}}
      <button id="add-source" class="lg-btn">
        <x-icons.book />
      </button>
      <button id="add-note" class="lg-btn">
        <x-icons.comment />
      </button>
      {{-- /Logos Icons --}}

      <button class="ql-link"></button>
    </span>
  </div>

  <div id="sidebar-controls">
    <button id="show-controls" type="button">
      <x-icons.cross class="h-icon" />
    </button>
    <span class="controls">
      <button id="image-button" type="button">
        <x-icons.camera class="h-icon" />
      </button>
      <button id="video-button" type="button">
        <x-icons.video class="h-icon"  />
      </button>
      <button id="tweet-button" type="button">
        <x-icons.tweeter class="h-icon" />
      </button>
      <button id="divider-button" type="button">
        <x-icons.horizontal-dots class="h-icon" />
      </button>
    </span>
  </div>

  <div {{ $attributes }} id="quill-wrapp" >
      <div id="quill-container">
  {{--  test text
                  <h1 class="ql-align-center">Capítulo IV. Donde se prosigue la narración de la desgracia de nuestro caballero</h1><p class="ql-align-justify">	Viendo, pues, que, en efeto, no podía menearse, acordó de acogerse a su ordinario remedio, que era pensar en algún paso de sus libros; y trújole su locura a la memoria aquel de Valdovinos y del marqués de Mantua, cuando Carloto le dejó herido en la montiña, historia sabida de los niños, no ignorada de los mozos, celebrada y aun creída de los viejos; y, con todo esto, no más verdadera que los milagros de Mahoma. Ésta, pues, le pareció a él que le venía de molde para el paso en que se hallaba; y así, con muestras de grande sentimiento, se comenzó a volcar por la tierra y a decir con debilitado aliento lo mesmo que dicen decía el herido caballero del bosque:</p><p class="ql-align-justify">	<em>-¿Donde estás, señora mía,</em></p><p class="ql-align-justify"><em>que no te duele mi mal?</em></p><p class="ql-align-justify"><em>O no lo sabes, señora,</em></p><p class="ql-align-justify"><em>o eres falsa y desleal.</em></p><p class="ql-align-justify">	Y, desta manera, fue prosiguiendo el romance hasta aquellos versos que dicen:</p><p class="ql-align-justify">	<em>-¡Oh noble marqués de Mantua,</em></p><p class="ql-align-justify"><em>mi tío y señor carnal!</em></p><p class="ql-align-justify">	Y quiso la suerte que, cuando llegó a este verso, acertó a pasar por allí un labrador de su mesmo lugar y vecino suyo, que venía de llevar una carga de trigo al molino; el cual, viendo aquel hombre allí tendido, se llegó a él y le preguntó que quién era y qué mal sentía que tan tristemente se quejaba. Don Quijote creyó, sin duda, que aquél era el marqués de Mantua, su tío; y así, no le respondió otra cosa si no fue proseguir en su romance, donde le daba cuenta de su desgracia y de los amores del hijo del Emperante con su esposa, todo de la mesma manera que el romance lo canta.</p><p class="ql-align-justify">	El labrador estaba admirado oyendo aquellos disparates; y, quitándole la visera, que ya estaba hecha pedazos de los palos, le limpió el rostro, que le tenía cubierto de polvo; y apenas le hubo limpiado, cuando le conoció y le dijo:</p><p class="ql-align-justify">	— Señor Quijana —que así se debía de llamar cuando él tenía juicio y no había pasado de hidalgo sosegado a caballero andante—, ¿quién ha puesto a vuestra merced desta suerte?</p><p class="ql-align-justify">	Pero él seguía con su romance a cuanto le preguntaba. Viendo esto el buen hombre, lo mejor que pudo le quitó el peto y espaldar, para ver si tenía alguna herida; pero no vio sangre ni señal alguna. Procuró levantarle del suelo, y no con poco trabajo le subió sobre su jumento, por parecer caballería más sosegada. Recogió las armas, hasta las astillas de la lanza, y liólas sobre Rocinante, al cual tomó de la rienda, y del cabestro al asno, y se encaminó hacia su pueblo, bien pensativo de oír los disparates que don Quijote decía; y no menos iba don Quijote, que, de puro molido y quebrantado, no se podía tener sobre el borrico, y de cuando en cuando daba unos suspiros que los ponía en el cielo; de modo que de nuevo obligó a que el labrador le preguntase le dijese qué mal sentía; y no parece sino que el diablo le traía a la memoria los cuentos acomodados a sus sucesos, porque, en aquel punto, olvidándose de Valdovinos, se acordó del moro Abindarráez, cuando el alcaide de Antequera, Rodrigo de Narváez, le prendió y llevó cautivo a su alcaidía. De suerte que, cuando el labrador le volvió a preguntar que cómo estaba y qué sentía, le respondió las mesmas palabras y razones que el cautivo Abencerraje respondía a Rodrigo de Narváez, del mesmo modo que él había leído la historia en La Diana, de Jorge de Montemayor, donde se escribe; aprovechándose della tan a propósito, que el labrador se iba dando al diablo de oír tanta máquina de necedades; por donde conoció que su vecino estaba loco, y dábale priesa a llegar al pueblo, por escusar el enfado que don Quijote le causaba con su larga arenga. Al cabo de lo cual, dijo:</p><p class="ql-align-justify">	— Sepa vuestra merced, señor don Rodrigo de Narváez, que esta hermosa Jarifa que he dicho es ahora la linda Dulcinea del Toboso, por quien yo he hecho, hago y haré los más famosos hechos de caballerías que se han visto, vean ni verán en el mundo.</p><p class="ql-align-justify">	A esto respondió el labrador:</p><p class="ql-align-justify">	— Mire vuestra merced, señor, pecador de mí, que yo no soy don Rodrigo de Narváez, ni el marqués de Mantua, sino Pedro Alonso, su vecino; ni vuestra merced es Valdovinos, ni Abindarráez, sino el honrado hidalgo del señor Quijana.</p><p class="ql-align-justify">	— Yo sé quién soy —respondió don Quijote—; y sé que puedo ser no sólo los que he dicho, sino todos los Doce Pares de Francia, y aun todos los Nueve de la Fama, pues a todas las hazañas que ellos todos juntos y cada uno por sí hicieron, se aventajarán las mías.</p><p class="ql-align-justify">	En estas pláticas y en otras semejantes, llegaron al lugar a la hora que anochecía, pero el labrador aguardó a que fuese algo más noche, porque no viesen al molido hidalgo tan mal caballero. Llegada, pues, la hora que le pareció, entró en el pueblo, y en la casa de don Quijote, la cual halló toda alborotada; y estaban en ella el cura y el barbero del lugar, que eran grandes amigos de don Quijote, que estaba diciéndoles su ama a voces:</p><p class="ql-align-justify">	— ¿Qué le parece a vuestra merced, señor licenciado Pero Pérez —que así se llamaba el cura—, de la desgracia de mi señor? Tres días ha que no parecen él, ni el rocín, ni la adarga, ni la lanza ni las armas. ¡Desventurada de mí!, que me doy a entender, y así es ello la verdad como nací para morir, que estos malditos libros de caballerías que él tiene y suele leer tan de ordinario le han vuelto el juicio; que ahora me acuerdo haberle oído decir muchas veces, hablando entre sí, que quería hacerse caballero andante e irse a buscar las aventuras por esos mundos. Encomendados sean a Satanás y a Barrabás tales libros, que así han echado a perder el más delicado entendimiento que había en toda la Mancha.</p><p class="ql-align-justify">	La sobrina decía lo mesmo, y aun decía más:</p><p class="ql-align-justify">	— Sepa, señor maese Nicolás —que éste era el nombre del barbero—, que muchas veces le aconteció a mi señor tío estarse leyendo en estos desalmados libros de desventuras dos días con sus noches, al cabo de los cuales, arrojaba el libro de las manos, y ponía mano a la espada y andaba a cuchilladas con las paredes; y cuando estaba muy cansado, decía que había muerto a cuatro gigantes como cuatro torres, y el sudor que sudaba del cansancio decía que era sangre de las feridas que había recebido en la batalla; y bebíase luego un gran jarro de agua fría, y quedaba sano y sosegado, diciendo que aquella agua era una preciosísima bebida que le había traído el sabio Esquife, un grande encantador y amigo suyo. Mas yo me tengo la culpa de todo, que no avisé a vuestras mercedes de los disparates de mi señor tío, para que lo remediaran antes de llegar a lo que ha llegado, y quemaran todos estos descomulgados libros, que tiene muchos, que bien merecen ser abrasados, como si fuesen de herejes.</p> --}}

      </div>
  </div>
  <div x-data="newDialogs()">
    <button
      @click="getSource"
      class="px-4 py-2 rounded bg-gray-800 text-white"
    >
      Seleccionar Fuente
    </button>
    {{-- <button
      @click="getEditSource"
      class="px-4 py-2 rounded bg-gray-800 text-white"
    >
      Editar Fuente
    </button> --}}
  </div>
  <livewire:source-select />
  {{-- <livewire:source-edit /> --}}
<script>
    function newDialogs() {
      return {
        getSource: () => {
          LogosUI.dialogGet('source-get', {ui: LogosUI})
                 .then(r => console.log(r))
        },
        // getEditSource: () => {
        //   LogosUI.dialogGet('source-edit', {withBg: true, ui: LogosUI}).then(r => console.log(r))
        // }
      }
    }
  </script>
</div>
