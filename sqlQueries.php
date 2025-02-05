<?php
// queries.php
function getUserById($conn, $user_id) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateUser($conn, $user_id, $name, $email, $password) {
    $sql = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $password, $user_id);
    return $stmt->execute();
}
?>
