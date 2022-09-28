<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0,user-scalable=0">
    <meta name="description" content="">
    <title>3D Viewer</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body style="background-color:#000000;">

<?php

$glbfiles = getFiles("models", "glb");

foreach ($glbfiles as $glbfile) {
    echo '<a href="view.php?model=' . $glbfile . '&format=glb" type="button" class="btn btn-primary">' . $glbfile . "</a>&nbsp;";
}

// get all files with extension "glb" from directory. return the filenames without extension
function getFiles($dir, $ext) {
    $files = array();
    if ($handle = opendir($dir)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $file = pathinfo($entry);
                if ($file['extension'] == $ext) {
                    // if filename contains other signs than letters, numbers, dashes and underscores, it will be ignored
                    if (preg_match('/^[a-zA-Z0-9-_]+$/', $file['filename'])) {
                        array_push($files, $file['filename']);
                    }
                }
            }
        }
        closedir($handle);
    }
    return $files;
}
?>
</body>
</html>