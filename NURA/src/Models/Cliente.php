<?php
require_once __DIR__ . '/Usuario.php';

class Cliente extends Usuario
{
    private $endereco;
    private $telefone;

    public function __construct($id, $nome, $email, $senha, $endereco, $telefone)
    {
        parent::__construct($id, $nome, $email, $senha);
        $this->endereco = $endereco;
        $this->telefone = $telefone;
    }

    public function getEndereco()
    {
        return $this->endereco;
    }

    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
    }

    public function getTelefone()
    {
        return $this->telefone;
    }

    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }

    public function ExibirInfo()
    {
        parent::ExibirInfo();
        echo ", Endereço: $this->endereco, Telefone: $this->telefone";
    }
}
?>
