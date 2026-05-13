<?php
session_start();
require_once __DIR__ . '/../Models/PerfilClinico.php';

class PerfilClinicoController
{
    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Requisito básico: ter certeza de que há um cliente logado na sessão
            if (!isset($_SESSION['cliente_id'])) {
                header('Location: ../Views/cadastro.php?nura_ft=info&nura_flash=' . rawurlencode('Faça login para salvar as informações do perfil clínico.'));
                exit;
            }

            // Coleta os dados do formulário
            $cliente_id = $_SESSION['cliente_id'];
            $peso = $_POST['peso'] ?? null;
            $altura = $_POST['altura'] ?? null;
            $restricao = $_POST['restricao'] ?? null;
            $alergias = $_POST['alergias'] ?? [];

            // Instancia o Model (que agora conecta ao banco oficial Nura_Db)
            $perfil = new PerfilClinico();
            $perfil->setClienteId($cliente_id);
            $perfil->setPeso($peso);
            $perfil->setAltura($altura);
            $perfil->setRestricao($restricao);
            $perfil->setAlergias($alergias);

            // Verifica se este cliente já tem algum Perfil Salvo no banco de dados
            $perfilExistente = PerfilClinico::buscarPorClienteId($cliente_id);

            if ($perfilExistente) {
                // Se já existia a linha no banco, faz um UPDATE
                $sucesso = $perfil->atualizar();
            } else {
                // Se não tinha (primeira vez salvando), faz um INSERT
                $sucesso = $perfil->salvar();
            }

            if ($sucesso) {
                // Quando salvo no banco com sucesso, ATUALIZA A SESSÃO para aparecer instantaneamente
                $_SESSION['perfil_clinico'] = [
                    'peso' => $peso,
                    'altura' => $altura,
                    'restricao' => $restricao,
                    'alergias' => $alergias
                ];
                header('Location: ../Views/perfil.php?nura_ft=success&nura_flash=' . rawurlencode('Perfil clínico salvo com sucesso.'));
            } else {
                header('Location: ../Views/perfil.php?nura_ft=error&nura_flash=' . rawurlencode('Erro ao salvar. Verifique os dados ou a configuração do banco.'));
            }
            exit;
        }
    }

    public function excluir()
    {
        if (!isset($_SESSION['cliente_id'])) {
            header('Location: ../Views/cadastro.php');
            exit;
        }

        $cliente_id = $_SESSION['cliente_id'];

        // Remove diretamente do Banco de Dados
        $sucesso = PerfilClinico::deletar($cliente_id);

        if ($sucesso) {
            // Remove da Sessão local do usuário
            unset($_SESSION['perfil_clinico']);
            header('Location: ../Views/perfil.php?nura_ft=success&nura_flash=' . rawurlencode('Perfil clínico removido com sucesso.'));
        } else {
            header('Location: ../Views/perfil.php?nura_ft=error&nura_flash=' . rawurlencode('Não foi possível excluir o perfil clínico.'));
        }
        exit;
    }
}

// Roteador simples
if (isset($_GET['acao'])) {
    $controller = new PerfilClinicoController();
    $acao = $_GET['acao'];

    if ($acao === 'salvar') {
        $controller->salvar();
    } elseif ($acao === 'excluir') {
        $controller->excluir();
    }
}
?>