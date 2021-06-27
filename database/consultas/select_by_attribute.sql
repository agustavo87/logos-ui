SELECT * FROM sources
INNER JOIN attributes ON sources.id = attributes.attributable_id
WHERE 
sources.id IN (SELECT attributable_id FROM attributes WHERE text_value LIKE '%las%' AND attribute_type_code_name = 'title')
AND sources.id IN (SELECT attributable_id FROM attributes WHERE text_value LIKE '%por%' AND attribute_type_code_name = 'abstractNote')
AND attribute_type_code_name IN ('title', 'abstractNote', 'name');