SELECT
	b."name",
	o."name" AS outlet,
	o.address,
	o.longitude,
	o.latitude,
	(
		SELECT count(id) FROM products p 
		WHERE p.brand_id = b.id
	) AS total_product,
	(
		SELECT
			CASE WHEN o."name" IS NOT NULL
			THEN trunc((point(-6.175136383245059, 106.82709913722499) <@> (point(o.longitude, o.latitude)::point))::NUMERIC * 1.609344, 2) || ' KM'
			ELSE 'N/A'
			END
	) AS distance
FROM brands b 
LEFT JOIN outlets o ON o.brand_id = b.id
ORDER BY distance