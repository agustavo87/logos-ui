SELECT * FROM sources INNER JOIN attributes ON sources.id = attributable_id
WHERE attributable_genus = 'source'
AND sources.id = '51'
ORDER BY sources.id DESC