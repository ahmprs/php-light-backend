
<?php
require_once __DIR__ . "/main.php";

$err_msg = "";
$target_dir = realpath(__DIR__ . "/../uploads/") . '\\';
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        // echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        $err_msg = "File is not an image.";
        $uploadOk = 0;
    }
}


// Check if file already exists
if (file_exists($target_file)) {
    $err_msg = "file already exists";
    $uploadOk = 0;
}


// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    $err_msg = 'Sorry, your file is too large.';
    $uploadOk = 0;
}


// Allow certain file formats
if (
    $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "pdf"
    && $imageFileType != "gif"
) {
    $err_msg = 'invalid format';
    $uploadOk = 0;
}


// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    resp(0, $err_msg);
} else {
    // Test Point
    // resp(0, $_FILES["fileToUpload"]["tmp_name"]);
    // resp(0, $target_file);
    // return;

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        resp(1, 'file ' . basename($_FILES["fileToUpload"]["name"]) . ' upload succeeded');
    } else {
        $err_msg = 'file copy to destination folder failed.';
        resp(0, $err_msg);
    }
}


/*
How to use:

		<form action="./srv/core/upload.php" method="post" enctype="multipart/form-data">
			Select image to upload:
			<input type="file" name="fileToUpload" id="fileToUpload" />
			<input type="submit" value="Upload Image" name="submit" />
		</form>
*/
?>
