<?php 

session_start();

$db = mysqli_connect('db', 'user', 'secret', 'rcs13-db');

function cleanup($value): string
{
    global $db;
    if (!isset($value)) 
        return '';

    $value = strip_tags($value);
    $value = mysqli_real_escape_string($db, $value);

    return $value;
}

if (mysqli_connect_errno()) {
    $error = mysqli_connect_error();
}

$parole = 'secret';

if (!empty($_POST['name'])) {
    $_SESSION['name'] = $_POST['name'];
}

if (isset($_POST['logout']) && $_POST['logout'] === 'true') {
    session_unset();
}

if (isset($_POST['type']) && $_POST['type'] === 'cookie') {
    setcookie($_POST['name'], $_POST['value'], time() + intval($_POST['max_age'] ?? 60));
}

if (isset($_POST['type']) && $_POST['type'] === 'file' && !empty($_POST['contents'])) {
    file_put_contents('storage.txt', $_POST['contents'], FILE_APPEND | LOCK_EX); date('d.m.Y H:i:s');
}

if (isset($_POST['type']) && $_POST['type'] === 'newArticle') {
    $title = cleanup($_POST['title']);
    $image = cleanup($_POST['image_url']);
    $body = cleanup($_POST['contents']);

    mysqli_query($db, "INSERT INTO `articles` (`title`, `image_url`, `body`) VALUES ('$title', '$image', '$body')");
}

if (isset($_POST['type']) && $_POST['type'] === 'editArticle') {
    $id = cleanup($_POST['id']);
    $title = cleanup($_POST['title']);
    $image = cleanup($_POST['image_url']);
    $body = cleanup($_POST['contents']);

    mysqli_query($db, "UPDATE `articles` SET `title` = '$title', `image_url` = '$image', `body` = '$body' WHERE `id`= $id");
}


if (isset($_GET['delete'])) {
    $id = cleanup($_GET['delete']);

    mysqli_query($db, "DELETE FROM `articles` WHERE `id`= $id");
}


// mysqli_query($db, "INSERT INTO `articles` (`title`, `image_url`, `body`) VALUES ('Generated article', '', '".date('d.m.Y H:i:s')."')");

$result = mysqli_query($db, 'SELECT * FROM `articles`');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Sveiciens, <?= $_SESSION['name'] ?? 'nenosauktais' ?></h1>
    <h3>Login</h3>
    <form action="./index.php" method="post">
        <input type="text" name="name">
        <input type="password" name="password">
        <input type="submit" value="Log in">
    </form>

    <form action="./index.php" method="post">
        <input type="hidden" name="logout" value="true">
        <input type="submit" value="Logout">
    </form>

    <h3>Cookie</h3>
    <form action="./index.php" method="post">
        <input type="hidden" name="type" value="cookie">
        <input type="text" name="name" required>
        <input type="text" name="value">
        <input type="text" name="max_age">
        <input type="submit" value="Store">
    </form>

    <h3>File</h3>
    <form action="./index.php" method="post">
        <input type="hidden" name="type" value="file">
        <textarea name="contents" id=""></textarea>
        <input type="submit" value="Store">
    </form>

    <h3>MySQL</h3>
    <form action="./index.php" method="post">
        <input type="hidden" name="type" value="newArticle">
        <input type="text" name="title">
        <input type="text" name="image_url">
        <textarea name="contents" id=""></textarea>
        <input type="submit" value="Store">
    </form>

    <?php 
    $editableArticle = [];

     if (!empty($_GET['edit'])) {
        $id = cleanup($_GET['edit']);
        $editResult = mysqli_query($db, "SELECT * FROM `articles` WHERE `id` = $id");

        if (mysqli_num_rows($editResult) > 0) {
            $editableArticle = mysqli_fetch_assoc($editResult);
        }

     }
    
    ?>
    <h3>MySQL edit</h3>
    <form action="./index.php" method="post">
        <input type="hidden" name="type" value="editArticle">
        <input type="text" name="id" value="<?= $editableArticle['id'] ?? '' ?>">
        <input type="text" name="title" value="<?= $editableArticle['title'] ?? '' ?>">
        <input type="text" name="image_url" value="<?= $editableArticle['image_url'] ?? '' ?>">
        <textarea name="contents" id=""><?= $editableArticle['body'] ?? '' ?></textarea>
        <input type="submit" value="Update">
    </form>

    <pre><?= var_dump($_COOKIE) ?></pre>

    <p><?= file_get_contents('storage.file.txt')?></p>
    <?php if (!empty($error)) { ?>
        <h1 style="color:red;">Neizdevās pieslēgties MySQL: <?= $error ?></h1>
    <?php } ?>

    <table>
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['id']?></td>
            <td><?= $row['title']?></td>
            <td><?= $row['image_url']?></td>
            <td><?= $row['body']?></td>
            <td><a href="?edit=<?= $row['id']?>"><button>Edit</button></a></td>
            <td><a href="?delete=<?= $row['id']?>"><button>Delete</button></a></td>
        </tr>
    <?php } ?>
    </table>
</body>
</html>
<?php 
    mysqli_close($db);
?>