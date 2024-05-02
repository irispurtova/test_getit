<?php
require_once 'create_database.php';

$stmt = $conn->query("SHOW TABLES LIKE 'posts'");
$tableExists = $stmt->rowCount() > 0;

if (!$tableExists) {
    require 'create_database.php';
    require 'import_blog_data.php';
}

if (isset($_GET['searchText'])) {
    $searchText = $_GET['searchText'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT posts.title, comments.body
                FROM posts
                INNER JOIN comments ON posts.id = comments.postId
                WHERE comments.body LIKE :searchText";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['searchText' => '%' . $searchText . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            echo "<h2 style='padding-left: 10px'>Результаты поиска:</h2>";
            foreach ($results as $result) {
                echo "<div class='post-block'>";
                echo "<h3>{$result['title']}</h3>";
                echo "<p>{$result['body']}</p>";
                echo "</div>";
            }
        } else {
            echo "<p style='padding-left: 10px'>Ничего не найдено</p>";
        }

    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
    }
} else {
    echo "<p>Введите текст для поиска.</p>";
}
?>
