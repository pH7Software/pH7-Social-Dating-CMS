UPDATE [DB_PREFIX]Members SET sex = :sex, matchSex = :match_sex, birthDate = :birth_date WHERE profileId = :profile_id LIMIT 1;
