SELECT *
FROM attributes INNER JOIN participations ON attributable_id = creator_id
WHERE attributable_genus = 'creator' AND 
# se especifica el nombre del atributo
attribute_type_code_name = 'name' AND
#se especifica el nombre de la columna de valor
text_value LIKE '%Miguel%'