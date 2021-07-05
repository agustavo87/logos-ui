SELECT * FROM sources
INNER JOIN attributes ON sources.id = attributes.attributable_id
## solo obtenemos attributos de fuentes
WHERE attributable_genus = 'source'

## especificamos el tipo de fuentes que deseamos
# AND source_type_code_name = 'journalArticle'

## especificamos el id del propietario
# AND user_id = 3

## Selección por attributos
# AND sources.id IN (SELECT attributable_id FROM attributes WHERE attributable_genus = 'source' AND text_value LIKE '%e%' AND attribute_type_code_name = 'title')
# AND sources.id IN (SELECT attributable_id FROM attributes WHERE attributable_genus = 'source' AND text_value LIKE '%a%' AND attribute_type_code_name = 'abstractNote')

## Se selecciona por datos de participacion y/o creador
AND sources.id IN (
	# Selecciona ID de fuentes según datos del rol y/o creador
	SELECT source_id
	FROM attributes INNER JOIN participations ON attributable_id = creator_id
	# nos aseguramos de solo seleccionar creadores
	WHERE attributable_genus = 'creator'
	# se especifica el nombre del atributo
	AND attribute_type_code_name = 'name'
	#se especifica el nombre de la columna de valor
	AND text_value LIKE '%Miguel%'
	# también se puede especificar el rol
	AND role_code_name = 'reviewedAuthor'
)

## Limitamos los attributos mostrados a unos cuantos para hacer más sencillo de ver
AND attribute_type_code_name IN ('title', 'abstractNote', 'name');