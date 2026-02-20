<?php
session_start();
require_once __DIR__ . '/../Models/Usuario.php';

class UsuarioController
{

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $dadosUsuario = Usuario::buscarPorEmail($email);

            if ($dadosUsuario && password_verify($senha, $dadosUsuario['senha'])) {
                $_SESSION['usuario_id'] = $dadosUsuario['id'];
                $_SESSION['usuario_nome'] = $dadosUsuario['nome']; // Guarda o nome na sessão
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
            $usuario = new Usuario();
            $usuario->setNome($_POST['nome']);
            $usuario->setEmail($_POST['email']);
            $usuario->setSenha($_POST['senha']);

            $novoId = $usuario->salvar();

            if ($novoId) {
                $_SESSION['usuario_id'] = $novoId;
                $_SESSION['usuario_nome'] = $_POST['nome'];
                header("Location: ../Views/perfil.php");
                exit;
            } else {
                echo "<script>alert('Erro no cadastro.'); window.location='../Views/cadastro.php';</script>";
            }
        }
    }

    public function atualizar()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ../index.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = new Usuario();
            $usuario->setId($_SESSION['usuario_id']);
            $usuario->setNome($_POST['nome']);
            $usuario->setEmail($_POST['email']);

            // Se o usuário digitou senha, o Model vai atualizar. Se deixou vazio, o Model ignora.
            if (!empty($_POST['senha'])) {
                $usuario->setSenha($_POST['senha']);
            }

            if ($usuario->atualizar()) {
                $_SESSION['usuario_nome'] = $_POST['nome']; // Atualiza nome na sessão
                echo "<script>alert('Dados (e senha, se informada) atualizados!'); window.location='../Views/perfil.php';</script>";
            } else {
                echo "Erro ao atualizar.";
            }
        }
    }

    public function deletar()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ../index.php");
            exit;
        }
        if (Usuario::deletar($_SESSION['usuario_id'])) {
            session_destroy();
            echo "<script>alert('Conta excluída.'); window.location='../index.php';</script>";
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
    $controller = new UsuarioController();
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