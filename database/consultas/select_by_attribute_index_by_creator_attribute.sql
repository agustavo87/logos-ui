SELECT sources.id, text_value , sources.key
FROM 

(
	(sources INNER JOIN participations ON sources.id = participations.source_id)
	INNER JOIN attributes ON participations.creator_id = attributes.attributable_id
)

# SELECT BY ATTRIBUTES
WHERE sources.id IN (
	SELECT DISTINCT sources.id FROM sources
	WHERE sources.id IN (SELECT attributable_id FROM attributes WHERE attributable_genus = 'source' AND text_value LIKE '%6113d6216ce4c%' AND attribute_type_code_name = 'title')
)

AND attributes.attributable_genus = 'creator'
# JUST GET THE RELEVANT ATTRIBUTE TO ORDER
AND attributes.attribute_type_code_name = 'lastName'

## ORDER BY SOURCE ATTRIBUTES
# AND attributes.attribute_type_code_name = 'date'
# ORDER BY attributes.date_value

# ORDER BY CREATOR PROPERTIES
#GROUP BY id
ORDER BY attributes.text_value ASC, sources.`key`

# LIMIT
 LIMIT 3 
 OFFSET 0

