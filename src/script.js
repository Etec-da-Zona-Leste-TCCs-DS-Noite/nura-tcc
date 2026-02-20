document.addEventListener('DOMContentLoaded', () => {

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
        // Seleciona apenas os itens DESTE container
        const items = container.querySelectorAll('.carousel-item');

        if (!track || items.length === 0) return;

        let currentIndex = 0;
        let autoPlayInterval;
        // Tempo levemente aleatório para não rodarem todos iguais
        const autoPlayDelay = 5000 + Math.random() * 2000;

        const getItemsVisible = () => window.innerWidth >= 768 ? 3 : 1;

        const updateCarousel = () => {
            const itemsVisible = getItemsVisible();
            const moveAmount = 100 / itemsVisible;
            track.style.transform = `translateX(-${currentIndex * moveAmount}%)`;
        };

        const moveNext = () => {
            const itemsVisible = getItemsVisible();
            // Calcula o índice máximo possível
            const maxIndex = Math.max(0, items.length - itemsVisible);

            if (currentIndex < maxIndex) {
                currentIndex++;
            } else {
                currentIndex = 0; // Loop para o início
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

        // Touch Swipe Logic
        let touchStartX = 0;
        let touchEndX = 0;
        track.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
        track.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) { moveNext(); resetAutoPlay(); }
            if (touchEndX - touchStartX > 50) { movePrev(); resetAutoPlay(); }
        }, { passive: true });

        // AutoPlay Logic
        function startAutoPlay() { autoPlayInterval = setInterval(moveNext, autoPlayDelay); }
        function stopAutoPlay() { clearInterval(autoPlayInterval); }
        function resetAutoPlay() { stopAutoPlay(); startAutoPlay(); }

        container.addEventListener('mouseenter', stopAutoPlay);
        container.addEventListener('mouseleave', startAutoPlay);

        // Inicializa
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
                        atualizarContadorCarrinho(data.novaQtd);
                        mostrarToast("Produto adicionado ao carrinho!");
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro. Tente novamente.');
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
                badge.style.cssText = "position: absolute; top: -5px; right: -5px; background: var(--primary); color: white; font-size: 0.7rem; font-weight: bold; min-width: 18px; height: 18px; border-radius: 99px; display: flex; align-items: center; justify-content: center; padding: 0 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);";
                cartIconContainer.appendChild(badge);
            }
            badge.textContent = qtd;
        } else if (badge) {
            badge.remove();
        }
    }

    function mostrarToast(mensagem) {
        let toast = document.getElementById('toast-notification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast-notification';
            toast.style.cssText = "position: fixed; bottom: 20px; right: 20px; background: #1a1a1a; color: white; padding: 12px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 9999; transform: translateY(100px); opacity: 0; transition: all 0.3s ease; display: flex; align-items: center; gap: 10px; font-weight: 500; font-family: sans-serif;";
            document.body.appendChild(toast);
        }
        toast.innerHTML = `<i class="ph-fill ph-check-circle" style="color: #22c55e;"></i> ${mensagem}`;
        requestAnimationFrame(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        });
        setTimeout(() => {
            toast.style.transform = 'translateY(100px)';
            toast.style.opacity = '0';
        }, 3000);
    }
});