-- 1. Cria a Tabela de Usuários/Clientes (Necessária para Cadastro e Login)
CREATE TABLE cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefone VARCHAR(20),
    senha VARCHAR(255) NOT NULL
);

-- 2. Cria a Tabela do Perfil de Saúde (Necessária para os Alertas)
CREATE TABLE perfil_clinico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    peso DECIMAL(5,2),
    altura DECIMAL(4,2),
    restricao VARCHAR(50),
    alergias TEXT,
    created_at DATETIME,
    updated_at DATETIME
);
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    img VARCHAR(500),
    tag VARCHAR(100),
    alergias JSON,
    restricoes JSON,
    estoque INT NOT NULL DEFAULT 15
);

INSERT INTO produtos (id, nome, descricao, preco, img, tag, alergias, restricoes) VALUES
(1, 'Bowl Verde Vitality', 'Mix de folhas frescas, abacate, quinoa e grão de bico.', 32.90, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500', 'Bowls', '[]', '[]'),
(2, 'Poke de Salmão Defumado', 'Salmão fresco, arroz gohan, manga, sunomono e molho tarê.', 45.00, 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500', 'Bowls', '["frutos_mar", "soja"]', '["vegano", "vegetariano", "celiaco"]'),
(3, 'Bowl Proteico de Tofu', 'Tofu marinado no shoyu, edamame, cogumelos salteados e arroz integral.', 34.50, 'https://images.unsplash.com/photo-1543339308-43e59d6b73a6?w=500', 'Bowls', '["soja"]', '["celiaco"]'),
(4, 'Mix Grelhado do Chef', 'Cubos de frango orgânico, batata doce, brócolis e ovo cozido.', 38.00, 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=500', 'Bowls', '["ovo"]', '["vegano", "vegetariano"]'),
(5, 'Salada Caesar Clássica', 'Alface romana, croutons, queijo parmesão e autêntico molho caesar.', 30.00, 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=500', 'Saladas', '["ovo", "soja"]', '["vegano", "vegetariano", "intolerancia_lactose", "celiaco"]'),
(6, 'Salada Thai com Camarões', 'Camarões grelhados, cenoura ralada, castanha-de-caju e molho sweet chili.', 42.50, 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=500', 'Saladas', '["frutos_mar", "amendoim"]', '["vegano", "vegetariano"]'),
(7, 'Salada Color Nura', 'Tomate cereja, pepino, rabanete, repolho roxo e sementes de abóbora.', 28.50, 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=500', 'Saladas', '[]', '[]'),
(8, 'Wrap Leve de Frango', 'Frango magro grelhado, cream cheese verde na tortilha de trigo.', 24.90, 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=500', 'Wraps', '[]', '["vegano", "vegetariano", "intolerancia_lactose", "celiaco"]'),
(9, 'Wrap Doce de Amendoim', 'Massa integral, fatias de maçã, canela e pasta de amendoim caseira.', 22.90, 'https://www.receitasnestle.com.br/sites/default/files/styles/recipe_detail_desktop_new/public/srh_recipes/bca8119743e8c9eb43c7c78fb6bf36e0.webp?itok=VPZxIonw', 'Wraps', '["amendoim"]', '["celiaco"]'),
(10, 'Smoothie Antioxidante', 'Maçã verde, abacaxi, couve, hortelã e um toque de limão.', 18.00, 'https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=500', 'Sucos', '[]', '[]'),
(12, 'Limonada Suíça Fit', 'Limão galego original batido em clara de ovo (espuma) e bastante gelo.', 14.00, 'https://img.freepik.com/fotos-gratis/fatias-de-frutas-perto-de-copo-de-bebida-com-gelo-e-ervas-na-mesa_23-2148107706.jpg?semt=ais_hybrid&w=740&q=80', 'Sucos', '["ovo"]', '["vegano"]'),
(14, 'Salada Mediterrânea', 'Grão de bico, rúcula, lascas de queijo feta, azeitonas e molho balsâmico.', 31.00, 'https://images.unsplash.com/photo-1551248429-40975aa4de74?w=500', 'Saladas', '[]', '["vegano", "intolerancia_lactose"]'),
(17, 'Suco Sunshine Natural', 'Mistura imbatível de suco de cenoura, laranja e um leve toque de gengibre.', 15.50, 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=500', 'Sucos', '[]', '[]'),
(18, 'Bowl de Frango Teriyaki', 'Frango ao molho teriyaki natural, edamame, gergelim e cenoura.', 35.50, 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=500', 'Bowls', '["soja"]', '["vegano", "vegetariano", "celiaco"]'),
(19, 'Salada Caprese Tostada', 'Mussarela de búfala fresca, tomates adocicados ao azeite e manjericão.', 33.00, 'https://images.unsplash.com/photo-1592417817098-8fd3d9eb14a5?w=500', 'Saladas', '[]', '["vegano", "intolerancia_lactose"]'),
(20, 'Suco Verde Metrópole', 'Aipo puro, pepino congelado, maçã verde importada e couve.', 17.50, 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=500', 'Sucos', '[]', '[]'),
(22, 'Sanduíche Caprese no Pão Sírio', 'Pão sírio levemente tostado recheado com mussarela de búfala, rúcula e tomate.', 26.90, 'https://images.unsplash.com/photo-1619096252214-ef06c45683e3?w=500', 'Wraps', '[]', '["vegano", "intolerancia_lactose", "celiaco"]');

-- 4. Cria a Tabela de Pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    frete DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    endereco TEXT,
    metodo_pagamento VARCHAR(50) DEFAULT 'PIX',
    dados_pagamento TEXT,
    itens JSON NOT NULL,
    status VARCHAR(50) DEFAULT 'Em Preparo',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES cliente(id) ON DELETE CASCADE
);

-- 5. Cria a Tabela de Administradores
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insere Administrador Padrão
INSERT INTO admin (id, nome, email, senha) VALUES
(1, 'Administrador Nura', 'admin@nura.com', '$2y$10$FyyOW92LPnX0q0x6kF2G8u25bbx6SyraHy.wP0xsrT7GHYP4IqjTy')
ON DUPLICATE KEY UPDATE id=id;