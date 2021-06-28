SELECT * FROM sources
INNER JOIN attributes
ON sources.id = attributes.attributable_id
WHERE attributable_genus = 'source';