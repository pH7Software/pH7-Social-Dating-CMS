UPDATE [DB_PREFIX]members_info SET country = :country, city = :city, zipCode = :zip_code WHERE profileId = :profile_id LIMIT 1;
