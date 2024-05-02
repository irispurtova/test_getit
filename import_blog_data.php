<?php

function fetchJSON($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$postsUrl = 'https://jsonplaceholder.typicode.com/posts';
$commentsUrl = 'https://jsonplaceholder.typicode.com/comments';

$posts = fetchJSON($postsUrl);

$comments = fetchJSON($commentsUrl);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test_task";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($posts as $post) {
        $existingPost = $conn->prepare("SELECT id FROM posts WHERE id = :id");
        $existingPost->execute(['id' => $post['id']]);
        $result = $existingPost->fetch();

        if (!$result) {
            $sql = "INSERT INTO posts (id, userId, title, body) VALUES (:id, :userId, :title, :body)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'id' => $post['id'],
                'userId' => $post['userId'],
                'title' => $post['title'],
                'body' => $post['body']
            ]);
        }
    }

    foreach ($comments as $comment) {
        $existingComment = $conn->prepare("SELECT id FROM comments WHERE id = :id");
        $existingComment->execute(['id' => $comment['id']]);
        $result = $existingComment->fetch();

        if (!$result) {
            $sql = "INSERT INTO comments (id, postId, name, email, body) VALUES (:id, :postId, :name, :email, :body)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'id' => $comment['id'],
                'postId' => $comment['postId'],
                'name' => $comment['name'],
                'email' => $comment['email'],
                'body' => $comment['body']
            ]);
        }
    }

    $countPosts = count($posts);
    $countComments = count($comments);
    $message = "Загружено $countPosts записей и $countComments комментариев";
    error_log($message, 0);

} catch(PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}


?>
