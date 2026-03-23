<?php
require_once __DIR__ . '/../config.php';

class PerfilClinico
{
    private $id;
    private $cliente_id;
    private $peso;
    private $altura;
    private $restricao;
    private $alergias;
    private $created_at;
    private $updated_at;

    // Getters and Setters Originais + Novos Campos
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }

    public function setClienteId($cliente_id)
    {
        $this->cliente_id = $cliente_id;
    }
    public function getClienteId()
    {
        return $this->cliente_id;
    }

    public function setPeso($peso)
    {
        $this->peso = $peso;
    }
    public function getPeso()
    {
        return $this->peso;
    }

    public function setAltura($altura)
    {
        $this->altura = $altura;
    }
    public function getAltura()
    {
        return $this->altura;
    }

    public function setRestricao($restricao)
    {
        $this->restricao = $restricao;
    }
    public function getRestricao()
    {
        return $this->restricao;
    }

    public function setAlergias($alergias)
    {
        // Converte o Array vindo do form em JSON para o banco.
        if (is_array($alergias)) {
            $this->alergias = json_encode($alergias);
        } else {
            $this->alergias = $alergias;
        }
    }
    public function getAlergias()
    {
        // Devolve o JSON convertido em Array.
        return json_decode($this->alergias ?? '[]', true) ?: [];
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /* =======================================================
     CRUD OFICIAL LIGADO AO BANCO DE DADOS (nura_db)
     100% BLINDADO CONTRA ERROS E EXCEÇÕES (PDOException)
     ======================================================= */

    public function salvar()
    {
        global $pdo;
        try {
            // A data de criação e atualização é gerada automaticamente pelo NOW()
            $sql = "INSERT INTO perfil_clinico (cliente_id, peso, altura, restricao, alergias, created_at, updated_at) 
                    VALUES (:cliente_id, :peso, :altura, :restricao, :alergias, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':cliente_id', $this->cliente_id);
            $stmt->bindValue(':peso', $this->peso);
            $stmt->bindValue(':altura', $this->altura);
            $stmt->bindValue(':restricao', $this->restricao);
            $stmt->bindValue(':alergias', $this->alergias);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Registra o erro no log e retorna false para o sistema não "cair"
            error_log("Erro ao salvar PerfilClinico: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar()
    {
        global $pdo;
        try {
            // Updated_at é renovado com NOW() para registrar que houve modificação
            $sql = "UPDATE perfil_clinico 
                    SET peso = :peso, 
                        altura = :altura, 
                        restricao = :restricao, 
                        alergias = :alergias,
                        updated_at = NOW()
                    WHERE cliente_id = :cliente_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':peso', $this->peso);
            $stmt->bindValue(':altura', $this->altura);
            $stmt->bindValue(':restricao', $this->restricao);
            $stmt->bindValue(':alergias', $this->alergias);
            $stmt->bindValue(':cliente_id', $this->cliente_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar PerfilClinico: " . $e->getMessage());
            return false;
        }
    }

    public static function buscarPorClienteId($cliente_id)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT * FROM perfil_clinico WHERE cliente_id = :cliente_id");
            $stmt->bindValue(':cliente_id', $cliente_id);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            // Decodifica a string JSON de Volta para um Array do PHP (se houver)
            if ($resultado && !empty($resultado['alergias'])) {
                $resultado['alergias'] = json_decode($resultado['alergias'], true) ?: [];
            } else if ($resultado) {
                $resultado['alergias'] = [];
            }

            return $resultado ?: null;

        } catch (PDOException $e) {
            error_log("Erro ao buscar PerfilClinico: " . $e->getMessage());
            // Se der qualquer erro crítico (ex: tabela sumiu), passamos null limpo.
            return null;
        }
    }

    public static function deletar($cliente_id)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("DELETE FROM perfil_clinico WHERE cliente_id = :cliente_id");
            $stmt->bindValue(':cliente_id', $cliente_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar PerfilClinico: " . $e->getMessage());
            return false;
        }
    }
}
?>