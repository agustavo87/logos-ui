
SELECT 
	participations.source_id
	, creator_id
	, role_code_name AS role
	, relevance
	, most_relevant

FROM participations INNER JOIN (
	SELECT source_id, min(relevance) AS most_relevant from participations GROUP BY source_id
) AS principal_autors
ON participations.source_id = principal_autors.source_id
WHERE relevance = most_relevant
