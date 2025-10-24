<?php
class Pedido
{
    private $id;
    private $valorTotal;
    private $cliente;

    public function __construct($id, $valorTotal, $cliente)
    {
        $this->id = $id;
        $this->valorTotal = $valorTotal;
        $this->cliente = $cliente;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
    }

    public function getCliente()
    {
        return $this->cliente;
    }

    public function ExibirInfo()
    {
        echo "<p>ID do Pedido: {$this->id}<br>Valor Total: {$this->valorTotal}</p>";
        echo "<h4>Informações do Cliente:</h4>";
        $this->cliente->ExibirInfo();
    }
}
?>
