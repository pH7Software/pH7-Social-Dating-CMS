INSERT INTO [DB_PREFIX]members (email, username, password, firstName, reference, active, ip, hashValidation, joinDate, lastActivity, affiliatedId)
VALUES (:email, :username, :password, :first_name, :reference, :is_active, :ip, :hash_validation, :current_date, :current_date, :affiliated_id);
