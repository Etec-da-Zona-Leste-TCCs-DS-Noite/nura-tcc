<?php
class Produto
{
    private $id;
    private $nome;
    private $preco;

    public function __construct($id, $nome, $preco)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->preco = $preco;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getPreco()
    {
        return $this->preco;
    }

    public function setPreco($preco)
    {
        $this->preco = $preco;
    }

    public function ExibirInfo()
    {
        echo "ID: $this->id, Nome: $this->nome, Preço: $this->preco";
    }

    public function ListaProdutos($produtos)
    {
        foreach ($produtos as $produto) {
            $produto->ExibirInfo();
            echo "<br>";
        }
    }
}
?>
