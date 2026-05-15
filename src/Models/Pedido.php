<?php
require_once __DIR__ . '/../config.php';

class Pedido
{
    private $id;
    private $cliente_id;
    private $total;
    private $subtotal;
    private $frete;
    private $endereco;
    private $metodo_pagamento;
    private $dados_pagamento;
    private $itens;
    private $status;
    private $created_at;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getClienteId() { return $this->cliente_id; }
    public function setClienteId($cliente_id) { $this->cliente_id = $cliente_id; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getSubtotal() { return $this->subtotal; }
    public function setSubtotal($subtotal) { $this->subtotal = $subtotal; }

    public function getFrete() { return $this->frete; }
    public function setFrete($frete) { $this->frete = $frete; }

    public function getEndereco() { return $this->endereco; }
    public function setEndereco($endereco) { $this->endereco = $endereco; }

    public function getMetodoPagamento() { return $this->metodo_pagamento; }
    public function setMetodoPagamento($metodo) { $this->metodo_pagamento = $metodo; }

    public function getDadosPagamento() { return $this->dados_pagamento; }
    public function setDadosPagamento($dados) { $this->dados_pagamento = $dados; }

    public function getItens() { return json_decode($this->itens ?? '[]', true) ?: []; }
    public function setItens($itens) {
        if (is_array($itens)) {
            $this->itens = json_encode($itens);
        } else {
            $this->itens = $itens;
        }
    }

    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }

    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }

    public function salvar()
    {
        global $pdo;
        try {
            date_default_timezone_set('America/Sao_Paulo');
            $this->created_at = date('Y-m-d H:i:s');

            // Lógica de criptografia AES-256 para dados sensíveis
            $dadosCriptografados = null;
            if (!empty($this->dados_pagamento)) {
                $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
                $encrypted = openssl_encrypt($this->dados_pagamento, 'aes-256-cbc', NURA_SECRET_KEY, 0, $iv);
                // Salva o IV junto com o dado criptografado separado por :
                $dadosCriptografados = base64_encode($encrypted . '::' . $iv);
            }

            $sql = "INSERT INTO pedidos (cliente_id, total, subtotal, frete, endereco, metodo_pagamento, dados_pagamento, itens, status, created_at) 
                    VALUES (:cliente_id, :total, :subtotal, :frete, :endereco, :metodo_pagamento, :dados_pagamento, :itens, :status, :created_at)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':cliente_id', $this->cliente_id);
            $stmt->bindValue(':total', $this->total);
            $stmt->bindValue(':subtotal', $this->subtotal ?? 0);
            $stmt->bindValue(':frete', $this->frete ?? 0);
            $stmt->bindValue(':endereco', $this->endereco ?? '');
            $stmt->bindValue(':metodo_pagamento', $this->metodo_pagamento ?? 'PIX');
            $stmt->bindValue(':dados_pagamento', $dadosCriptografados);
            $stmt->bindValue(':itens', $this->itens);
            $stmt->bindValue(':status', $this->status ?? 'Em Preparo');
            $stmt->bindValue(':created_at', $this->created_at);
            $stmt->execute();
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao salvar Pedido: " . $e->getMessage());
            return false;
        }
    }

    public static function buscarPorId($id) {
        global $pdo;
        $sql = "SELECT * FROM pedidos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function atualizarStatus($id, $novoStatus) {
        global $pdo;
        $sql = "UPDATE pedidos SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':status', $novoStatus);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public static function buscarPorClienteId($cliente_id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE cliente_id = :cliente_id ORDER BY created_at DESC");
        $stmt->bindValue(':cliente_id', $cliente_id);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultados as &$r) {
            $r['id'] = (int) $r['id'];
            $r['total'] = (float) $r['total'];
            $r['subtotal'] = (float) ($r['subtotal'] ?? 0);
            $r['frete'] = (float) ($r['frete'] ?? 0);
            $r['itens'] = json_decode($r['itens'] ?? '[]', true) ?: [];
        }
        return $resultados;
    }
}
?>
