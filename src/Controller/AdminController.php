<?php
// src/Controller/AdminController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Admin.php';
require_once __DIR__ . '/../Models/Produto.php';

class AdminController
{
    public function login($email = '', $senha = '')
    {
        // Se chamado via injeção no login.php com parâmetros
        if ($email !== '' && $senha !== '') {
            $adminDados = Admin::buscarPorEmail($email);
            if ($adminDados && password_verify($senha, $adminDados['senha'])) {
                $admin = new Admin();
                $admin->setId($adminDados['id']);
                $admin->setNome($adminDados['nome']);
                $admin->setEmail($adminDados['email']);
                return $admin;
            }
            return false;
        }

        // Fallback para quando acessado via rota AdminController.php?acao=login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $emailPost = trim($_POST['email'] ?? '');
            $senhaPost = $_POST['senha'] ?? '';

            $adminDados = Admin::buscarPorEmail($emailPost);

            if ($adminDados && password_verify($senhaPost, $adminDados['senha'])) {
                $_SESSION['admin_id'] = $adminDados['id'];
                $_SESSION['admin_nome'] = $adminDados['nome'];
                header("Location: ../Views/admin/dashboard.php");
                exit;
            } else {
                header("Location: ../Views/admin/login.php?nura_ft=error&nura_flash=" . urlencode("E-mail ou senha incorretos."));
                exit;
            }
        }
        header("Location: ../Views/admin/login.php");
        exit;
    }

    public function sair()
    {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_nome']);
        header("Location: ../Views/admin/login.php?nura_ft=success&nura_flash=" . urlencode("Você saiu com sucesso do painel administrativo."));
        exit;
    }

    public function criarProduto()
    {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ../Views/admin/login.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $preco = floatval($_POST['preco'] ?? 0);
            $tag = trim($_POST['tag'] ?? '');
            $estoque = intval($_POST['estoque'] ?? 0);
            $alergias = $_POST['alergias'] ?? [];
            $restricoes = $_POST['restricoes'] ?? [];

            // Gerenciamento de Upload de Imagem
            $imgPath = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500'; // fallback
            if (isset($_FILES['imagem_upload']) && $_FILES['imagem_upload']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['imagem_upload']['tmp_name'];
                $fileName = $_FILES['imagem_upload']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                // Tipos permitidos
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../assets/img/';
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    $dest_path = $uploadFileDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $imgPath = '../assets/img/' . $newFileName;
                    }
                }
            } elseif (!empty($_POST['img_url'])) {
                $imgPath = trim($_POST['img_url']);
            }

            $produto = new Produto();
            $produto->setNome($nome);
            $produto->setDescricao($descricao);
            $produto->setPreco($preco);
            $produto->setImg($imgPath);
            $produto->setTag($tag);
            $produto->setAlergias($alergias);
            $produto->setRestricoes($restricoes);
            $produto->setEstoque($estoque);

            if ($produto->salvar()) {
                header("Location: ../Views/admin/produtos.php?nura_ft=success&nura_flash=" . urlencode("Produto cadastrado com sucesso!"));
                exit;
            } else {
                header("Location: ../Views/admin/produtos_form.php?nura_ft=error&nura_flash=" . urlencode("Erro ao cadastrar produto no banco."));
                exit;
            }
        }
        header("Location: ../Views/admin/produtos.php");
        exit;
    }

    public function atualizarProduto()
    {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ../Views/admin/login.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $nome = trim($_POST['nome'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $preco = floatval($_POST['preco'] ?? 0);
            $tag = trim($_POST['tag'] ?? '');
            $estoque = intval($_POST['estoque'] ?? 0);
            $alergias = $_POST['alergias'] ?? [];
            $restricoes = $_POST['restricoes'] ?? [];
            $imgPath = trim($_POST['img_existente'] ?? '');

            // Se subir nova imagem
            if (isset($_FILES['imagem_upload']) && $_FILES['imagem_upload']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['imagem_upload']['tmp_name'];
                $fileName = $_FILES['imagem_upload']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../assets/img/';
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    $dest_path = $uploadFileDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $imgPath = '../assets/img/' . $newFileName;
                    }
                }
            } elseif (!empty($_POST['img_url'])) {
                $imgPath = trim($_POST['img_url']);
            }

            $produto = new Produto();
            $produto->setId($id);
            $produto->setNome($nome);
            $produto->setDescricao($descricao);
            $produto->setPreco($preco);
            $produto->setImg($imgPath);
            $produto->setTag($tag);
            $produto->setAlergias($alergias);
            $produto->setRestricoes($restricoes);
            $produto->setEstoque($estoque);

            if ($produto->atualizar()) {
                header("Location: ../Views/admin/produtos.php?nura_ft=success&nura_flash=" . urlencode("Produto atualizado com sucesso!"));
                exit;
            } else {
                header("Location: ../Views/admin/produtos_form.php?id=$id&nura_ft=error&nura_flash=" . urlencode("Erro ao atualizar o produto."));
                exit;
            }
        }
        header("Location: ../Views/admin/produtos.php");
        exit;
    }

    public function deletarProduto()
    {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ../Views/admin/login.php");
            exit;
        }

        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            if (Produto::deletar($id)) {
                header("Location: ../Views/admin/produtos.php?nura_ft=success&nura_flash=" . urlencode("Produto excluído com sucesso!"));
                exit;
            } else {
                header("Location: ../Views/admin/produtos.php?nura_ft=error&nura_flash=" . urlencode("Erro ao excluir produto. Verifique se ele está associado a algum pedido."));
                exit;
            }
        }
        header("Location: ../Views/admin/produtos.php");
        exit;
    }
}

// Roteador de Ações
$acao = $_GET['acao'] ?? '';
$controller = new AdminController();

switch ($acao) {
    case 'login':
        $controller->login();
        break;
    case 'sair':
        $controller->sair();
        break;
    case 'criar_produto':
        $controller->criarProduto();
        break;
    case 'atualizar_produto':
        $controller->atualizarProduto();
        break;
    case 'deletar_produto':
        $controller->deletarProduto();
        break;
}
?>
