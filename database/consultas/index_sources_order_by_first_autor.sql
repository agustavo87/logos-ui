SELECT 
	first_participations.source_id AS source_id 
	, first_participations.creator_id AS first_creator_id
	, first_participations.role_code_name AS role
	, attributes.attribute_type_code_name AS attribute_name
	, attributes.text_value AS attribute_value
FROM (
   # First Participations Table
	SELECT 
		participations.source_id
		, creator_id
		, role_code_name
		, relevance 
	FROM participations INNER JOIN (
		# Min relevant participation by source
		SELECT source_id, min(relevance) AS min_relevance from participations GROUP BY source_id
	) AS min_relevances
	ON participations.source_id = min_relevances.source_id
	# Filter just the first participation (with lesser relevance).
	WHERE relevance = min_relevance
) AS first_participations
# Add the creators attributes data
INNER JOIN attributes ON first_participations.creator_id = attributes.attributable_id
WHERE attributable_genus = 'creator'
AND attribute_type_code_name = 'lastName'

# SELECT BY ATTRIBUTES
AND source_id IN (
	SELECT DISTINCT attributable_id FROM attributes 
	WHERE attributable_genus = 'source' 
	AND text_value LIKE '%6113d6216ce4c%' 
	AND attribute_type_code_name = 'title'
)

ORDER BY attribute_value