EPI Questionário – WordPress Plugin
Descrição

Plugin WordPress para gerenciar questionários de EPI (Equipamentos de Proteção Individual) e registrar respostas.
Projeto para o curso de extensao das engenharias  "Pi Vida e Carreira".


Funcionalidades Implementadas

Front-end:

Shortcode [epi_questionario] para exibir o questionário.

Formulário gerado a partir das perguntas cadastradas no banco.

Salva respostas serializadas na tabela epi_respostas.

Admin:

Menu principal: EPI Questionário

Submenus:

Perguntas – Adicionar e listar perguntas com categoria e ordem.

Respostas – Listagem das respostas registradas, com filtro por datas.

Alertas – Edição de mensagens de alerta por categoria (funciona apenas se a tabela epi_alertas existir e tiver dados).

Banco de Dados

O plugin espera as seguintes tabelas:

wp_epi_perguntas – Perguntas do questionário.

wp_epi_respostas – Respostas dos usuários, serializadas.

wp_epi_alertas – Mensagens de alerta por categoria (opcional para o front-end).

Estrutura de Arquivos

epi-questionario/

epi-questionario.php : Arquivo principal do plugin

epi-functions.php : Funções auxiliares

admin/epi-admin.php : Admin (Perguntas, Respostas, Alertas)

front/epi-frontend.php : Front-end / shortcode

sql/seed.sql : Seed inicial de perguntas e alertas

Shortcodes

[epi_questionario] – Exibe o formulário do questionário.

Notas Importantes

Alertas dependem da existência da tabela epi_alertas e correspondência exata entre categorias de perguntas e alertas.

Não há autenticação ou restrição de usuários no front-end além do login do WordPress.

Não há upload de fotos, dashboards, relatórios exportáveis ou outras funcionalidades avançadas.

Seed inicial deve ser executado manualmente via SQL.

Instruções de Uso

Copiar a pasta epi-questionario para wp-content/plugins/.

Ativar o plugin no painel WordPress.

Criar as tabelas no banco de dados (wp_epi_perguntas, wp_epi_respostas, wp_epi_alertas) e rodar o seed.

Criar uma página no WordPress e inserir [epi_questionario] para exibir o questionário.

Acessar Admin → EPI Questionário para gerenciar perguntas, alertas e visualizar respostas.

SQL para criar tabelas e popular perguntas/alertas
-- Criar tabela de perguntas
CREATE TABLE IF NOT EXISTS wp_epi_perguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pergunta TEXT NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    ordem INT NOT NULL
);

-- Criar tabela de respostas
CREATE TABLE IF NOT EXISTS wp_epi_respostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    respostas TEXT NOT NULL,
    data_hora DATETIME NOT NULL
);

-- Criar tabela de alertas
CREATE TABLE IF NOT EXISTS wp_epi_alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(100) NOT NULL,
    mensagem VARCHAR(255) NOT NULL
);

-- Seed de perguntas
INSERT INTO wp_epi_perguntas (pergunta, categoria, ordem) VALUES
('A touca cobre completamente os cabelos, sem fios soltos?', 'Touca', 1),
('A touca está em bom estado, sem rasgos ou furos?', 'Touca', 2),
('A touca está limpa e higienizada?', 'Touca', 3),
('A máscara está íntegra, sem sujeira, rasgos ou umidade?', 'Máscara', 4),
('A máscara cobre corretamente nariz, boca e queixo?', 'Máscara', 5),
('Os elásticos da máscara estão firmes e ajustam bem ao rosto?', 'Máscara', 6),
('A máscara está dentro do tempo recomendado de uso?', 'Máscara', 7),
('O avental está em boas condições, sem rasgos ou desgaste?', 'Avental', 8),
('O avental está limpo e apropriado para a atividade?', 'Avental', 9),
('O material do avental é adequado ao risco da tarefa?', 'Avental', 10),
('As luvas estão em bom estado, sem furos ou rasgos?', 'Luvas', 11),
('As luvas estão limpas e adequadas para uso?', 'Luvas', 12),
('O tamanho das luvas é adequado ao usuário?', 'Luvas', 13),
('O tipo de luva é compatível com o risco da atividade?', 'Luvas', 14),
('A bota está em boas condições, sem rachaduras ou desgaste excessivo?', 'Bota', 15),
('O solado da bota está íntegro e antiderrapante?', 'Bota', 16),
('O fechamento da bota (cadarço, elástico ou velcro) está funcionando corretamente?', 'Bota', 17),
('O calçado é adequado ao ambiente de trabalho?', 'Bota', 18),
('As lentes estão em boas condições (sem riscos, trincas ou embaçamento)?', 'Óculos', 19),
('Os óculos estão limpos e oferecem boa visibilidade?', 'Óculos', 20),
('A fixação dos óculos está firme e confortável?', 'Óculos', 21),
('O modelo dos óculos é adequado ao risco da atividade?', 'Óculos', 22),
('O protetor auricular está em boas condições, sem sujeira ou deformações?', 'Protetor Auricular', 23),
('O protetor auricular se adapta corretamente ao ouvido do usuário?', 'Protetor Auricular', 24),
('O nível de atenuação do protetor é adequado ao ruído do ambiente?', 'Protetor Auricular', 25),
('A viseira está em bom estado, sem rachaduras ou arranhões?', 'Viseira', 26),
('A viseira cobre todo o rosto de forma adequada?', 'Viseira', 27),
('A viseira está limpa e permite boa visibilidade?', 'Viseira', 28),
('O modelo da viseira é adequado para a atividade realizada?', 'Viseira', 29),
('O EPI está sendo utilizado durante toda a atividade?', 'Geral', 30),
('O colaborador recebeu treinamento para uso correto deste EPI?', 'Geral', 31),
('Existe necessidade de substituição ou manutenção imediata deste EPI?', 'Geral', 32);

-- Seed de alertas
INSERT INTO wp_epi_alertas (categoria, mensagem) VALUES
('Touca', 'Solicitar ao Técnico de Segurança troca imediata'),
('Máscara', 'Solicitar ao Técnico de Segurança nova máscara ou ajuste'),
('Avental', 'Solicitar ao Técnico de Segurança substituição'),
('Luvas', 'Solicitar ao Técnico de Segurança luvas adequadas'),
('Bota', 'Solicitar ao Técnico de Segurança substituição ou manutenção'),
('Óculos', 'Solicitar ao Técnico de Segurança substituição ou ajuste'),
('Protetor Auricular', 'Solicitar ao Técnico de Segurança ajuste ou substituição'),
('Viseira', 'Solicitar ao Técnico de Segurança modelo adequado'),
('Geral', 'Solicitar orientação ao Técnico de Segurança');
