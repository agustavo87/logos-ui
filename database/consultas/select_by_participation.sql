SELECT * FROM sources 
INNER JOIN attributes ON sources.id = attributes.attributable_id
WHERE attributable_genus = 'source' AND
sources.id IN (
	# Selecciona ID de fuentes según datos del rol y/o creador
	SELECT source_id
	FROM attributes INNER JOIN participations ON attributable_id = creator_id
	# nos aseguramos de solo seleccionar creadores
	WHERE attributable_genus = 'creator' AND 
	# se especifica el nombre del atributo
	attribute_type_code_name = 'name' AND
	#se especifica el nombre de la columna de valor
	text_value LIKE '%Miguel%'
	# también se puede especificar el rol
	AND role_code_name = 'reviewedAuthor'
)