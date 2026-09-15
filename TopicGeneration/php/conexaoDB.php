<?php
    $serverName = "localhost";
    $username = "root";
    $password = "";
    $DBName = "topicgeneration";

    if(!isset($conn)) {
        try {
            $conn = new PDO("mysql:host=$serverName;dbname=$DBName", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            function sanitize($string){
                global $conn;
                $string = preg_replace('/[^A-Za-z\s]/', '', $string);
                $string = htmlentities($string);
                $string = str_replace('%EF%BF%BD', '', $string);
                $string = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $string);
                $string = filter_var($string, FILTER_SANITIZE_STRING);
                $string = $conn->quote($string);
                return $string;
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
?>