<?php
include '../config/dataBaseConnect.php';

function fetch_countries($connection) {
    $query = "SELECT * FROM countries";
    $result = $connection->query($query);
    $countries = [];
    while ($row = $result->fetch_assoc()) {
        $countries[] = $row;
    }
    return $countries;
}

function fetch_states($connection, $country_id) {
    $query = "SELECT * FROM states WHERE country_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $country_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $states = [];
    while ($row = $result->fetch_assoc()) {
        $states[] = $row;
    }
    return $states;
}
?>
