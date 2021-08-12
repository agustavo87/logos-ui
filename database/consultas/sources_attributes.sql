SELECT sources.id, sources.`key`, attributes.* FROM sources INNER JOIN attributes ON sources.id = attributes.attributable_id
WHERE attributes.attributable_genus = 'source' AND attributes.attribute_type_code_name IN ('title', 'abstract')
AND sources.id IN (97, 106, 98)