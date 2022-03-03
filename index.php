<?php include('inc/head.php'); ?>

<?php
    $folderPath = "files";

    switch ($action) {
        case 'delete':
            deletePath($_GET['file']);

            header("Location: /");
            break;

        case 'edit':
            editPath($_GET['file']);

            break;
        case 'save':
            savePath($_GET['file'], $_POST['content']);

            break;
        default:
            readFolder($folderPath);
    }
?>

<?php include('inc/foot.php'); ?>