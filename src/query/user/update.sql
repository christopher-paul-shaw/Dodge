UPDATE
	user
SET name = :name,
email = :email,
password = :password,
ip = :ip,
token = :token,
permission = :permission
WHERE id_user = :id_user