<?php
require_once __DIR__ . '/../config.php';

class Usuario
{
    private $id;
    private $nome;
    private $email;
    private $senha; // A senha ficará guardada aqui

    // Getters e Setters
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setNome($nome)
    {
        $this->nome = $nome;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }

    // SetSenha: Só define se vier algo escrito
    public function setSenha($senha)
    {
        if (!empty($senha)) {
            $this->senha = password_hash($senha, PASSWORD_DEFAULT);
        }
    }

    public function salvar()
    {
        global $pdo;
        try {
            $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nome', $this->nome);
            $stmt->bindValue(':email', $this->email);
            $stmt->bindValue(':senha', $this->senha);
            $stmt->execute();
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function buscarPorId($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscarPorEmail($email)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ATUALIZAR INTELIGENTE (Com ou Sem Senha)
    public function atualizar()
    {
        global $pdo;
        try {
            // Se a senha estiver preenchida, atualiza ela também
            if (!empty($this->senha)) {
                $sql = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':senha', $this->senha);
            } else {
                // Se não, atualiza só nome e email
                $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
                $stmt = $pdo->prepare($sql);
            }

            $stmt->bindValue(':nome', $this->nome);
            $stmt->bindValue(':email', $this->email);
            $stmt->bindValue(':id', $this->id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function deletar($id)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>