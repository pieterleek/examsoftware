<?php
$db_host = "pieterleek.nl";
$db_user = ""; 
$db_pass = "Paashaas888";
$db_name = ""; 

// Je kan inloggen op de database via phpMyAdmin: https://pieterleek.nl/phpmyadmin/ met dezelfde inloggegevens als hierboven.

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    

    $query = "SELECT * FROM accounts WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $success = "Welkom, " . $user["username"] . "! Je bent ingelogd.";
    } else {
        $error = "Ongeldige gebruikersnaam of wachtwoord.";
    }
}
?>