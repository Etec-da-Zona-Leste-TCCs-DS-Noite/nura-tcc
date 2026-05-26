<?php
session_start();
$nomeCliente = $_SESSION['cliente_nome'] ?? null;
$carrinho = $_SESSION['carrinho'] ?? [];

// Calcula Totais
$subtotal = 0;
foreach ($carrinho as $item) {
    $subtotal += $item['preco'] * $item['qtd'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura - Carrinho</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body class="page-carrinho">

    <header>
        <div class="container header-inner">
            <a href="index.php" class="logo" aria-label="Nura — Início">
                <img class="logo-img" src="../assets/img/NURA_logo.png" alt="">
            </a>
            <nav class="nav-links" aria-label="Principal">
                <a href="index.php">Início</a>
                <a href="produtos.php">Produtos</a>
                <a href="<?php echo $nomeCliente ? 'perfil.php' : 'cadastro.php'; ?>">Minha Conta</a>
            </nav>

            <form class="header-search" action="produtos.php" method="GET">
                <div class="search-input-wrapper">
                    <i class="ph ph-magnifying-glass search-icon" aria-hidden="true"></i>
                    <input type="text" name="busca" placeholder="Buscar pratos..." aria-label="Buscar" required>
                </div>
            </form>

            <div class="header-actions">
                <a href="<?php echo $nomeCliente ? 'perfil.php' : 'cadastro.php'; ?>" class="btn btn-ghost"
                    aria-label="Conta">
                    <?php if ($nomeCliente): ?>
                        <span class="header-user-label">Olá, <?php echo htmlspecialchars(explode(' ', trim($nomeCliente))[0]); ?></span>
                    <?php endif; ?>
                    <i class="ph ph-user header-icon" aria-hidden="true"></i>
                </a>
            </div>
            <button type="button" class="mobile-menu-btn btn btn-ghost" aria-label="Abrir menu">
                <i class="ph ph-list header-icon" aria-hidden="true"></i>
            </button>
        </div>
    </header>

    <main class="container cart-page">
        <h1 class="cart-page-title">Seu carrinho</h1>

        <?php if (empty($carrinho)): ?>
            <div class="cart-empty">
                <i class="ph ph-shopping-cart cart-empty-icon" aria-hidden="true"></i>
                <p>Seu carrinho está vazio.</p>
                <a href="produtos.php" class="btn btn-primary">Ver cardápio</a>
            </div>
            <?php
        else: ?>

            <div class="cart-lines">
                <?php foreach ($carrinho as $item): ?>
                    <div class="order-card">
                        <img src="<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" class="order-card-thumb">

                        <div class="order-card-body">
                            <h3><?php echo htmlspecialchars($item['nome']); ?></h3>
                            <div class="order-card-price">
                                R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?>
                            </div>
                        </div>

                        <div class="qty-row">
                            <a href="carrinho_acoes.php?acao=atualizar&id=<?php echo (int)$item['id']; ?>&qtd=<?php echo (int)$item['qtd'] - 1; ?>"
                                class="btn btn-ghost qty-btn"
                                <?php echo $item['qtd'] == 1 ? 'onclick="mostrarModalDelete(event, this.href);"' : ''; ?>>−</a>

                            <span class="qty-value"><?php echo (int)$item['qtd']; ?></span>

                            <a href="carrinho_acoes.php?acao=atualizar&id=<?php echo (int)$item['id']; ?>&qtd=<?php echo (int)$item['qtd'] + 1; ?>"
                                class="btn btn-ghost qty-btn">+</a>

                            <a href="carrinho_acoes.php?acao=remover&id=<?php echo (int)$item['id']; ?>" class="btn btn-ghost cart-remove"
                                title="Remover item"
                                onclick="mostrarModalDelete(event, this.href);">
                                <i class="ph ph-trash" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                    <?php
                endforeach; ?>
            </div>

            <!-- Formulário que engloba o resumo para mandar via POST para o checkout -->
            <form action="Checkout/checkout.php" method="POST" class="cart-summary" style="height: fit-content;">
                <h3 style="font-family: 'Outfit'; margin-bottom: 1rem;">Entrega e Resumo</h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.875rem; font-weight: 600; color: var(--muted); display: block; margin-bottom: 0.5rem;">Calcular Frete</label>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <input type="text" id="cart-cep" name="cep" class="input" placeholder="00000-000" style="flex: 1;">
                        <button type="button" id="cart-btn-geolocate" class="btn btn-outline" style="padding: 0 1rem;" title="Usar minha localização">
                            <i class="ph-bold ph-map-pin"></i>
                        </button>
                    </div>
                    <input type="text" id="cart-endereco" name="endereco" class="input" placeholder="Rua, Bairro, Cidade" readonly style="background: var(--surface-hover); color: var(--muted); font-size: 0.85rem;">
                </div>
                <div class="cart-summary-row">
                    <span>Subtotal</span>
                    <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                </div>
                <div class="cart-summary-row">
                    <span>Entrega</span>
                    <span id="cart-display-frete" style="color: var(--muted); font-size: 0.875rem;">Calculando...</span>
                </div>
                <div class="cart-summary-total">
                    <span>Total estimado</span>
                    <span id="cart-display-total">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                </div>

                <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                <input type="hidden" name="frete" id="cart-input-frete" value="0.00">

                <?php if ($nomeCliente): ?>
                    <button type="submit" id="btn-go-checkout" class="btn btn-primary btn-full" style="margin-top: 1.5rem; padding: 1rem;">Ir para o Checkout</button>
                    <?php
                else: ?>
                    <button type="button" class="btn btn-primary btn-full" style="margin-top: 1.5rem; padding: 1rem;"
                        onclick="window.location.href='cadastro.php'">Faça login para finalizar</button>
                    <?php
                endif; ?>
            </form>

            <?php
        endif; ?>
    </main>

    <!-- CUSTOM DELETE MODAL -->
    <div class="modal-overlay" id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
        <div class="custom-modal">
            <div class="modal-icon">
                <i class="ph-fill ph-warning-circle" aria-hidden="true"></i>
            </div>
            <h2 class="modal-title" id="delete-modal-title">Poxa vida...</h2>
            <p class="modal-text">Você tem certeza que deseja remover essa delícia do seu pedido? O seu prato vai ficar tão triste sem ele!</p>
            <div class="modal-actions">
                <a href="#" class="btn btn-primary" id="confirmDeleteLink">Sim, remover do carrinho</a>
                <button type="button" class="btn btn-secondary" onclick="fecharModalDelete()">Não, manter no pedido</button>
            </div>
        </div>
    </div>

    <script>
        function mostrarModalDelete(event, urlOriginal) {
            event.preventDefault();
            document.getElementById('confirmDeleteLink').href = urlOriginal;
            document.getElementById('deleteModal').classList.add('active');
        }

        function fecharModalDelete() {
            document.getElementById('deleteModal').classList.remove('active');
        }

        // Lógica de Frete no Carrinho
        document.addEventListener('DOMContentLoaded', () => {
            const btnGeolocate = document.getElementById('cart-btn-geolocate');
            const inputCep = document.getElementById('cart-cep');
            const inputEndereco = document.getElementById('cart-endereco');
            const displayFrete = document.getElementById('cart-display-frete');
            const displayTotal = document.getElementById('cart-display-total');
            const inputFreteHidden = document.getElementById('cart-input-frete');
            const btnGoCheckout = document.getElementById('btn-go-checkout');
            const subtotal = <?php echo $subtotal; ?>;

            const storeLat = -23.5505;
            const storeLon = -46.6333;

            function deg2rad(deg) { return deg * (Math.PI/180); }
            function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
                var R = 6371; 
                var dLat = deg2rad(lat2-lat1);  
                var dLon = deg2rad(lon2-lon1); 
                var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2); 
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
                return R * c; 
            }

            function atualizarFreteETotal(freteValor) {
                inputFreteHidden.value = freteValor.toFixed(2);
                displayFrete.textContent = 'R$ ' + freteValor.toFixed(2).replace('.', ',');
                displayFrete.style.color = 'var(--foreground)';
                displayFrete.style.fontWeight = '600';
                const total = subtotal + freteValor;
                displayTotal.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
            }

            if(btnGeolocate) {
                btnGeolocate.addEventListener('click', () => {
                    if (!navigator.geolocation) {
                        window.NuraNotify.toast('Geolocalização não suportada', 'error');
                        return;
                    }
                    btnGeolocate.innerHTML = '<i class="ph ph-spinner ph-spin"></i>';
                    navigator.geolocation.getCurrentPosition(async (position) => {
                        try {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;
                            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`);
                            const data = await response.json();
                            
                            if (data && data.address) {
                                const road = data.address.road || data.address.pedestrian || '';
                                const suburb = data.address.suburb || data.address.neighbourhood || '';
                                const city = data.address.city || data.address.town || '';
                                const postcode = data.address.postcode || '';
                                
                                inputEndereco.value = `${road}${suburb ? ', ' + suburb : ''}, ${city}`;
                                if (postcode) inputCep.value = postcode;

                                const distance = getDistanceFromLatLonInKm(storeLat, storeLon, lat, lon);
                                const frete = 5.00 + (distance * 2.50);
                                atualizarFreteETotal(frete);
                                window.NuraNotify.toast('Frete calculado com base na sua localização!', 'success');
                            }
                        } catch (err) {
                            window.NuraNotify.toast('Erro ao buscar o endereço', 'error');
                        } finally {
                            btnGeolocate.innerHTML = '<i class="ph-bold ph-map-pin"></i>';
                        }
                    }, () => {
                        window.NuraNotify.toast('Permissão de localização negada.', 'error');
                        btnGeolocate.innerHTML = '<i class="ph-bold ph-map-pin"></i>';
                    });
                });
            }

            if(inputCep) {
                inputCep.addEventListener('input', async (e) => {
                    // Máscara automática de CEP
                    let val = e.target.value.replace(/\D/g, '');
                    if (val.length > 5) {
                        val = val.substring(0, 5) + '-' + val.substring(5, 8);
                    }
                    e.target.value = val;

                    const cepNumbers = val.replace(/\D/g, '');
                    
                    if (cepNumbers.length === 8) {
                        inputEndereco.value = "Buscando endereço...";
                        try {
                            const response = await fetch(`https://viacep.com.br/ws/${cepNumbers}/json/`);
                            const data = await response.json();
                            if(!data.erro) {
                                inputEndereco.value = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
                                // Simulação de frete fixo quando buscou por CEP (poderia consultar API dos Correios aqui)
                                atualizarFreteETotal(12.50);
                                window.NuraNotify.toast('Endereço e frete atualizados com sucesso.', 'success');
                            } else {
                                inputEndereco.value = "";
                                window.NuraNotify.toast('CEP não encontrado.', 'error');
                            }
                        } catch(err) {
                            console.error(err);
                            inputEndereco.value = "";
                        }
                    } else {
                        // Reseta frete se apagar o CEP
                        if (inputFreteHidden.value !== "0.00") {
                            atualizarFreteETotal(0);
                            inputEndereco.value = "";
                            displayFrete.textContent = 'Calculando...';
                            displayFrete.style.color = 'var(--muted)';
                            displayFrete.style.fontWeight = 'normal';
                        }
                    }
                });
            }
            
            if(btnGoCheckout) {
                btnGoCheckout.addEventListener('click', (e) => {
                    if (inputFreteHidden.value === "0.00") {
                        e.preventDefault();
                        window.NuraNotify.toast('Calcule o frete para continuar.', 'warning');
                    }
                });
            }
        });
    </script>
    <script src="../script.js"></script>
</body>

</html>