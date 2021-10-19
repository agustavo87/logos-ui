SELECT 
	sources.id, 
	sources.`key`, 
	attributes.attribute_type_code_name AS attribute,
	attributes.text_value,
	# attributes.date_value,
	# attributes.number_value,
	# attributes.complex_value
	participations.role_code_name AS role, 
	participations.relevance
FROM (sources INNER JOIN participations ON sources.id = participations.source_id) INNER JOIN attributes ON participations.creator_id = attributes.attributable_id
WHERE attributes.attributable_genus = 'creator' 
AND attributes.attribute_type_code_name IN ('lastName')
#AND sources.id IN (97, 106, 98)