<?php

$db = mysqli_connect('db', 'user', 'secret', 'rcs13-db');

function createArticle(array $data)
{
    global $db;

    $title = cleanup($data['title']);
    $image = cleanup($data['image_url']);
    $body = cleanup($data['contents']);

    mysqli_query($db, "INSERT INTO `articles` (`title`, `image_url`, `body`) VALUES ('$title', '$image', '$body')");
}

function editArticle($id)
{
    global $db;

    $id = cleanup($id);
    $editResult = mysqli_query($db, "SELECT * FROM `articles` WHERE `id` = $id");

    if (mysqli_num_rows($editResult) > 0) {
        return mysqli_fetch_assoc($editResult);
    }

    return [];
}

function updateArticle(array $data)
{
    global $db;

    $id = cleanup($data['id']);
    $title = cleanup($data['title']);
    $image = cleanup($data['image_url']);
    $body = cleanup($data['contents']);

    mysqli_query($db, "UPDATE `articles` SET `title` = '$title', `image_url` = '$image', `body` = '$body' WHERE `id`= $id");

}

function deleteArticle(array $data)
{
    global $db;

    $id = cleanup($data['id']);
    mysqli_query($db, "DELETE FROM `articles` WHERE `id`= $id");
}

function articlesResult()
{
    global $db;
    return mysqli_query($db, 'SELECT * FROM `articles`');
}

function cleanup($value): string
{
    global $db;
    if (!isset($value)) 
        return '';

    $value = strip_tags($value);
    $value = mysqli_real_escape_string($db, $value);

    return $value;
}