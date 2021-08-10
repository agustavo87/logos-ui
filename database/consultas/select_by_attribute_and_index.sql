
SELECT sources.id, sources.key, attributes.attribute_type_code_name, attributes.date_value FROM sources
INNER JOIN attributes ON sources.id = attributes.attributable_id

#SELECT BY ATTRIBUTES
WHERE sources.id IN (
	SELECT DISTINCT sources.id FROM sources
	WHERE sources.id IN (SELECT attributable_id FROM attributes WHERE attributable_genus = 'source' AND text_value LIKE '%ar%' AND attribute_type_code_name = 'title')
	AND sources.id IN (SELECT attributable_id FROM attributes WHERE attributable_genus = 'source' AND text_value LIKE '%b%' AND attribute_type_code_name = 'abstractNote')
)
## ORDER BY SOURCE ATTRIBUTES
AND attributes.attribute_type_code_name = 'date'
# ORDER BY attributes.date_value

# ORDER BY SOURCE PROPERTIES
ORDER BY sources.key DESC

# LIMIT
# LIMIT 5 ;
#OFFSET 2;
