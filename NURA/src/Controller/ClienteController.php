<?php
require_once __DIR__ . '/../Models/Cliente.php';

class ClienteController
{
    public function exibir()
    {
        $cliente = new Cliente(2, "Wilson", "wilson@gmail.com", "1715", "Rua João Augusto Morais, 348", "+55 (11) 94569-9696");
        return $cliente;
    }
}
?>
