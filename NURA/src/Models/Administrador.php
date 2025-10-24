<?php
require_once __DIR__ . '/Usuario.php';
require_once __DIR__ . '/Produto.php';
require_once __DIR__ . '/Pedido.php';

class Administrador extends Usuario
{
    private $nivelAcesso;

    public function __construct($id, $nome, $email, $senha, $nivelAcesso)
    {
        parent::__construct($id, $nome, $email, $senha);
        $this->nivelAcesso = $nivelAcesso;
    }

    public function getNivelAcesso()
    {
        return $this->nivelAcesso;
    }

    public function setNivelAcesso($nivelAcesso)
    {
        $this->nivelAcesso = $nivelAcesso;
    }

    public function ExibirInfo()
    {
        parent::ExibirInfo();
        echo ", Nível de Acesso: $this->nivelAcesso<br><br>";
    }
}
?>
