<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
require_once("functions.php");

if(!isset($_REQUEST['debug'])){
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
}else{
error_reporting(E_ALL);
ini_set('display_errors', '1');	
}

$host = 'localhost';
$dbUser = 'root';
//$dbUser = 'root';
$dbPass = '';

$dbUso = 'sw3t';
//$dbUso = 'm_prod';

$link = mysqli_connect ("$host", "$dbUser", "$dbPass","$dbUso");
 
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    die;
}
mysqli_set_charset($link,'utf8');

$titulo = "sw3t";
$title = $titulo;
$footer = "2019 © ";

$salt0="DdnSqmnAVFfK4sr9T3D3DVeezpQQAgBFLvCD6YHcDeBD7TEbb4ShDU39ynj4nSbZVbMhjQuXWWWERCACZgDpAjZyVpaqNfHJhsksJwAWncrJUst6TgFn4Vfp5A8fzHYS";
$salt1="LuuuKq7pnfv5CjDNRPASw7k2wEWNeYsUpAdPQ7LvVhR5Yyrzhz8ZYCDwWzKHJFfYCDKLgsb3htsN5Lt6M8zaJYdawdRgU8WsuMBhJd6PxXnbqELbCBxQFwWu2wy6eWVH";
$salt2="cvxynSWWyvWStTkXCQ9xZP5ffGbvjrYJ7AtKuY57AnynVJeBcDCPCa5vFqyDFkttmLLQpQnmgh4fPk58ZHmvRCJ2GJyL9uVWqrn7RQFRNEQ3aHfD7zMwyhT9cYw8f72C";
$salt3="FtS2HAxdh98ScxGMy8pfegQs2VGGhFLdjeX7LJMjFWmJFTdYbymxZwnXRbBXEFCVhdBnAHnavBbQSz8ZQrdLy8cpEytXYe7yePWdwycqe8N3GnXLTSVYBwzqrznWYFbq";
$salt4="8Q3Ve2yeeS2BuqwKJ6uHRMnBVDsbT8qJEPp3BRq8XHhvFJRsuFKY277rp5ZddtWN9kYbA9rKaKavXj4wLNhgGkS93ddF2upBE9sEgMs8mdPuvD77s3f5WGz49V433xqp";

$dev=true;//settar pra true para tentar bypassar alguns caches

// echo "a";
$sw3tLocation = dirname(__FILE__)."/";
$sw3tLocation = str_replace('\\','/',$sw3tLocation);
// echo $sw3tLocation;die;

// $sw3tUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http"). "://$_SERVER[HTTP_HOST]/";
$sw3tUrl = "http://$_SERVER[HTTP_HOST]/";

$presetQtdTopico= 10;
$presetQtdPalavras= 8;
// $justificativa != "titulo") and ($justificativa != "resumo") and ($justificativa != "conteudo") and ($justificativa != "topico") and ($justificativa != "duplicado")
$justificativasMotivo[]="titulo";
$justificativasMotivo[]="resumo";
$justificativasMotivo[]="conteudo";
$justificativasMotivo[]="topico";
$justificativasMotivo[]="duplicado";
$justificativasMotivo[]="area_conhecimento";
$justificativasMotivo[]="duplicado resumo";
$justificativasMotivo[]="duplicado titulo";
$justificativasMotivo[]="botao";

?>