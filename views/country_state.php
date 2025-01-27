<?php

// locationHelpers.php

function getCountries($connection) {
    $query = "SELECT id, name FROM countries";
    $result = $connection->query($query);
    $countries = [];
    while ($row = $result->fetch_assoc()) {
        $countries[] = $row;
    }
    return $countries;
}

function getStates($connection, $countryId) {
    $query = "SELECT id, name FROM states WHERE country_id = $countryId";
    $result = $connection->query($query);
    $states = [];
    while ($row = $result->fetch_assoc()) {
        $states[] = $row;
    }
    return $states;
}
?>