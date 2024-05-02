<?php

$servername = "localhost";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbname = "test_task";
    $sqlCreateDB = "CREATE DATABASE IF NOT EXISTS $dbname";
    $conn->exec($sqlCreateDB);

    $conn->exec("USE $dbname");

    $sqlCreatePostsTable = "CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        userId INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        body TEXT NOT NULL
    )";
    $conn->exec($sqlCreatePostsTable);

    $sqlCreateCommentsTable = "CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        postId INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        body TEXT NOT NULL,
        FOREIGN KEY (postId) REFERENCES posts(id)
    )";
    $conn->exec($sqlCreateCommentsTable);

    require 'import_blog_data.php';

} catch(PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}


?>
