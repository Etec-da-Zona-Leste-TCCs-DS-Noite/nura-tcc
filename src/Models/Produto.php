<?php
require_once __DIR__ . '/../config.php';

class Produto
{
    private $id;
    private $nome;
    private $descricao;
    private $preco;
    private $img;
    private $tag;
    private $alergias;
    private $restricoes;

    // Getters e Setters padronizados
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNome()
    {
        return $this->nome;
    }
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getPreco()
    {
        return $this->preco;
    }
    public function setPreco($preco)
    {
        $this->preco = $preco;
    }

    public function getImg()
    {
        return $this->img;
    }
    public function setImg($img)
    {
        $this->img = $img;
    }

    public function getTag()
    {
        return $this->tag;
    }
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    public function getAlergias()
    {
        return json_decode($this->alergias ?? '[]', true) ?: [];
    }
    public function setAlergias($alergias)
    {
        if (is_array($alergias)) {
            $this->alergias = json_encode($alergias);
        } else {
            $this->alergias = $alergias;
        }
    }

    public function getRestricoes()
    {
        return json_decode($this->restricoes ?? '[]', true) ?: [];
    }
    public function setRestricoes($restricoes)
    {
        if (is_array($restricoes)) {
            $this->restricoes = json_encode($restricoes);
        } else {
            $this->restricoes = $restricoes;
        }
    }

    /* =======================================================
       OPERAÇÕES DE BANCO DE DADOS (CRUD)
       ======================================================= */

    public function salvar()
    {
        global $pdo;
        try {
            $sql = "INSERT INTO produtos (nome, descricao, preco, img, tag, alergias, restricoes) 
                    VALUES (:nome, :descricao, :preco, :img, :tag, :alergias, :restricoes)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nome', $this->nome);
            $stmt->bindValue(':descricao', $this->descricao);
            $stmt->bindValue(':preco', $this->preco);
            $stmt->bindValue(':img', $this->img);
            $stmt->bindValue(':tag', $this->tag);
            $stmt->bindValue(':alergias', $this->alergias);
            $stmt->bindValue(':restricoes', $this->restricoes);
            $stmt->execute();
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao salvar Produto: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar()
    {
        global $pdo;
        try {
            $sql = "UPDATE produtos 
                    SET nome = :nome, descricao = :descricao, preco = :preco, img = :img, tag = :tag, alergias = :alergias, restricoes = :restricoes 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nome', $this->nome);
            $stmt->bindValue(':descricao', $this->descricao);
            $stmt->bindValue(':preco', $this->preco);
            $stmt->bindValue(':img', $this->img);
            $stmt->bindValue(':tag', $this->tag);
            $stmt->bindValue(':alergias', $this->alergias);
            $stmt->bindValue(':restricoes', $this->restricoes);
            $stmt->bindValue(':id', $this->id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar Produto: " . $e->getMessage());
            return false;
        }
    }

    public static function buscarTodos()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM produtos ORDER BY id ASC");
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Formatar para exibição retrocompatível
        foreach ($resultados as &$r) {
            $r['id'] = (int) $r['id'];
            $r['preco'] = (float) $r['preco'];
            $r['alergias'] = json_decode($r['alergias'] ?? '[]', true) ?: [];
            $r['restricoes'] = json_decode($r['restricoes'] ?? '[]', true) ?: [];
            $r['desc'] = $r['descricao']; // Retorna "desc" também para a view não quebrar
        }
        return $resultados;
    }

    public static function buscarPorId($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $resultado['id'] = (int) $resultado['id'];
            $resultado['preco'] = (float) $resultado['preco'];
            $resultado['alergias'] = json_decode($resultado['alergias'] ?? '[]', true) ?: [];
            $resultado['restricoes'] = json_decode($resultado['restricoes'] ?? '[]', true) ?: [];
            $resultado['desc'] = $resultado['descricao'];
        }
        return $resultado ?: null;
    }

    public static function deletar($id)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = :id");
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>