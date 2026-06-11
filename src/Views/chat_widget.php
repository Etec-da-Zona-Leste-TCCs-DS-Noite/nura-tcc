<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    .nura-widget {
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 9999;
        font-family: 'Inter', sans-serif;
    }

    /* === BOTÃO TRIGGER === */
    .nura-trigger {
        width: 56px;
        height: 56px;
        background: #16a34a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        border: none;
        box-shadow: 0 4px 16px rgba(22,163,74,0.35);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .nura-trigger:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(22,163,74,0.45);
    }
    .nura-trigger svg {
        width: 24px;
        height: 24px;
        fill: white;
        transition: opacity 0.2s, transform 0.2s;
    }
    .nura-trigger .icon-chat { opacity: 1; transform: scale(1); position: absolute; }
    .nura-trigger .icon-close { opacity: 0; transform: scale(0.5) rotate(-90deg); position: absolute; }
    .nura-widget.is-open .nura-trigger .icon-chat { opacity: 0; transform: scale(0.5) rotate(90deg); }
    .nura-widget.is-open .nura-trigger .icon-close { opacity: 1; transform: scale(1) rotate(0deg); }

    .nura-badge {
        position: absolute;
        top: -3px;
        right: -3px;
        background: #ef4444;
        color: white;
        font-size: 10px;
        font-weight: 600;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
    }

    /* Pulso animado no trigger quando fechado */
    .nura-trigger::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        border: 2px solid rgba(22,163,74,0.4);
        animation: nura-pulse 2.5s ease-out infinite;
        pointer-events: none;
    }
    .nura-widget.is-open .nura-trigger::after { display: none; }
    @keyframes nura-pulse {
        0% { transform: scale(1); opacity: 0.6; }
        70% { transform: scale(1.4); opacity: 0; }
        100% { transform: scale(1.4); opacity: 0; }
    }

    /* === JANELA DO CHAT === */
    .nura-window {
        position: absolute;
        bottom: 70px;
        right: 0;
        width: 360px;
        height: 520px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.14), 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
        transform-origin: bottom right;
        transform: scale(0.85) translateY(12px);
        opacity: 0;
        pointer-events: none;
        transition: transform 0.22s cubic-bezier(0.34,1.56,0.64,1), opacity 0.18s ease;
    }
    .nura-widget.is-open .nura-window {
        transform: scale(1) translateY(0);
        opacity: 1;
        pointer-events: all;
    }

    /* Header */
    .nura-header {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }
    .nura-avatar {
        width: 38px;
        height: 38px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .nura-header-info { flex: 1; }
    .nura-header-name {
        color: white;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.2;
    }
    .nura-header-status {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 2px;
    }
    .nura-status-dot {
        width: 7px;
        height: 7px;
        background: #86efac;
        border-radius: 50%;
        animation: nura-blink 2s ease-in-out infinite;
    }
    @keyframes nura-blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
    .nura-header-status span {
        color: rgba(255,255,255,0.85);
        font-size: 11px;
    }

    /* Mensagens */
    .nura-messages {
        flex: 1;
        padding: 16px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f9fafb;
        scroll-behavior: smooth;
    }
    .nura-messages::-webkit-scrollbar { width: 4px; }
    .nura-messages::-webkit-scrollbar-track { background: transparent; }
    .nura-messages::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

    .nura-row { display: flex; align-items: flex-end; gap: 7px; }
    .nura-row.user { flex-direction: row-reverse; }

    .nura-row-avatar {
        width: 26px;
        height: 26px;
        background: #dcfce7;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
        margin-bottom: 2px;
    }

    .nura-bubble {
        max-width: 78%;
        padding: 10px 13px;
        border-radius: 16px;
        font-size: 13.5px;
        line-height: 1.5;
        animation: nura-pop 0.18s ease-out;
    }
    @keyframes nura-pop {
        from { transform: scale(0.92); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .nura-row.ia .nura-bubble {
        background: white;
        color: #1f2937;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.07);
        border: 1px solid #f3f4f6;
    }
    .nura-row.user .nura-bubble {
        background: #16a34a;
        color: white;
        border-bottom-right-radius: 4px;
    }

    /* Typing indicator */
    .nura-typing {
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .nura-typing-dots {
        background: white;
        border: 1px solid #f3f4f6;
        box-shadow: 0 1px 3px rgba(0,0,0,0.07);
        padding: 10px 14px;
        border-radius: 16px;
        border-bottom-left-radius: 4px;
        display: flex;
        gap: 4px;
        align-items: center;
    }
    .nura-typing-dots span {
        width: 6px;
        height: 6px;
        background: #9ca3af;
        border-radius: 50%;
        animation: nura-dot 1.2s ease-in-out infinite;
    }
    .nura-typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .nura-typing-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes nura-dot {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
        30% { transform: translateY(-5px); opacity: 1; }
    }

    /* Sugestões rápidas */
    .nura-suggestions {
        padding: 8px 16px 4px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        background: #f9fafb;
        flex-shrink: 0;
    }
    .nura-chip {
        background: white;
        border: 1px solid #e5e7eb;
        color: #374151;
        font-size: 11.5px;
        font-family: 'Inter', sans-serif;
        padding: 5px 10px;
        border-radius: 20px;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
        white-space: nowrap;
    }
    .nura-chip:hover {
        background: #f0fdf4;
        border-color: #16a34a;
        color: #16a34a;
    }

    /* Footer */
    .nura-footer {
        padding: 10px 12px 12px;
        background: white;
        border-top: 1px solid #f3f4f6;
        flex-shrink: 0;
    }
    .nura-input-row {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f9fafb;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 8px 8px 8px 14px;
        transition: border-color 0.15s;
    }
    .nura-input-row:focus-within {
        border-color: #16a34a;
        background: white;
    }
    .nura-input-row input {
        flex: 1;
        border: none;
        background: transparent;
        outline: none;
        font-size: 13.5px;
        font-family: 'Inter', sans-serif;
        color: #1f2937;
    }
    .nura-input-row input::placeholder { color: #9ca3af; }
    .nura-send-btn {
        width: 32px;
        height: 32px;
        background: #16a34a;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: background 0.15s, transform 0.1s;
    }
    .nura-send-btn:hover { background: #15803d; }
    .nura-send-btn:active { transform: scale(0.93); }
    .nura-send-btn svg { width: 15px; height: 15px; fill: white; }
    .nura-send-btn:disabled { background: #d1d5db; cursor: not-allowed; transform: none; }

    .nura-footer-note {
        text-align: center;
        font-size: 10.5px;
        color: #9ca3af;
        margin-top: 7px;
    }
</style>

<div class="nura-widget" id="nuraWidget">

    <!-- JANELA DO CHAT -->
    <div class="nura-window" id="nuraWindow">

        <!-- Header -->
        <div class="nura-header">
            <div class="nura-avatar">🥗</div>
            <div class="nura-header-info">
                <div class="nura-header-name">NutriBot · Nura</div>
                <div class="nura-header-status">
                    <div class="nura-status-dot"></div>
                    <span>Online agora</span>
                </div>
            </div>
        </div>

        <!-- Mensagens -->
        <div class="nura-messages" id="nuraMessages">
            <div class="nura-row ia">
                <div class="nura-row-avatar">🥗</div>
                <div class="nura-bubble">Olá! Sou a NutriBot da Nura 🍃<br>Posso ajudar com pratos, pagamento ou navegação no site. O que você precisa?</div>
            </div>
        </div>

        <!-- Sugestões rápidas -->
        <div class="nura-suggestions" id="nuraSuggestions">
            <button class="nura-chip" onclick="enviarSugestao('Ver cardápio completo')">🥗 Ver cardápio</button>
            <button class="nura-chip" onclick="enviarSugestao('Formas de pagamento')">💳 Pagamento</button>
            <button class="nura-chip" onclick="enviarSugestao('Tenho alergia alimentar')">⚠️ Alergias</button>
        </div>

        <!-- Footer -->
        <div class="nura-footer">
            <div class="nura-input-row">
                <input type="text" id="nuraInput" placeholder="Pergunte sobre sua compra..." autocomplete="off">
                <button class="nura-send-btn" id="nuraSendBtn">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>
            <div class="nura-footer-note">Nura · Alimentação saudável 🍃</div>
        </div>
    </div>

    <!-- BOTÃO TRIGGER -->
    <button class="nura-trigger" id="nuraTrigger" onclick="toggleNura()">
        <!-- Ícone chat -->
        <svg class="icon-chat" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
        </svg>
        <!-- Ícone fechar -->
        <svg class="icon-close" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
        </svg>
        <?php if (isset($qtdCarrinho) && $qtdCarrinho > 0): ?>
            <span class="nura-badge"><?php echo $qtdCarrinho; ?></span>
        <?php endif; ?>
    </button>
</div>

<script>
    const nuraWidget   = document.getElementById('nuraWidget');
    const nuraMessages = document.getElementById('nuraMessages');
    const nuraInput    = document.getElementById('nuraInput');
    const nuraSendBtn  = document.getElementById('nuraSendBtn');
    const nuraSuggestions = document.getElementById('nuraSuggestions');

    function toggleNura() {
        nuraWidget.classList.toggle('is-open');
        if (nuraWidget.classList.contains('is-open')) {
            nuraInput.focus();
        }
    }

    function addMessage(sender, text) {
        // Esconde sugestões após primeira interação do usuário
        if (sender === 'user') nuraSuggestions.style.display = 'none';

        const row = document.createElement('div');
        row.classList.add('nura-row', sender);

        if (sender === 'ia') {
            row.innerHTML = `<div class="nura-row-avatar">🥗</div><div class="nura-bubble">${text}</div>`;
        } else {
            row.innerHTML = `<div class="nura-bubble">${text}</div>`;
        }

        nuraMessages.appendChild(row);
        nuraMessages.scrollTop = nuraMessages.scrollHeight;
    }

    function showTyping() {
        const el = document.createElement('div');
        el.classList.add('nura-row', 'ia');
        el.id = 'nuraTyping';
        el.innerHTML = `
            <div class="nura-row-avatar">🥗</div>
            <div class="nura-typing-dots">
                <span></span><span></span><span></span>
            </div>`;
        nuraMessages.appendChild(el);
        nuraMessages.scrollTop = nuraMessages.scrollHeight;
    }

    function removeTyping() {
        const el = document.getElementById('nuraTyping');
        if (el) el.remove();
    }

    async function enviar(msg) {
        if (!msg) return;
        addMessage('user', msg);
        nuraInput.value = '';
        nuraInput.disabled = true;
        nuraSendBtn.disabled = true;
        showTyping();

        try {
            const response = await fetch('../api/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mensagem: msg })
            });
            const data = await response.json();
            removeTyping();
            addMessage('ia', data.resposta);
        } catch (e) {
            removeTyping();
            addMessage('ia', 'Desculpe, tive um problema de conexão. Tente novamente 🙏');
        } finally {
            nuraInput.disabled = false;
            nuraSendBtn.disabled = false;
            nuraInput.focus();
        }
    }

    function enviarSugestao(texto) {
        enviar(texto);
    }

    nuraSendBtn.addEventListener('click', () => enviar(nuraInput.value.trim()));
    nuraInput.addEventListener('keypress', e => { if (e.key === 'Enter') enviar(nuraInput.value.trim()); });
</script>