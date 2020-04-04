UPDATE [DB_PREFIX]members SET sex = :sex, matchSex = :match_sex, birthDate = :birth_date WHERE profileId = :profile_id LIMIT 1;
