<?php
session_start();
require_once __DIR__ . '/../Models/Cliente.php';

class ClienteController
{

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $dadosCliente = Cliente::buscarPorEmail($email);

            if ($dadosCliente && password_verify($senha, $dadosCliente['senha'])) {
                $_SESSION['cliente_id'] = $dadosCliente['id'];
                $_SESSION['cliente_nome'] = $dadosCliente['nome']; // Guarda o nome na sessão
                $_SESSION['login_recente'] = true;
                header("Location: ../Views/perfil.php");
                exit;
            } else {
                header('Location: ../Views/cadastro.php?nura_ft=error&nura_flash=' . rawurlencode('Email ou senha incorretos.'));
                exit;
            }
        }
    }

    public function cadastrar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cliente = new Cliente();
            $cliente->setNome($_POST['nome']);
            $cliente->setEmail($_POST['email']);
            $cliente->setTelefone($_POST['telefone'] ?? null);
            $cliente->setSenha($_POST['senha']);

            $novoId = $cliente->salvar();

            if ($novoId) {
                $_SESSION['cliente_id'] = $novoId;
                $_SESSION['cliente_nome'] = $_POST['nome'];
                $_SESSION['conta_nova'] = true;
                header("Location: ../Views/perfil.php");
                exit;
            } else {
                header('Location: ../Views/cadastro.php?nura_ft=error&nura_flash=' . rawurlencode('Erro no cadastro. Tente outro e-mail ou tente novamente.'));
                exit;
            }
        }
    }

    public function atualizar()
    {
        if (!isset($_SESSION['cliente_id'])) {
            header("Location: ../index.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cliente = new Cliente();
            $cliente->setId($_SESSION['cliente_id']);
            $cliente->setNome($_POST['nome']);
            $cliente->setEmail($_POST['email']);
            $cliente->setTelefone($_POST['telefone'] ?? null);

            // Se o cliente digitou senha, o Model vai atualizar. Se deixou vazio, o Model ignora.
            if (!empty($_POST['senha'])) {
                $cliente->setSenha($_POST['senha']);
            }

            if ($cliente->atualizar()) {
                $_SESSION['cliente_nome'] = $_POST['nome']; // Atualiza nome na sessão
                header('Location: ../Views/perfil.php?nura_ft=success&nura_flash=' . rawurlencode('Dados atualizados com sucesso.'));
                exit;
            } else {
                header('Location: ../Views/perfil.php?nura_ft=error&nura_flash=' . rawurlencode('Não foi possível atualizar seus dados.'));
                exit;
            }
        }
    }

    public function deletar()
    {
        if (!isset($_SESSION['cliente_id'])) {
            header("Location: ../Views/index.php");
            exit;
        }

        $id = $_SESSION['cliente_id'];

        if (!empty($_GET['motivo'])) {
            error_log('[Nura] Exclusão de conta — cliente_id=' . (int) $id . ' motivo=' . substr($_GET['motivo'], 0, 64));
        }
        if (!empty($_GET['detalhe'])) {
            error_log('[Nura] Exclusão de conta — detalhe=' . substr($_GET['detalhe'], 0, 200));
        }

        // Importa e deleta o Perfil Clínico do banco ANTES de deletar o Cliente (previne falha de Chave Estrangeira restrita)
        require_once __DIR__ . '/../Models/PerfilClinico.php';
        PerfilClinico::deletar($id);

        if (Cliente::deletar($id)) {
            unset($_SESSION['cliente_id']);
            unset($_SESSION['cliente_nome']);
            unset($_SESSION['login_recente']);
            unset($_SESSION['conta_nova']);
            header('Location: ../Views/index.php?nura_ft=success&nura_flash=' . rawurlencode('Sua conta foi encerrada. Até logo!'));
            exit;
        } else {
            header('Location: ../Views/perfil.php?nura_ft=error&nura_flash=' . rawurlencode('Não foi possível excluir a conta. Tente novamente mais tarde.'));
            exit;
        }
    }

    public function sair()
    {
        unset($_SESSION['cliente_id']);
        unset($_SESSION['cliente_nome']);
        unset($_SESSION['login_recente']);
        unset($_SESSION['conta_nova']);
        header("Location: ../Views/index.php"); // Manda pra home ao sair
        exit;
    }

    public function firebase_sync()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $uid = $_POST['uid'] ?? '';
            $email = $_POST['email'] ?? '';
            $nome = $_POST['nome'] ?? '';
            $telefone = $_POST['telefone'] ?? '';

            if (empty($email) || empty($uid)) {
                echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
                exit;
            }

            // Verifica se o cliente já existe
            $dadosCliente = Cliente::buscarPorEmail($email);

            if ($dadosCliente) {
                // Já existe, apenas faz login e associa a sessão
                $_SESSION['cliente_id'] = $dadosCliente['id'];
                $_SESSION['cliente_nome'] = $dadosCliente['nome'];
                $_SESSION['login_recente'] = true;
                echo json_encode(['success' => true]);
                exit;
            } else {
                // Não existe: Cria a conta automaticamente usando as informações do Firebase
                $cliente = new Cliente();
                $cliente->setNome($nome);
                $cliente->setEmail($email);
                $cliente->setTelefone($telefone);
                
                // Gera uma senha aleatória para preencher o campo obrigatório do banco
                $senhaProvisoria = bin2hex(random_bytes(16));
                $cliente->setSenha($senhaProvisoria); 
                
                $novoId = $cliente->salvar();

                if ($novoId) {
                    $_SESSION['cliente_id'] = $novoId;
                    $_SESSION['cliente_nome'] = $nome;
                    $_SESSION['conta_nova'] = true;
                    echo json_encode(['success' => true]);
                    exit;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Falha ao salvar o novo usuário no banco.']);
                    exit;
                }
            }
        }
    }
}

if (isset($_GET['acao'])) {
    $controller = new ClienteController();
    $acao = $_GET['acao'];
    if ($acao == 'login')
        $controller->login();
    elseif ($acao == 'cadastrar')
        $controller->cadastrar();
    elseif ($acao == 'atualizar')
        $controller->atualizar();
    elseif ($acao == 'deletar')
        $controller->deletar();
    elseif ($acao == 'sair')
        $controller->sair();
    elseif ($acao == 'firebase_sync')
        $controller->firebase_sync();
}
?>