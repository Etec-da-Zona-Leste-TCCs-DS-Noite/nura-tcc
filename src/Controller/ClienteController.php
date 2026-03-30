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
                header("Location: ../Views/perfil.php");
                exit;
            } else {
                echo "<script>alert('Email ou senha incorretos!'); window.location='../Views/cadastro.php';</script>";
            }
        }
    }

    public function cadastrar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cliente = new Cliente();
            $cliente->setNome($_POST['nome']);
            $cliente->setEmail($_POST['email']);
            $cliente->setSenha($_POST['senha']);

            $novoId = $cliente->salvar();

            if ($novoId) {
                $_SESSION['cliente_id'] = $novoId;
                $_SESSION['cliente_nome'] = $_POST['nome'];
                header("Location: ../Views/perfil.php");
                exit;
            } else {
                echo "<script>alert('Erro no cadastro.'); window.location='../Views/cadastro.php';</script>";
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

            // Se o cliente digitou senha, o Model vai atualizar. Se deixou vazio, o Model ignora.
            if (!empty($_POST['senha'])) {
                $cliente->setSenha($_POST['senha']);
            }

            if ($cliente->atualizar()) {
                $_SESSION['cliente_nome'] = $_POST['nome']; // Atualiza nome na sessão
                echo "<script>alert('Dados (e senha, se informada) atualizados!'); window.location='../Views/perfil.php';</script>";
            } else {
                echo "Erro ao atualizar.";
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

        // Importa e deleta o Perfil Clínico do banco ANTES de deletar o Cliente (previne falha de Chave Estrangeira restrita)
        require_once __DIR__ . '/../Models/PerfilClinico.php';
        PerfilClinico::deletar($id);

        if (Cliente::deletar($id)) {
            session_destroy();
            echo "<script>alert('Conta excluída com sucesso.'); window.location='../Views/index.php';</script>";
        } else {
            echo "<script>alert('Aconteceu um erro ao tentar excluir a sua conta. Tente novamente mais tarde.'); window.location='../Views/perfil.php';</script>";
        }
    }

    public function sair()
    {
        session_destroy();
        header("Location: ../Views/index.php"); // Manda pra home ao sair
        exit;
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
}
?>