
1ro. Insertar fuente.
- Chequear que el tipo de fuente es válido, sino excepción.
- Chequear que los tipos de atributos pasados están incluidos en el tipo de fuente. Sino excepción
- (Scale) Chequear si existe una fuente con atributos similares, si lo hace retornar id.
- Crear una nueva fuente, asociada al tipo
- Insertar los atributos (concretos) asociados a la fuente y al tipo de atributo
    - insertar valor de tipo adecuado.
    - insertar atributo asociado a la fuente, tipo de atributo, y al valor.

2do. Insertar Creador
    - Chequear que sea de un tipo válido. Sino excepción.
    - Chequear que los tipos de atributos estén incluidos en los atributos válidos para el tipo de creador.
    - (Scale)Chequear si existe un creador con atributos similares, si lo hace retornar id.
    - Insertar Creador
    - Insertar atributos (concreots) asociados
        - Insertar valor de tipo adecuado
        - Insertar atributo asociado al creador, al tipo de atributo y al valor.

3ro. Insertar Participación.
    - Chequear que el Rol forme parete de los roles válidos de la fuente. Sino excepción. 
    - Chequear que creador existe. Sinó excepción.
    - Insertar participación con su relevancia, asociando fuente, creador y rol.
