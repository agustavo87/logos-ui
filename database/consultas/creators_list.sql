SELECT * FROM creators
INNER JOIN attributes
ON creators.id = attributes.attributable_id
WHERE attributable_genus = 'creator';