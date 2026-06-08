import { initializeApp } from "firebase/app";
import { 
    getAuth, 
    signInWithEmailAndPassword, 
    createUserWithEmailAndPassword, 
    GoogleAuthProvider, 
    signInWithRedirect, // Mudado para Redirect para evitar bloqueios de popup
    getRedirectResult   // Necessário para capturar o login após o retorno da página
} from "firebase/auth";

const firebaseConfig = {
  apiKey: "AIzaSyC94ajk8ssemaYk-U9bDsYoih7DXJDxvdE",
  authDomain: "nura-cbb0b.firebaseapp.com",
  projectId: "nura-cbb0b",
  storageBucket: "nura-cbb0b.firebasestorage.app",
  messagingSenderId: "572341674848",
  appId: "1:572341674848:web:26e5fa9fa348c19c76f405",
  measurementId: "G-PZ3LX5X2D5"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const provider = new GoogleAuthProvider();

// Função para sincronizar o usuário com o Backend PHP
async function syncUserWithBackend(user, nomeOpcional = null) {
    const nome = user.displayName || nomeOpcional || user.email.split('@')[0];
    
    try {
        if (typeof mostrarOverlayGlobal === 'function') {
            mostrarOverlayGlobal('Autenticando...', 'Sincronizando seus dados com o sistema.', true);
        }

        const formData = new FormData();
        formData.append('uid', user.uid);
        formData.append('email', user.email);
        formData.append('nome', nome);

        const response = await fetch('../Controller/ClienteController.php?acao=firebase_sync', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            window.location.href = '../Views/perfil.php';
        } else {
            if (typeof mostrarOverlayGlobal === 'function') {
                document.getElementById('global-page-transition').classList.remove('active');
            }
            window.NuraNotify.toast('Erro ao sincronizar com o servidor: ' + (result.message || 'Desconhecido'), 'error');
        }
    } catch (error) {
        console.error("Erro no sync:", error);
        window.NuraNotify.toast('Erro de comunicação com o servidor.', 'error');
        if (typeof mostrarOverlayGlobal === 'function') {
            document.getElementById('global-page-transition').classList.remove('active');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    
    // --- VERIFICA SE RETORNOU DO LOGIN DO GOOGLE ---
    getRedirectResult(auth)
        .then((result) => {
            if (result && result.user) {
                syncUserWithBackend(result.user);
            }
        })
        .catch((error) => {
            console.error("Erro ao retornar do Google Redirect:", error);
            window.NuraNotify.toast('Erro ao processar login com Google.', 'error');
        });

    // --- LOGIN COM EMAIL/SENHA ---
    const loginForm = document.getElementById('firebase-login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const senha = document.getElementById('login-senha').value;
            const btn = loginForm.querySelector('button[type="submit"]');
            
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Entrando...';
            btn.disabled = true;

            signInWithEmailAndPassword(auth, email, senha)
                .then((userCredential) => {
                    syncUserWithBackend(userCredential.user);
                })
                .catch((error) => {
                    btn.innerHTML = 'Entrar na minha conta';
                    btn.disabled = false;
                    let msg = "Erro ao fazer login.";
                    if(error.code === 'auth/invalid-credential') msg = "Email ou senha incorretos.";
                    else if(error.code === 'auth/user-not-found') msg = "Usuário não encontrado.";
                    else if(error.code === 'auth/wrong-password') msg = "Senha incorreta.";
                    window.NuraNotify.toast(msg, 'error');
                });
        });
    }

    // --- CADASTRO COM EMAIL/SENHA ---
    const signupForm = document.getElementById('firebase-signup-form');
    if (signupForm) {
        signupForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const nome = document.getElementById('signup-nome').value;
            const email = document.getElementById('signup-email').value;
            const senha = document.getElementById('signup-senha').value;
            const btn = signupForm.querySelector('button[type="submit"]');

            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Criando...';
            btn.disabled = true;

            createUserWithEmailAndPassword(auth, email, senha)
                .then((userCredential) => {
                    syncUserWithBackend(userCredential.user, nome);
                })
                .catch((error) => {
                    btn.innerHTML = 'Criar minha conta';
                    btn.disabled = false;
                    let msg = "Erro ao criar conta.";
                    if(error.code === 'auth/email-already-in-use') msg = "Este e-mail já está em uso.";
                    else if(error.code === 'auth/weak-password') msg = "A senha é muito fraca.";
                    window.NuraNotify.toast(msg, 'error');
                });
        });
    }

    // --- LOGIN COM GOOGLE ---
    const btnGoogle = document.getElementById('btn-google-login');
    if (btnGoogle) {
        btnGoogle.addEventListener('click', () => {
            signInWithRedirect(auth, provider);
        });
    }
});