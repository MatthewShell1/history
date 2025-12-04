<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$db_host = "localhost";
$db_username = "root";
$db_password = "Nousirons1";
$db_database = "history";
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
session_start();
date_default_timezone_set("America/New_York");
$date = date('Y-m-d H:i:s', time());
$current_time = new DateTime();
$ip = $_SERVER['REMOTE_ADDR'];
$agent = $_SERVER['HTTP_USER_AGENT'];
?>
<!DOCTYPE html>
<html>

<head>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-393NP1S717"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-393NP1S717');
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Permanent+Marker&display=swap" rel="stylesheet">




  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://kit.fontawesome.com/29d81e24a9.js" crossorigin="anonymous" SameSite="none Secure"></script>
  <title>Life In Time</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>