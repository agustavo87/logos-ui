SELECT sources.id, sources.key, attributes.* 
FROM 

(
	(sources INNER JOIN participations ON sources.id = participations.source_id)
	INNER JOIN attributes ON participations.creator_id = attributes.attributable_id
)

# SELECT BY ATTRIBUTES
WHERE sources.id IN (
	SELECT DISTINCT sources.id FROM sources
	WHERE sources.id IN (SELECT attributable_id FROM attributes WHERE attributable_genus = 'source' AND text_value LIKE '%ar%' AND attribute_type_code_name = 'title')
	AND sources.id IN (SELECT attributable_id FROM attributes WHERE attributable_genus = 'source' AND text_value LIKE '%b%' AND attribute_type_code_name = 'abstractNote')
)

AND attributes.attributable_genus = 'creator'
# JUST GET THE RELEVANT ATTRIBUTE TO ORDER
AND attributes.attribute_type_code_name = 'lastName'

## ORDER BY SOURCE ATTRIBUTES
# AND attributes.attribute_type_code_name = 'date'
# ORDER BY attributes.date_value

# ORDER BY CREATOR PROPERTIES
ORDER BY attributes.text_value DESC

# LIMIT
LIMIT 5 
OFFSET 2

