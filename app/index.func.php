<?php 

include 'functions.php';

session_start();

$type = $_POST['type'] ?? '';

$editableArticle = !empty($_GET['edit']) ? editArticle($_GET['edit']) : [];

switch($type) {
    case 'createArticle':
        createArticle($_POST);
        break;
    case 'updateArticle':
        updateArticle($_POST);
        break;
    case 'deleteArticle':
        deleteArticle($_POST);
        break;
    default:
        break;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h3>MySQL create</h3>
    <form action="./index.php" method="post">
        <input type="hidden" name="type" value="createArticle">
        <input type="text" name="title">
        <input type="text" name="image_url">
        <textarea name="contents" id=""></textarea>
        <input type="submit" value="Store">
    </form>

    <h3>MySQL edit</h3>
    <form action="./index.php" method="post">
        <input type="hidden" name="type" value="updateArticle">
        <input type="text" name="id" value="<?= $editableArticle['id'] ?? '' ?>">
        <input type="text" name="title" value="<?= $editableArticle['title'] ?? '' ?>">
        <input type="text" name="image_url" value="<?= $editableArticle['image_url'] ?? '' ?>">
        <textarea name="contents" id=""><?= $editableArticle['body'] ?? '' ?></textarea>
        <input type="submit" value="Update">
    </form>

    <?php if (!empty($error)) { ?>
        <h1 style="color:red;">Neizdevās pieslēgties MySQL: <?= $error ?></h1>
    <?php } ?>

    <table>
    <?php 
        $result = articlesResult();
        while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['id']?></td>
            <td><?= $row['title']?></td>
            <td><?= $row['image_url']?></td>
            <td><?= $row['body']?></td>
            <td><a href="?edit=<?= $row['id']?>"><button>Edit</button></a></td>
            <td>
                <form action="./index.php" method="post">
                    <input type="hidden" name="type" value="deleteArticle">
                    <input type="hidden" name="id" value="<?= $row['id']?>">
                    <input type="submit" value="Delete">
                </form>
            </td>
        </tr>
    <?php } ?>
    </table>
</body>
</html>
<?php 
    // mysqli_close($db);
    $db->close();
?>