<?php
// Controller/IaController.php

class IaController {
    
    public function processarChat() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $mensagemUsuario = $input['mensagem'] ?? '';

        if (empty(trim($mensagemUsuario))) {
            echo json_encode(['resposta' => 'Por favor, digite uma mensagem válida.']);
            exit;
        }

        // SUA CHAVE DA API DEEPSEEK
        $apiKey = "sk-3763344fde4546b2a72b948465cbc88a"; 
        $url = "https://api.deepseek.com/v1/chat/completions";

        // === REGRAS DE BLINDAGEM E DIRETRIZES DA IA ===
        $systemPrompt = "Você é a NutriBot, a assistente virtual oficial da Nura (loja de alimentação saudável). " .
                        "Sua missão é ajudar o cliente a navegar pelo site, escolher pratos, tirar dúvidas e finalizar a compra.\n\n" .
                        
                        "DIRETRIZES DE ESCOPO (REGRA MAIS IMPORTANTE):\n" .
                        "- Responda APENAS assuntos relacionados à Nura, alimentação saudável, ingredientes, funcionamento da plataforma, pagamentos ou suporte.\n" .
                        "- Se o usuário perguntar qualquer coisa fora desse escopo (ex: piadas, futebol, política, receitas caseiras, códigos, outros sites), recuse educadamente dizendo que você foi projetada exclusivamente para ajudar com a Nura.\n\n" .
                        
                        "GUIA DE RESPOSTAS DO SITE:\n" .
                        "1. COMPRA E PRODUTOS: Ajude o cliente a escolher. Fale sobre nossos pratos balanceados, bowls, saladas e smoothies 100% naturais. Incentive o cliente a clicar no botão 'Adicionar' do prato ou ir até a página de 'Produtos' no menu superior.\n" .
                        "2. PAGAMENTO: Informe que aceitamos as principais bandeiras de cartão (Visa, Mastercard, Elo, Amex), Cartão de Débito e PIX. O pagamento é 100% seguro com tecnologia Google Safe Browsing.\n" .
                        "3. NAVEGAÇÃO: Oriente o cliente sobre o menu superior: 'Início' para os destaques, 'Produtos' para o cardápio completo, e 'Minha Conta' para se cadastrar ou gerenciar o perfil clínico.\n" .
                        "4. PERFIL CLÍNICO E ALERGIAS: Explique que no menu 'Minha Conta', o cliente pode preencher suas alergias e restrições alimentares. O nosso sistema filtra os produtos automaticamente para que nada que faça mal apareça para ele.\n" .
                        "5. SUPORTE: Se o cliente tiver algum problema com o pedido ou precisar de ajuda humana, passe o e-mail oficial: contato@nura.com.br ou o telefone (11) 98765-4321.\n\n" .
                        
                        "TOM DA CONVERSA:\n" .
                        "- Seja muito gentil, acolhedora e prestativa.\n" .
                        "- Use respostas diretas e curtas (máximo 3 parágrafos) para facilitar a leitura no chat flutuante.\n" .
                        "- Use emojis de forma moderada (🥗, 🍃, 😊, 🛒, 💳).";

        $dados = [
            "model" => "deepseek-chat",
            "messages" => [
                [
                    "role" => "system",
                    "content" => $systemPrompt
                ],
                [
                    "role" => "user",
                    "content" => $mensagemUsuario
                ]
            ],
            "temperature" => 0.4 // Temperatura mais baixa deixa a IA mais focada e menos propensa a inventar coisas
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $apiKey
        ]);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo json_encode(['resposta' => 'Ops! Senti uma instabilidade na minha conexão. Pode tentar enviar novamente?']);
            curl_close($ch);
            exit;
        }

        curl_close($ch);

        $resultado = json_decode($response, true);
        $respostaDaIa = $resultado['choices'][0]['message']['content'] ?? 'Não consegui processar sua resposta agora.';

        echo json_encode(['resposta' => $respostaDaIa]);
        exit;
    }
}

if (basename($_SERVER['PHP_SELF']) == 'IaController.php') {
    $controller = new IaController();
    $controller->processarChat();
}