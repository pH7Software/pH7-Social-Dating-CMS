UPDATE [DB_PREFIX]MembersInfo SET country = :country, city = :city, zipCode = :zip_code WHERE profileId = :profile_id LIMIT 1;
