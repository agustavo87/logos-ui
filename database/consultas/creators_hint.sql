SELECT attributable_id 
FROM attributes  INNER JOIN creators ON attributes.attributable_id = creators.id
WHERE attributable_genus = 'creator'
AND attribute_type_code_name = 'lastName'
AND text_value LIKE '%a%'
ORDER BY text_value ASC
LIMIT 5
