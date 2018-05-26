SELECT
	id_user,
	name,
	email,
	permission
FROM user
WHERE 
(
	name LIKE CONCAT('%',:name,'%')	
)