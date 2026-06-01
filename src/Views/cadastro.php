<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura - Acessar Conta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body class="page-auth">

    <header>
        <div class="container header-inner">
            <a href="index.php" class="logo" aria-label="Nura — Início">
                <img class="logo-img" src="../assets/img/NURA_logo.png" alt="">
            </a>
            <a href="index.php" class="back-link">
                <i class="ph-bold ph-arrow-left" aria-hidden="true"></i> Voltar
            </a>
        </div>
    </header>

    <div class="auth-split">
        <div class="auth-split-image">
            <div class="auth-image-content">
                <h2>Alimentação saudável que transforma seu dia.</h2>
                <p>Ingredientes frescos, refeições balanceadas e muito sabor entregues na sua porta.</p>
            </div>
        </div>

        <div class="auth-split-form">
            <div class="auth-card">

                <div class="auth-header">
                    <div class="auth-logo-wrap">
                        <img class="auth-logo" src="../assets/img/NURA_logo.png" alt="Nura">
                    </div>
                    <p class="auth-tagline">Bem-vindo(a) de volta! Acesse ou crie sua conta.</p>
                </div>

                <div class="tabs" role="tablist" aria-label="Login ou cadastro">
                    <button type="button" class="tab-btn active" data-target="login-form" role="tab" aria-selected="true" aria-controls="login-form" id="tab-login">Login</button>
                    <button type="button" class="tab-btn" data-target="signup-form" role="tab" aria-selected="false" aria-controls="signup-form" id="tab-signup">Cadastro</button>
                </div>

                <div class="social-login">
                    <button type="button" class="btn btn-social" aria-label="Entrar com Google" id="btn-google-login">
                        <img src="https://authjs.dev/img/providers/google.svg" alt="Google" width="20" height="20">
                        <span>Continuar com Google</span>
                    </button>
                </div>

                <div class="auth-divider">
                    <span>ou</span>
                </div>

                <div id="login-form" class="form-content active" role="tabpanel" aria-labelledby="tab-login">
                    <form id="firebase-login-form">
                        <div class="form-group">
                            <label for="login-email">Email</label>
                            <div class="input-wrapper">
                                <i class="ph ph-envelope input-icon" aria-hidden="true"></i>
                                <input id="login-email" type="email" name="email" class="input input-with-icon" placeholder="seu@email.com" required autocomplete="email">
                                <i class="ph-fill ph-check-circle input-success-icon" aria-hidden="true" style="display:none;"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="login-senha">Senha</label>
                            <div class="input-wrapper">
                                <i class="ph ph-lock input-icon" aria-hidden="true"></i>
                                <input id="login-senha" type="password" name="senha" class="input input-with-icon" placeholder="••••••••" required autocomplete="current-password">
                                <button type="button" class="toggle-password" aria-label="Mostrar ou ocultar senha">
                                    <i class="ph ph-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="auth-options">
                            <label class="remember-me">
                                <input type="checkbox" name="remember" id="remember-me">
                                <span class="checkbox-custom"></span>
                                Lembrar de mim
                            </label>
                            <a href="#" class="forgot-password">Esqueceu a senha?</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">Entrar na minha conta</button>
                    </form>
                </div>

                <div id="signup-form" class="form-content" role="tabpanel" aria-labelledby="tab-signup">
                    <form id="firebase-signup-form">
                        <div class="form-group">
                            <label for="signup-nome">Nome completo</label>
                            <div class="input-wrapper">
                                <i class="ph ph-user input-icon" aria-hidden="true"></i>
                                <input id="signup-nome" type="text" name="nome" class="input input-with-icon" placeholder="Seu nome" required autocomplete="name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signup-email">Email</label>
                            <div class="input-wrapper">
                                <i class="ph ph-envelope input-icon" aria-hidden="true"></i>
                                <input id="signup-email" type="email" name="email" class="input input-with-icon" placeholder="seu@email.com" required autocomplete="email">
                                <i class="ph-fill ph-check-circle input-success-icon" aria-hidden="true" style="display:none;"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signup-telefone">Telefone</label>
                            <div class="input-wrapper">
                                <i class="ph ph-phone input-icon" aria-hidden="true"></i>
                                <input id="signup-telefone" type="text" name="telefone" class="input input-with-icon input-telefone" placeholder="(11) 90000-0000" autocomplete="tel">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signup-senha">Senha</label>
                            <div class="input-wrapper">
                                <i class="ph ph-lock input-icon" aria-hidden="true"></i>
                                <input id="signup-senha" type="password" name="senha" class="input input-with-icon" placeholder="Crie uma senha forte" required autocomplete="new-password">
                                <button type="button" class="toggle-password" aria-label="Mostrar ou ocultar senha">
                                    <i class="ph ph-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="password-strength">
                                <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                                <span class="strength-text" id="strength-text">Mínimo 8 caracteres</span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-full">Criar minha conta</button>
                    </form>
                </div>

                <p class="auth-trust" style="margin-top: 1.5rem; text-align: center;"><i class="ph-fill ph-shield-check" aria-hidden="true"></i> Compra e dados protegidos</p>

            </div>
        </div>
    </div>

    <div id="google-registration-modal" class="modal-overlay">
        <div class="custom-modal" style="max-width: 400px; padding: 2rem; border-radius: 1rem; text-align: left;">
            <h3 class="modal-title" style="margin-bottom: 1rem;">Completar Cadastro</h3>
            <p class="modal-text" style="margin-bottom: 1.5rem;">Falta pouco! Confirme seus dados e crie uma senha para concluir o cadastro com Google.</p>
            <form id="form-google-complete">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label>Nome</label>
                    <input type="text" id="google-nome" class="input" required>
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label>Telefone</label>
                    <input type="text" id="google-telefone" class="input input-telefone" placeholder="(11) 90000-0000" required>
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label>Criar Senha</label>
                    <input type="password" id="google-senha" class="input" required placeholder="Crie uma senha forte" minlength="8">
                </div>
                <div class="modal-actions" style="margin-top: 2rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('google-registration-modal').classList.remove('active')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-google-complete-submit">Concluir</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="auth-footer">
        <div class="container">
            <p>&copy; 2026 Nura. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="../assets/js/firebase-auth-bundle.js"></script>
    <script src="../script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const googleSpan = document.querySelector('#btn-google-login span');
            
            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    if (targetId === 'login-form') {
                        googleSpan.textContent = 'Continuar com Google';
                    } else if (targetId === 'signup-form') {
                        googleSpan.textContent = 'Cadastre-se com Google';
                    }
                });
            });
        });
    </script>
</body>

</html>