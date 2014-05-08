UPDATE [DB_PREFIX]MembersInfo SET country = :country, city = :city, state = :state, zipCode = :zip_code WHERE profileId = :profile_id LIMIT 1;
