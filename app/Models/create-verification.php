<?php
include 'Config/connect.php';

// SQL to create table
$sql = "CREATE TABLE verification (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table users created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
