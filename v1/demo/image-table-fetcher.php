<?php

// Assuming you've already connected to your database using PDO
// $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'your_username', 'your_password');

// SQL query to retrieve table names with column "image_1"
$sql = "SELECT table_name
        FROM information_schema.columns
        WHERE column_name = 'image_1' 
        AND table_schema = :database";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

$stmt->bindParam(':database',getenv('DB_NAME'), PDO::PARAM_STR);
// Execute the query
$stmt->execute();

// Fetch table names
$tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($tables) > 0) {
    echo "Tables with 'image_1' column:<br>";
    foreach ($tables as $table) {
        echo $table["table_name"] . "<br>";
    }
} else {
    echo "No tables found with 'image_1' column.";
}
?>