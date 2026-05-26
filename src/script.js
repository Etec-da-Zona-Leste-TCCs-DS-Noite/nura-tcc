/**
 * Notificações Nura — substitui alert() e mensagens via redirect PHP (?nura_flash)
 */
window.NuraNotify = window.NuraNotify || {};
(function () {
    let toastHost;
    let hideTimer;

    function ensureToastHost() {
        if (!toastHost) {
            toastHost = document.createElement('div');
            toastHost.id = 'nura-toast-host';
            toastHost.setAttribute('role', 'status');
            toastHost.setAttribute('aria-live', 'polite');
            document.body.appendChild(toastHost);
        }
        return toastHost;
    }

    window.NuraNotify.toast = function (message, type) {
        if (!message) return;
        type = type === 'error' ? 'error' : type === 'info' ? 'info' : 'success';
        const el = ensureToastHost();
        clearTimeout(hideTimer);
        const icon =
            type === 'error'
                ? 'ph-fill ph-x-circle'
                : type === 'info'
                    ? 'ph-fill ph-info'
                    : 'ph-fill ph-check-circle';
        el.className = 'nura-toast-host nura-toast-host--' + type;
        el.innerHTML =
            '<i class="' + icon + ' nura-toast-host__icon" aria-hidden="true"></i><span class="nura-toast-host__text"></span>';
        el.querySelector('.nura-toast-host__text').textContent = message;
        requestAnimationFrame(function () {
            el.classList.add('is-visible');
        });
        hideTimer = setTimeout(function () {
            el.classList.remove('is-visible');
        }, 4200);
    };

    window.NuraNotify.readFlashFromUrl = function () {
        try {
            const params = new URLSearchParams(window.location.search);
            const flash = params.get('nura_flash');
            const ft = (params.get('nura_ft') || 'success').toLowerCase();
            if (!flash) return;
            const type = ft === 'error' ? 'error' : ft === 'info' ? 'info' : 'success';
            window.NuraNotify.toast(decodeURIComponent(flash), type);
            params.delete('nura_flash');
            params.delete('nura_ft');
            const q = params.toString();
            const url = window.location.pathname + (q ? '?' + q : '') + window.location.hash;
            window.history.replaceState({}, document.title, url);
        } catch (e) {
            /* noop */
        }
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    window.NuraNotify.readFlashFromUrl();

    if (window.__NURA_SESSION_TOAST__ && window.__NURA_SESSION_TOAST__.msg) {
        window.NuraNotify.toast(window.__NURA_SESSION_TOAST__.msg, window.__NURA_SESSION_TOAST__.type || 'success');
        delete window.__NURA_SESSION_TOAST__;
    }

    /* --- 0. HEADER SCROLL TRANSITION --- */
    const siteHeader = document.querySelector('header');
    if (siteHeader) {
        let ticking = false;
        const SCROLL_THRESHOLD = 50;

        function onScroll() {
            if (!ticking) {
                requestAnimationFrame(() => {
                    siteHeader.classList.toggle('header--scrolled', window.scrollY > SCROLL_THRESHOLD);
                    ticking = false;
                });
                ticking = true;
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        // Set initial state in case page loads already scrolled
        onScroll();
    }

    /* --- 1. LÓGICA DAS ABAS (AUTH e Perfil) --- */
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.form-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            const targetId = btn.getAttribute('data-target');
            const target = document.getElementById(targetId);
            if (target) target.classList.add('active');
        });
    });

    /* --- 2. CARROSSEL MULTI-INSTÂNCIA (Suporta vários na mesma página) --- */
    const carousels = document.querySelectorAll('.carousel-container');

    carousels.forEach(container => {
        const track = container.querySelector('.carousel-track');
        const prevBtn = container.querySelector('.prev-btn');
        const nextBtn = container.querySelector('.next-btn');
        const items = container.querySelectorAll('.carousel-item');

        if (!track || items.length === 0) return;

        let currentIndex = 0;
        let autoPlayInterval;
        const autoPlayDelay = 5000 + Math.random() * 2000;

        const getItemsVisible = () => window.innerWidth >= 768 ? 3 : 1;

        const updateCarousel = () => {
            const itemsVisible = getItemsVisible();
            const moveAmount = 100 / itemsVisible;
            track.style.transform = `translateX(-${currentIndex * moveAmount}%)`;

            // Hide buttons if not enough items
            if (items.length <= itemsVisible) {
                if (prevBtn) prevBtn.style.display = 'none';
                if (nextBtn) nextBtn.style.display = 'none';
            } else {
                if (prevBtn) prevBtn.style.display = 'flex';
                if (nextBtn) nextBtn.style.display = 'flex';
            }
        };

        const moveNext = () => {
            const itemsVisible = getItemsVisible();
            const maxIndex = Math.max(0, items.length - itemsVisible);

            if (currentIndex < maxIndex) {
                currentIndex++;
            } else {
                currentIndex = 0;
            }
            updateCarousel();
        };

        const movePrev = () => {
            if (currentIndex > 0) {
                currentIndex--;
            }
            updateCarousel();
        };

        if (nextBtn) nextBtn.addEventListener('click', () => { moveNext(); resetAutoPlay(); });
        if (prevBtn) prevBtn.addEventListener('click', () => { movePrev(); resetAutoPlay(); });

        let touchStartX = 0;
        let touchEndX = 0;
        track.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
        track.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) { moveNext(); resetAutoPlay(); }
            if (touchEndX - touchStartX > 50) { movePrev(); resetAutoPlay(); }
        }, { passive: true });

        function startAutoPlay() { autoPlayInterval = setInterval(moveNext, autoPlayDelay); }
        function stopAutoPlay() { clearInterval(autoPlayInterval); }
        function resetAutoPlay() { stopAutoPlay(); startAutoPlay(); }

        container.addEventListener('mouseenter', stopAutoPlay);
        container.addEventListener('mouseleave', startAutoPlay);

        window.addEventListener('resize', updateCarousel);
        updateCarousel();
        startAutoPlay();
    });

    /* --- 3. AJAX DO CARRINHO (Adicionar sem recarregar) --- */
    const formsAdicionar = document.querySelectorAll('form[action*="carrinho_acoes.php?acao=adicionar"]');

    formsAdicionar.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btn = this.querySelector('button');
            const originalText = btn.innerHTML;

            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Adicionando...';
            btn.disabled = true;

            fetch('carrinho_acoes.php?acao=adicionar&ajax=1', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        animarParaCarrinho(btn);
                        atualizarContadorCarrinho(data.novaQtd);
                        window.NuraNotify.toast('Produto adicionado ao carrinho!', 'success');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    window.NuraNotify.toast('Não foi possível adicionar. Tente novamente.', 'error');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        });
    });

    function atualizarContadorCarrinho(qtd) {
        const cartIconContainer = document.querySelector('a[aria-label="Carrinho"]');
        if (!cartIconContainer) return;

        let badge = cartIconContainer.querySelector('.cart-badge');

        if (qtd > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'cart-badge';
                cartIconContainer.appendChild(badge);
            }
            badge.textContent = qtd;

            // Efeito bounce no ícone do carrinho
            const icon = cartIconContainer.querySelector('.ph-shopping-cart') || cartIconContainer;
            icon.classList.remove('cart-bounce');
            void icon.offsetWidth; // trigger reflow
            icon.classList.add('cart-bounce');
        } else if (badge) {
            badge.remove();
        }
    }

    function animarParaCarrinho(btnElement) {
        const cartLink = document.querySelector('a[href="carrinho.php"]') || document.querySelector('a[aria-label="Carrinho"]');
        if (!cartLink || !btnElement) return;

        const btnRect = btnElement.getBoundingClientRect();
        const cartRect = cartLink.getBoundingClientRect();

        const flyEl = document.createElement('div');
        flyEl.className = 'fly-to-cart-element';

        // Posição inicial (centro do botão)
        const startX = btnRect.left + btnRect.width / 2 - 10;
        const startY = btnRect.top + btnRect.height / 2 - 10;

        flyEl.style.left = startX + 'px';
        flyEl.style.top = startY + 'px';

        document.body.appendChild(flyEl);

        // Posição final (centro do ícone do carrinho)
        const endX = cartRect.left + cartRect.width / 2 - 10;
        const endY = cartRect.top + cartRect.height / 2 - 10;

        // Anima via requestAnimationFrame para garantir fluidez
        requestAnimationFrame(() => {
            flyEl.style.transform = `translate(${endX - startX}px, ${endY - startY}px) scale(0.3)`;
            flyEl.style.opacity = '0.3';
        });

        // Limpa o elemento após a transição
        setTimeout(() => {
            flyEl.remove();
        }, 800);
    }



    /* --- Exclusão de conta (perfil) — motivo obrigatório --- */
    const deleteAccountModal = document.getElementById('deleteAccountModal');
    const openDeleteAccountBtn = document.getElementById('open-delete-account-modal');
    const cancelDeleteAccountBtn = document.getElementById('cancel-delete-account');
    const deleteAccountForm = document.getElementById('delete-account-form');
    const deleteAccountDetalheWrap = document.getElementById('delete-account-detalhe-wrap');
    const deleteAccountDetalhe = document.getElementById('delete-account-detalhe');

    function openModal(el) {
        if (el) el.classList.add('active');
    }
    function closeModal(el) {
        if (el) el.classList.remove('active');
    }

    if (deleteAccountModal && openDeleteAccountBtn) {
        openDeleteAccountBtn.addEventListener('click', e => {
            e.preventDefault();
            openModal(deleteAccountModal);
        });
        cancelDeleteAccountBtn?.addEventListener('click', () => closeModal(deleteAccountModal));
        deleteAccountModal.addEventListener('click', e => {
            if (e.target === deleteAccountModal) closeModal(deleteAccountModal);
        });
        deleteAccountForm?.querySelectorAll('input[name="motivo"]').forEach(radio => {
            radio.addEventListener('change', () => {
                const show = radio.value === 'outro' && radio.checked;
                if (deleteAccountDetalheWrap) {
                    deleteAccountDetalheWrap.classList.toggle('is-visible', show);
                }
                if (deleteAccountDetalhe && !show) deleteAccountDetalhe.value = '';
            });
        });
        deleteAccountForm?.addEventListener('submit', e => {
            e.preventDefault();
            const fd = new FormData(deleteAccountForm);
            const motivo = fd.get('motivo');
            if (!motivo) {
                window.NuraNotify.toast('Selecione um motivo para continuar.', 'info');
                return;
            }
            let detalhe = (fd.get('detalhe') || '').toString().trim();
            if (motivo === 'outro' && detalhe.length < 3) {
                window.NuraNotify.toast('Descreva brevemente o motivo (mínimo 3 caracteres).', 'info');
                return;
            }
            const base = '../Controller/ClienteController.php?acao=deletar';
            const url =
                base +
                '&motivo=' +
                encodeURIComponent(motivo) +
                (detalhe ? '&detalhe=' + encodeURIComponent(detalhe) : '');
            window.location.href = url;
        });
    }

    /* --- Exclusão perfil clínico (confirmação estilizada) --- */
    const clinicalModal = document.getElementById('clinicalDeleteModal');
    const clinicalDeleteTrigger = document.getElementById('clinical-delete-trigger');
    const clinicalDeleteHref = clinicalDeleteTrigger?.getAttribute('data-href') || '';
    const cancelClinicalBtn = document.getElementById('cancel-clinical-delete');
    const confirmClinicalBtn = document.getElementById('confirm-clinical-delete');

    if (clinicalModal && clinicalDeleteHref) {
        clinicalDeleteTrigger.addEventListener('click', e => {
            e.preventDefault();
            openModal(clinicalModal);
        });
        cancelClinicalBtn?.addEventListener('click', () => closeModal(clinicalModal));
        clinicalModal.addEventListener('click', e => {
            if (e.target === clinicalModal) closeModal(clinicalModal);
        });
        confirmClinicalBtn?.addEventListener('click', () => {
            window.location.href = clinicalDeleteHref;
        });
    }
});

/* HERO CAROUSEL */

const heroTrack = document.querySelector('.hero-track');

if (heroTrack) {

    let heroIndex = 0;
    const slides = heroTrack.children;

    function updateHero() {
        heroTrack.style.transform = `translateX(-${heroIndex * 100}%)`;
    }

    document.querySelector('.hero-next').onclick = () => {
        heroIndex = (heroIndex + 1) % slides.length;
        updateHero();
    };

    document.querySelector('.hero-prev').onclick = () => {
        heroIndex = (heroIndex - 1 + slides.length) % slides.length;
        updateHero();
    };

    setInterval(() => {
        heroIndex = (heroIndex + 1) % slides.length;
        updateHero();
    }, 6000);

}

// --- MOBILE MENU TOGGLE ---
document.addEventListener('DOMContentLoaded', () => {
    const mobileBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');

    if (mobileBtn && navLinks) {
        mobileBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            const icon = mobileBtn.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.replace('ph-list', 'ph-x');
            } else {
                icon.classList.replace('ph-x', 'ph-list');
            }
        });
    }
});

// --- PASSWORD TOGGLE ---
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtns = document.querySelectorAll('.toggle-password');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ph-eye', 'ph-eye-slash');
                icon.style.color = 'var(--primary)';
            } else {
                input.type = 'password';
                icon.classList.replace('ph-eye-slash', 'ph-eye');
                icon.style.color = 'var(--muted)';
            }
        });
    });
});

// --- MASKS ---
document.addEventListener('DOMContentLoaded', () => {
    const telefoneInputs = document.querySelectorAll('.input-telefone');
    telefoneInputs.forEach(input => {
        input.addEventListener('input', function (e) {
            let val = e.target.value.replace(/\D/g, '');
            let formatted = val;
            if (val.length > 2) {
                formatted = `(${val.substring(0, 2)}) `;
                if (val.length > 7) {
                    formatted += `${val.substring(2, 7)}-${val.substring(7, 11)}`;
                } else {
                    formatted += val.substring(2, 7);
                }
            } else if (val.length > 0) {
                formatted = `(${val}`;
            }
            e.target.value = formatted;
        });
    });

    // --- FORM VALIDATION (AUTH) ---
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('input', function (e) {
            const val = e.target.value;
            const successIcon = e.target.parentElement.querySelector('.input-success-icon');
            if (successIcon) {
                const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
                successIcon.style.display = isValid ? 'block' : 'none';
            }
        });
    });

    const signupPassword = document.getElementById('signup-senha');
    const strengthFill = document.getElementById('strength-fill');
    const strengthText = document.getElementById('strength-text');

    if (signupPassword && strengthFill && strengthText) {
        signupPassword.addEventListener('input', function (e) {
            const val = e.target.value;
            let strength = 0;

            if (val.length >= 8) strength += 25;
            if (/[A-Z]/.test(val)) strength += 25;
            if (/[0-9]/.test(val)) strength += 25;
            if (/[^A-Za-z0-9]/.test(val)) strength += 25;

            strengthFill.style.width = strength + '%';

            if (val.length === 0) {
                strengthFill.style.width = '0%';
                strengthFill.style.background = 'transparent';
                strengthText.textContent = 'Mínimo 8 caracteres';
                strengthText.style.color = 'var(--muted)';
            } else if (strength < 50) {
                strengthFill.style.background = 'var(--danger)';
                strengthText.textContent = 'Senha fraca';
                strengthText.style.color = 'var(--danger)';
            } else if (strength < 100) {
                strengthFill.style.background = 'var(--warning)';
                strengthText.textContent = 'Senha média';
                strengthText.style.color = 'var(--warning)';
            } else {
                strengthFill.style.background = 'var(--green-leaf)';
                strengthText.textContent = 'Senha forte!';
                strengthText.style.color = 'var(--green-leaf)';
            }
        });
    }
});

/* --- GLOBAL PAGE TRANSITION --- */
function mostrarOverlayGlobal(titulo = '', subtitulo = '', isProcessing = false) {
    let overlay = document.getElementById('global-page-transition');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'global-page-transition';
        overlay.className = 'page-transition-overlay';

        const spinner = document.createElement('div');
        spinner.className = 'transition-spinner';

        const titleEl = document.createElement('div');
        titleEl.id = 'transition-title';
        titleEl.className = 'transition-text';

        const subtextEl = document.createElement('div');
        subtextEl.id = 'transition-subtext';
        subtextEl.className = 'transition-subtext';

        overlay.appendChild(spinner);
        overlay.appendChild(titleEl);
        overlay.appendChild(subtextEl);
        document.body.appendChild(overlay);
    }

    document.getElementById('transition-title').textContent = titulo;
    document.getElementById('transition-subtext').textContent = subtitulo;

    if (isProcessing) {
        overlay.classList.add('processing');
    } else {
        overlay.classList.remove('processing');
    }

    overlay.classList.add('active');
}

document.addEventListener('DOMContentLoaded', () => {
    // Interceptar cliques em links para a transição global
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function (e) {
            if (e.defaultPrevented) return;
            const href = this.getAttribute('href');
            const target = this.getAttribute('target');

            // Ignorar links vazios, âncoras internas, JS, etc
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || target === '_blank') {
                return;
            }

            // Se tiver uma classe q ignora (ex: accordion), pula
            if (this.classList.contains('no-transition')) return;

            // Permite o click fluir se o control/cmd/shift estiver pressionado (nova aba)
            if (e.ctrlKey || e.metaKey || e.shiftKey) return;

            // Mostrar transição padrão (apenas loader)
            e.preventDefault();
            mostrarOverlayGlobal('', '');

            setTimeout(() => {
                window.location.href = href;
            }, 300); // tempo curtinho pra dar o fade in do overlay
        });
    });

    // Se a página está voltando do cache do browser (back button)
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            const overlay = document.getElementById('global-page-transition');
            if (overlay) overlay.classList.remove('active');
        }
    });

    // --- Live Search Autocomplete ---
    const searchForms = document.querySelectorAll('.header-search');
    searchForms.forEach(form => {
        const input = form.querySelector('input[name="busca"]');
        const wrapper = form.querySelector('.search-input-wrapper');
        if (!input || !wrapper) return;

        // Remove autocomplete padrão do HTML
        input.setAttribute('autocomplete', 'off');

        // Cria o container do dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'search-results-dropdown';
        dropdown.setAttribute('aria-live', 'polite');
        wrapper.appendChild(dropdown);

        let timeout = null;
        let currentFocus = -1;

        input.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(timeout);

            if (query.length < 2) {
                dropdown.classList.remove('active');
                return;
            }

            dropdown.classList.add('active');
            dropdown.innerHTML = '<div class="search-loading"><i class="ph ph-spinner" aria-hidden="true"></i></div>';

            timeout = setTimeout(() => {
                let basePath = 'api/search.php';
                if (window.location.pathname.includes('/Checkout/')) {
                    basePath = '../../api/search.php';
                } else if (window.location.pathname.includes('/Views/')) {
                    basePath = '../api/search.php';
                }
                
                fetch(`${basePath}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        currentFocus = -1;
                        if (!data || data.length === 0) {
                            dropdown.innerHTML = '<div class="search-dropdown-empty"><i class="ph ph-magnifying-glass" aria-hidden="true"></i>Nenhum resultado encontrado</div>';
                            return;
                        }

                        const list = document.createElement('ul');
                        list.className = 'search-dropdown-list';
                        
                        data.forEach((item) => {
                            const li = document.createElement('li');
                            const a = document.createElement('a');
                            a.className = 'search-dropdown-item';
                            a.href = item.url;
                            
                            // Visual
                            let visualHtml = '';
                            if (item.type === 'product' && item.image) {
                                visualHtml = `<img src="${item.image}" class="search-item-img" alt="">`;
                            } else {
                                visualHtml = `<div class="search-item-icon"><i class="ph ${item.icon || 'ph-file-text'}" aria-hidden="true"></i></div>`;
                            }

                            // Tag
                            let tagHtml = item.tag ? `<span class="search-item-tag">${item.tag}</span>` : '';

                            a.innerHTML = `
                                ${visualHtml}
                                <div class="search-item-content">
                                    <div class="search-item-title">${item.title} ${tagHtml}</div>
                                    <div class="search-item-desc">${item.description || ''}</div>
                                </div>
                            `;

                            li.appendChild(a);
                            list.appendChild(li);
                        });

                        dropdown.innerHTML = '';
                        dropdown.appendChild(list);
                    })
                    .catch(() => {
                        dropdown.innerHTML = '<div class="search-dropdown-empty"><i class="ph ph-warning-circle" aria-hidden="true"></i>Erro ao buscar. Tente novamente.</div>';
                    });
            }, 300);
        });

        // Navegação por Teclado
        input.addEventListener('keydown', function(e) {
            const items = dropdown.querySelectorAll('.search-dropdown-item');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentFocus++;
                addActive(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentFocus--;
                addActive(items);
            } else if (e.key === 'Enter') {
                if (currentFocus > -1 && dropdown.classList.contains('active')) {
                    e.preventDefault();
                    items[currentFocus].click();
                }
            } else if (e.key === 'Escape') {
                dropdown.classList.remove('active');
                input.blur();
            }
        });

        function addActive(items) {
            if (!items || items.length === 0) return;
            removeActive(items);
            if (currentFocus >= items.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (items.length - 1);
            items[currentFocus].classList.add('focused');
            items[currentFocus].scrollIntoView({ block: 'nearest' });
        }

        function removeActive(items) {
            for (let i = 0; i < items.length; i++) {
                items[i].classList.remove('focused');
            }
        }

        // Clicar fora para fechar
        document.addEventListener('click', function(e) {
            if (!form.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });

        // Focar novamente abre o painel se houver texto
        input.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && dropdown.innerHTML !== '') {
                dropdown.classList.add('active');
            }
        });
    });
});