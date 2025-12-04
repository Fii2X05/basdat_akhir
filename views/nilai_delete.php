<?php
require '../config.php';

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM nilai WHERE id=$id");

header("Location: nilai_list.php");
