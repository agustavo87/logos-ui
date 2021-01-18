
@push('head-script')
<link rel="stylesheet" href="{{ asset('css/logos.css') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
<style>
  #sidebar-controls {
      display: none;
      position: absolute;
      z-index: 5;
      
  }
  #sidebar-controls button {
      background-color: transparent;
      border: none;
      padding: 0;
  }
  #sidebar-controls .h-icon {
      background-color: #444e;
      /* border-radius: 25px; */
      color: #ccc;
      /* background-color: #fff; */
      /* border: 1px solid #111; */
      border-radius: 50%;
      
      /* color: #111; */
      padding: 7px;
      /* margin-left: 1px; */
      width: 20px;
      height: 20px;
      /* line-height: 20px; */
      box-sizing: content-box;
  }
  #sidebar-controls .h-icon:hover {
      color: #fff;
  }
  #sidebar-controls .controls {
    display: none; 
    margin-left: 5px;
  }
  #sidebar-controls .controls button {
    margin-left: 13px;
  }

  #sidebar-controls #show-controls {
   
  }


  #sidebar-controls #show-controls .h-icon {
    transition: all 100ms ease-out;
    transform-origin: center;
    color: #4444;
    background-color: transparent;
  }
  #sidebar-controls #show-controls .h-icon:hover {
    color:white;
    background-color: #444e;
  }

  #sidebar-controls.active .controls {
    display: inline-block;
  }

  #sidebar-controls.active #show-controls .h-icon {
      margin: auto 0;
      background-color: #444e;
      color:#ccc;
      transform: rotate(45deg);
  }

  #sidebar-controls.active #show-controls .h-icon:hover {
    color:white;
  }

  #sidebar-controls button {
      cursor: pointer;
      display: inline-block;
      /* font-size: 16px; */
      /* padding: 0; */
      height: 20px;
      width: 20px;
      text-align: center;
  }
  #sidebar-controls button:active, #sidebar-controls button:focus {
      outline: none;  
  }

</style>

@endpush

@push('foot-script')
<script src="{{ asset('js/logos.js') }}"></script>

<script>
  let Block = Quill.import('blots/block');


  let sideControls = document.querySelector('#sidebar-controls');
  let quillContainer = document.querySelector('#quill-container');
  let btnShowSideControls = document.querySelector('#show-controls');

  btnShowSideControls.addEventListener('click', function() {
    sideControls.classList.toggle('active')
    quill.focus();
  })
  
  let quill = new Quill(quillContainer,{
      modules: {
          toolbar: '#toolbar'
        //   toolbar: ['bold', 'italic', {'header':2}]
      },
      theme:'bubble',
      placeholder: "Escribe algo épico..."
  });


  quill.addContainer(sideControls);

  quill.on('editor-change', function(eventType, ...args) {
    if (eventType === 'text-change') {
        const [delta, oldDelta, source] = args;
        // console.log("Event '%s'\nrange:%o\noldRange:%o\nsource:%s", 
        //     eventType, delta, oldDelta, source)
        if (source == 'user') {
            delta.forEach((op) => {
                if (op.insert == "\n") {
                    // Evaluates in next 'tick', after quill updates.
                    new Promise( (resolve, reject) => {
                        resolve();
                    }).then(corregirOffset);
                }
            });
        }

    } else if (eventType === 'selection-change') {
        const [range, oldRange, source] = args;
        if(range == null) return;
        if(range.length === 0) {
          console.log("Event '%s'\nrange:%o\noldRange:%o\nsource:%s", 
            eventType, range, oldRange, source);
          const [block, offset] = quill.scroll.descendant(Block, range.index);
          if(block != null && block.domNode.firstChild instanceof HTMLBRElement) {
            console.log("Descendientes:\nblock: %o\noffset: %i",block, offset);
            let lineBounds = quill.getBounds(range);
            sideControls.style.display = 'block'
            sideControls.style.left = lineBounds.left - 50 + "px"
            console.log(lineBounds)
            sideControls.style.top = lineBounds.top - 7  + "px"
          } else {
            console.log('escondiendo');
            sideControls.style.display = 'none';
            sideControls.classList.remove('active')
          }
        } else {
          sideControls.style.display = 'none';
          sideControls.classList.remove('active')
        }
        // console.log("Event '%s'\nrange:%o\noldRange:%o\nsource:%s", 
        //     eventType, range, oldRange, source)

    }
  })

  function corregirOffset() {
    console.log('corrigiendo offset');
    let bounds = quill.getBounds(quill.getSelection().index);
    let margin = bounds.height * 5; // px
    // console.log(bounds);
    let screenBottom = window.pageYOffset + window.innerHeight;
    let cursorBottom =  bounds.bottom + quillContainer.offsetTop
    // console.log('screen bottom: ' + screenBottom + ': cursorBottom: ' + cursorBottom);
    if (cursorBottom + margin > screenBottom ) {
      window.scrollTo(0, cursorBottom + margin * 3 - window.innerHeight)
    }
  }
</script>
@endpush

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
    </span>
</div>

<div id="sidebar-controls">
  <button id="show-controls" type="button">
    <svg class="h-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
    </svg>
  </button>
  <span class="controls">
    <button id="image-button" type="button">
      <svg class="h-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
      </svg>
    </button>
    <button id="video-button" type="button">      
      <svg class="h-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
      </svg>
    </button>
    <button id="tweet-button" type="button">      
      <svg class="h-icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 30 30" width="30px" height="30px">    <path d="M28,6.937c-0.957,0.425-1.985,0.711-3.064,0.84c1.102-0.66,1.947-1.705,2.345-2.951c-1.03,0.611-2.172,1.055-3.388,1.295 c-0.973-1.037-2.359-1.685-3.893-1.685c-2.946,0-5.334,2.389-5.334,5.334c0,0.418,0.048,0.826,0.138,1.215 c-4.433-0.222-8.363-2.346-10.995-5.574C3.351,6.199,3.088,7.115,3.088,8.094c0,1.85,0.941,3.483,2.372,4.439 c-0.874-0.028-1.697-0.268-2.416-0.667c0,0.023,0,0.044,0,0.067c0,2.585,1.838,4.741,4.279,5.23 c-0.447,0.122-0.919,0.187-1.406,0.187c-0.343,0-0.678-0.034-1.003-0.095c0.679,2.119,2.649,3.662,4.983,3.705 c-1.825,1.431-4.125,2.284-6.625,2.284c-0.43,0-0.855-0.025-1.273-0.075c2.361,1.513,5.164,2.396,8.177,2.396 c9.812,0,15.176-8.128,15.176-15.177c0-0.231-0.005-0.461-0.015-0.69C26.38,8.945,27.285,8.006,28,6.937z"/></svg>    </button>
    <button id="divider-button" type="button">
      <svg class="h-icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
      </svg>
    </button>
  </span>
</div>

<div {{ $attributes }} id="quill-wrapp">
    <div id="quill-container">

                <h1 class="ql-align-center">Capítulo IV. Donde se prosigue la narración de la desgracia de nuestro caballero</h1><p class="ql-align-justify">	Viendo, pues, que, en efeto, no podía menearse, acordó de acogerse a su ordinario remedio, que era pensar en algún paso de sus libros; y trújole su locura a la memoria aquel de Valdovinos y del marqués de Mantua, cuando Carloto le dejó herido en la montiña, historia sabida de los niños, no ignorada de los mozos, celebrada y aun creída de los viejos; y, con todo esto, no más verdadera que los milagros de Mahoma. Ésta, pues, le pareció a él que le venía de molde para el paso en que se hallaba; y así, con muestras de grande sentimiento, se comenzó a volcar por la tierra y a decir con debilitado aliento lo mesmo que dicen decía el herido caballero del bosque:</p><p class="ql-align-justify">	<em>-¿Donde estás, señora mía,</em></p><p class="ql-align-justify"><em>que no te duele mi mal?</em></p><p class="ql-align-justify"><em>O no lo sabes, señora,</em></p><p class="ql-align-justify"><em>o eres falsa y desleal.</em></p><p class="ql-align-justify">	Y, desta manera, fue prosiguiendo el romance hasta aquellos versos que dicen:</p><p class="ql-align-justify">	<em>-¡Oh noble marqués de Mantua,</em></p><p class="ql-align-justify"><em>mi tío y señor carnal!</em></p><p class="ql-align-justify">	Y quiso la suerte que, cuando llegó a este verso, acertó a pasar por allí un labrador de su mesmo lugar y vecino suyo, que venía de llevar una carga de trigo al molino; el cual, viendo aquel hombre allí tendido, se llegó a él y le preguntó que quién era y qué mal sentía que tan tristemente se quejaba. Don Quijote creyó, sin duda, que aquél era el marqués de Mantua, su tío; y así, no le respondió otra cosa si no fue proseguir en su romance, donde le daba cuenta de su desgracia y de los amores del hijo del Emperante con su esposa, todo de la mesma manera que el romance lo canta.</p><p class="ql-align-justify">	El labrador estaba admirado oyendo aquellos disparates; y, quitándole la visera, que ya estaba hecha pedazos de los palos, le limpió el rostro, que le tenía cubierto de polvo; y apenas le hubo limpiado, cuando le conoció y le dijo:</p><p class="ql-align-justify">	— Señor Quijana —que así se debía de llamar cuando él tenía juicio y no había pasado de hidalgo sosegado a caballero andante—, ¿quién ha puesto a vuestra merced desta suerte?</p><p class="ql-align-justify">	Pero él seguía con su romance a cuanto le preguntaba. Viendo esto el buen hombre, lo mejor que pudo le quitó el peto y espaldar, para ver si tenía alguna herida; pero no vio sangre ni señal alguna. Procuró levantarle del suelo, y no con poco trabajo le subió sobre su jumento, por parecer caballería más sosegada. Recogió las armas, hasta las astillas de la lanza, y liólas sobre Rocinante, al cual tomó de la rienda, y del cabestro al asno, y se encaminó hacia su pueblo, bien pensativo de oír los disparates que don Quijote decía; y no menos iba don Quijote, que, de puro molido y quebrantado, no se podía tener sobre el borrico, y de cuando en cuando daba unos suspiros que los ponía en el cielo; de modo que de nuevo obligó a que el labrador le preguntase le dijese qué mal sentía; y no parece sino que el diablo le traía a la memoria los cuentos acomodados a sus sucesos, porque, en aquel punto, olvidándose de Valdovinos, se acordó del moro Abindarráez, cuando el alcaide de Antequera, Rodrigo de Narváez, le prendió y llevó cautivo a su alcaidía. De suerte que, cuando el labrador le volvió a preguntar que cómo estaba y qué sentía, le respondió las mesmas palabras y razones que el cautivo Abencerraje respondía a Rodrigo de Narváez, del mesmo modo que él había leído la historia en La Diana, de Jorge de Montemayor, donde se escribe; aprovechándose della tan a propósito, que el labrador se iba dando al diablo de oír tanta máquina de necedades; por donde conoció que su vecino estaba loco, y dábale priesa a llegar al pueblo, por escusar el enfado que don Quijote le causaba con su larga arenga. Al cabo de lo cual, dijo:</p><p class="ql-align-justify">	— Sepa vuestra merced, señor don Rodrigo de Narváez, que esta hermosa Jarifa que he dicho es ahora la linda Dulcinea del Toboso, por quien yo he hecho, hago y haré los más famosos hechos de caballerías que se han visto, vean ni verán en el mundo.</p><p class="ql-align-justify">	A esto respondió el labrador:</p><p class="ql-align-justify">	— Mire vuestra merced, señor, pecador de mí, que yo no soy don Rodrigo de Narváez, ni el marqués de Mantua, sino Pedro Alonso, su vecino; ni vuestra merced es Valdovinos, ni Abindarráez, sino el honrado hidalgo del señor Quijana.</p><p class="ql-align-justify">	— Yo sé quién soy —respondió don Quijote—; y sé que puedo ser no sólo los que he dicho, sino todos los Doce Pares de Francia, y aun todos los Nueve de la Fama, pues a todas las hazañas que ellos todos juntos y cada uno por sí hicieron, se aventajarán las mías.</p><p class="ql-align-justify">	En estas pláticas y en otras semejantes, llegaron al lugar a la hora que anochecía, pero el labrador aguardó a que fuese algo más noche, porque no viesen al molido hidalgo tan mal caballero. Llegada, pues, la hora que le pareció, entró en el pueblo, y en la casa de don Quijote, la cual halló toda alborotada; y estaban en ella el cura y el barbero del lugar, que eran grandes amigos de don Quijote, que estaba diciéndoles su ama a voces:</p><p class="ql-align-justify">	— ¿Qué le parece a vuestra merced, señor licenciado Pero Pérez —que así se llamaba el cura—, de la desgracia de mi señor? Tres días ha que no parecen él, ni el rocín, ni la adarga, ni la lanza ni las armas. ¡Desventurada de mí!, que me doy a entender, y así es ello la verdad como nací para morir, que estos malditos libros de caballerías que él tiene y suele leer tan de ordinario le han vuelto el juicio; que ahora me acuerdo haberle oído decir muchas veces, hablando entre sí, que quería hacerse caballero andante e irse a buscar las aventuras por esos mundos. Encomendados sean a Satanás y a Barrabás tales libros, que así han echado a perder el más delicado entendimiento que había en toda la Mancha.</p><p class="ql-align-justify">	La sobrina decía lo mesmo, y aun decía más:</p><p class="ql-align-justify">	— Sepa, señor maese Nicolás —que éste era el nombre del barbero—, que muchas veces le aconteció a mi señor tío estarse leyendo en estos desalmados libros de desventuras dos días con sus noches, al cabo de los cuales, arrojaba el libro de las manos, y ponía mano a la espada y andaba a cuchilladas con las paredes; y cuando estaba muy cansado, decía que había muerto a cuatro gigantes como cuatro torres, y el sudor que sudaba del cansancio decía que era sangre de las feridas que había recebido en la batalla; y bebíase luego un gran jarro de agua fría, y quedaba sano y sosegado, diciendo que aquella agua era una preciosísima bebida que le había traído el sabio Esquife, un grande encantador y amigo suyo. Mas yo me tengo la culpa de todo, que no avisé a vuestras mercedes de los disparates de mi señor tío, para que lo remediaran antes de llegar a lo que ha llegado, y quemaran todos estos descomulgados libros, que tiene muchos, que bien merecen ser abrasados, como si fuesen de herejes.</p>

    </div>
</div>

