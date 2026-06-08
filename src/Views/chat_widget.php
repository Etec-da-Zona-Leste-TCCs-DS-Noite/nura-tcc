<script src="https://unpkg.com/@phosphor-icons/web"></script>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    .nura-chat-widget {
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 9999;
        font-family: 'DM Sans', sans-serif;
    }

    /* O botão redondo verde com visual de carrinho */
    .chat-trigger-btn {
        width: 60px;
        height: 60px;
        background: #10b981;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 26px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        transition: transform 0.2s ease, background 0.2s;
        position: relative;
    }

    .chat-trigger-btn:hover {
        transform: scale(1.05);
        background: #059669;
    }

    /* Bolinha vermelha de notificação de itens no carrinho */
    .widget-cart-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        background: #ef4444;
        color: white;
        font-size: 11px;
        font-weight: 700;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Caixa do Chat Inteligente */
    .chat-box-window {
        position: absolute;
        bottom: 75px;
        right: 0;
        width: 360px;
        height: 500px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        display: none;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .chat-box-window.open {
        display: flex;
    }

    .chat-box-header {
        background: #10b981;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-box-header h4 { margin: 0; font-size: 16px; display: flex; align-items: center; gap: 8px; }
    .chat-box-header button { background: transparent; border: none; color: white; cursor: pointer; font-size: 20px; }

    .chat-box-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #f8fafc;
    }

    .widget-msg { display: flex; width: 100%; }
    .widget-msg.user { justify-content: flex-end; }
    .widget-msg.ia { justify-content: flex-start; }

    .widget-bubble {
        max-width: 80%;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.4;
    }
    .widget-msg.user .widget-bubble { background: #10b981; color: white; border-bottom-right-radius: 2px; }
    .widget-msg.ia .widget-bubble { background: white; color: #334155; border-bottom-left-radius: 2px; border: 1px solid #e2e8f0; }

    .chat-box-footer {
        padding: 12px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 8px;
        background: white;
    }

    .chat-box-footer input {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        outline: none;
        font-size: 14px;
    }

    .chat-box-footer input:focus { border-color: #10b981; }

    .chat-box-footer button {
        background: #10b981;
        color: white;
        border: none;
        padding: 0 14px;
        border-radius: 8px;
        cursor: pointer;
    }
</style>

<div class="nura-chat-widget">
    <div class="chat-box-window" id="nuraChatWindow">
        <div class="chat-box-header">
            <h4><i class="ph-bold ph-shopping-cart-simple"></i> Suporte de Compras Nura</h4>
            <button onclick="toggleNuraChat()"><i class="ph-bold ph-x"></i></button>
        </div>
        <div class="chat-box-messages" id="nuraChatMessages">
            <div class="widget-msg ia">
                <div class="widget-bubble">Olá! Sou o assistente de compras da Nura. 🥗 Precisa de ajuda com o seu carrinho, formas de pagamento ou dúvidas sobre os pratos? Pode me perguntar!</div>
            </div>
        </div>
        <div class="chat-box-footer">
            <input type="text" id="nuraWidgetInput" placeholder="Pergunte sobre sua compra..." autocomplete="off">
            <button id="nuraWidgetBtn"><i class="ph-bold ph-paper-plane-right"></i></button>
        </div>
    </div>

    <div class="chat-trigger-btn" onclick="toggleNuraChat()">
        <i class="ph-bold ph-chat-circle-dots"></i>
        
        <?php if (isset($qtdCarrinho) && $qtdCarrinho > 0): ?>
            <span class="widget-cart-badge"><?php echo $qtdCarrinho; ?></span>
        <?php endif; ?>
    </div>

<script>
    const nuraChatWindow = document.getElementById('nuraChatWindow');
    const nuraChatMessages = document.getElementById('nuraChatMessages');
    const nuraWidgetInput = document.getElementById('nuraWidgetInput');
    const nuraWidgetBtn = document.getElementById('nuraWidgetBtn');

    function toggleNuraChat() {
        nuraChatWindow.classList.toggle('open');
        if(nuraChatWindow.classList.contains('open')) {
            nuraWidgetInput.focus();
        }
    }

    function appendWidgetMessage(sender, text) {
        const row = document.createElement('div');
        row.classList.add('widget-msg', sender);
        row.innerHTML = `<div class="widget-bubble">${text}</div>`;
        nuraChatMessages.appendChild(row);
        nuraChatMessages.scrollTop = nuraChatMessages.scrollHeight;
    }

    async function enviarMensagemCliente() {
        const msg = nuraWidgetInput.value.trim();
        if(!msg) return;

        appendWidgetMessage('user', msg);
        nuraWidgetInput.value = '';
        nuraWidgetInput.disabled = true;
        nuraWidgetBtn.disabled = true;

        try {
            const response = await fetch('../api/chat.php', { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mensagem: msg })
            });

            const data = await response.json();
            appendWidgetMessage('ia', data.resposta);

        } catch (error) {
            appendWidgetMessage('ia', "Desculpe, tive um problema ao processar. Tente novamente.");
        } finally {
            nuraWidgetInput.disabled = false;
            nuraWidgetBtn.disabled = false;
            nuraWidgetInput.focus();
        }
    }

    nuraWidgetBtn.addEventListener('click', enviarMensagemCliente);
    nuraWidgetInput.addEventListener('keypress', function(e) {
        if(e.key === 'Enter') enviarMensagemCliente();
    });
</script>