<?php
require_once __DIR__ . '/../Models/Administrador.php';

class AdministradorController
{
    public function exibir()
    {
        $adm = new Administrador(1, "Jailson", "jailson@gmail.com", "1234", "Total");
        return $adm;
    }
}
?>
