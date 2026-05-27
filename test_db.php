<?php
require 'src/config.php';
$stmt = $pdo->query('SELECT * FROM admin');
print_r($stmt->fetchAll());
