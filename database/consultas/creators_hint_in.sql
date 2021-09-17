SELECT creators.id AS id, creator_type_code_name as `type`, attributes.attribute_type_code_name AS `attribute`, attributes.text_value AS `value`
FROM creators INNER JOIN attributes ON creators.id = attributes.attributable_id
WHERE user_id = 1
AND attributes.attributable_genus = 'creator'
AND creators.id IN (257, 259, 263, 265, 521)
