UPDATE [DB_PREFIX]members_info
SET
propertyPrice = :price,
propertyBedrooms = :bedrooms,
propertyBathrooms = :bathrooms,
propertySize = :house_size,
propertyYearBuilt = :year_built,
propertyHomeType = :home_type,
propertyHomeStyle = :home_style,
propertySquareFeet = :square_ft,
propertyLotSize = :lot_size,
propertyGarageSpaces = :garage_spaces,
propertyCarportSpaces = :carport_spaces
WHERE profileId = :profile_id LIMIT 1;
