<?php

//List all content of a folder
function readFolder( $path ) {
    // Open the folder
    if ( !( $dir = opendir( $path ) ) ) {
        echo "Can't open $path";
        echo "<a href='/'>Go to index</a>";

        die;
    }
    
    $filenames = array();

    // Read the contents of the folder, ignoring '.' and '..', and
    // appending '/' to any subfolder names. Add all the files and
    // subfolders to the $filenames array.

    while ( $filename = readdir( $dir ) ) {
        if ( $filename != '.' && $filename != '..' ) {
            if ( is_dir( "$path/$filename" ) ) $filename .= '/';
            $filenames[] = $filename;
        }
    }

    closedir ( $dir );

    // Sort the filenames in alphabetical order
    sort( $filenames );

    // Display the filenames, and process any subfolders

    echo "<ul>";

    foreach ( $filenames as $filename ) {
        echo "<li>$filename";
        echo "<a href='?action=edit&file=" . $path . "/" . $filename . "'>Editer</a> || ";
        echo "<a href='?action=delete&file=" . $path . "/" . $filename . "'>Supprimer</a>";
        if ( substr( $filename, -1 ) == '/' ) readFolder( "$path/" . substr( $filename, 0, -1 ) );
        
        echo "</li>";
    }

    echo "</ul>";
}

//Delete a file / folder
function deletePath($path) {
    if (is_file($path)) {
        unlink($path);

        return 1;
    }

    // Open the folder
    if ( !( $dir = opendir( $path ) ) ) return 0;

    while ( $filename = readdir( $dir ) ) {
        if ( $filename != '.' && $filename != '..' ) {
            if ( is_dir( "$path/$filename" ) ) {
                deletePath("$path/$filename");
            } else {
                unlink( "$path/$filename" );
            }
        }
    }

    rmdir($path);

    closedir ( $dir );

    return 1;
}

function editPath(string $path) {
    if (is_dir($path)) {
        echo "Can't edit a folder<br/>";
        echo "<a href='/'>Go to index</a>";

        return 0;
    }

    //all extension files allowed to be edited
    $editableExtensions = ['txt', 'php', 'html', 'css', 'js'];

    $pathParts = pathinfo($path);

    if (in_array($pathParts['extension'], $editableExtensions)) {
        echo "<form action = '?action=save&file=" . $path . "' method='post'>";
        echo "<textarea style='width: 100%;' cols='30' rows='15' name='content'>" . file_get_contents($path) . "</textarea>";
        echo "<input type='submit' value='Enregistrer'/>";
        echo "</form>";
    }
}

function savePath(string $path, string $content) {
    if (is_dir($path)) {
        echo "Can't edit a folder<br/>";
        echo "<a href='/'>Go to index</a>";

        return 0;
    }

    if (!is_writable($path)) {
        echo "Can't edit this file ($path)<br/>";
        echo "<a href='/'>Go to index</a>";

        return 0;
    }

    if (!$fp = fopen($path, 'c+')) {
        echo "Can't open this file ($path)<br/>";
        echo "<a href='/'>Go to index</a>";

        return 0;
    }

    //delete the file content
    ftruncate($fp, 0);

    //write content in the file
    if (fwrite($fp, $content) === FALSE) {
        echo "Can't write in this file ($path)<br/>";
        echo "<a href='/'>Go to index</a>";

        return 0;
    }

    fclose($fp);

    echo "The file ($path) has been edited<br/>";
    echo "<a href='/'>Go to index</a>";
}