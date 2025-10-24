<?php
require_once __DIR__ . '/../Models/Produto.php';

class ProdutoController
{
    public function exibir()
    {
        $produto = new Produto(3, "Banana", 5.68);
        return $produto;
    }
}
?>
