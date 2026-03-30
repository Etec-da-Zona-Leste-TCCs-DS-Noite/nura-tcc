<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Produto.php';

class ProdutoController
{
    /**
     * Instancia um novo Produto e salva no BD
     */
    public function criar($dados)
    {
        $produto = new Produto();
        $produto->setNome($dados['nome']);
        $produto->setDescricao($dados['descricao'] ?? $dados['desc'] ?? '');
        $produto->setPreco($dados['preco']);
        $produto->setImg($dados['img']);
        $produto->setTag($dados['tag']);
        $produto->setAlergias($dados['alergias'] ?? []);
        $produto->setRestricoes($dados['restricoes'] ?? []);

        return $produto->salvar();
    }

    /**
     * Retorna o Array mapeado com todos os produtos
     */
    public function listarTodos()
    {
        return Produto::buscarTodos();
    }

    /**
     * Retorna a array de um Produto Específico
     */
    public function buscarUm($id)
    {
        return Produto::buscarPorId($id);
    }

    /**
     * Atualiza um Produto e Salva no DB
     */
    public function atualizar($id, $dados)
    {
        $produto = new Produto();
        $produto->setId($id);
        $produto->setNome($dados['nome']);
        $produto->setDescricao($dados['descricao'] ?? $dados['desc'] ?? '');
        $produto->setPreco($dados['preco']);
        $produto->setImg($dados['img']);
        $produto->setTag($dados['tag']);
        $produto->setAlergias($dados['alergias'] ?? []);
        $produto->setRestricoes($dados['restricoes'] ?? []);

        return $produto->atualizar();
    }

    /**
     * Apaga Produto Permanentemente do BD
     */
    public function deletar($id)
    {
        return Produto::deletar($id);
    }
}
?>