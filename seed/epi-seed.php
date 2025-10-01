<?php
if (!defined('ABSPATH')) exit;

function epi_seed_initial() {
    global $wpdb;

    $table_perguntas = $wpdb->prefix . 'epi_perguntas';
    $table_respostas = $wpdb->prefix . 'epi_respostas';
    $table_alertas = $wpdb->prefix . 'epi_alertas';

    // Cria tabelas se não existirem
    $charset_collate = $wpdb->get_charset_collate();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Definição das tabelas - ajustado para consistência com README
    $sql = "CREATE TABLE IF NOT EXISTS $table_perguntas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pergunta TEXT NOT NULL,
        categoria VARCHAR(100) NOT NULL,
        ordem INT NOT NULL
    ) $charset_collate;
    CREATE TABLE IF NOT EXISTS $table_respostas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT NOT NULL,
        respostas TEXT NOT NULL,
        data_hora DATETIME NOT NULL
    ) $charset_collate;
    CREATE TABLE IF NOT EXISTS $table_alertas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        categoria VARCHAR(100) NOT NULL,
        mensagem VARCHAR(255) NOT NULL
    ) $charset_collate;";

    dbDelta($sql);

    // Seed inicial de alertas
    $alertas = [
        ['categoria'=>'Touca','mensagem'=>'Solicitar ao Técnico de Segurança troca imediata'],
        ['categoria'=>'Máscara','mensagem'=>'Solicitar ao Técnico de Segurança nova máscara ou ajuste'],
        ['categoria'=>'Avental','mensagem'=>'Solicitar ao Técnico de Segurança substituição'],
        ['categoria'=>'Luvas','mensagem'=>'Solicitar ao Técnico de Segurança luvas adequadas'],
        ['categoria'=>'Bota','mensagem'=>'Solicitar ao Técnico de Segurança substituição ou manutenção'],
        ['categoria'=>'Óculos','mensagem'=>'Solicitar ao Técnico de Segurança substituição ou ajuste'],
        ['categoria'=>'Protetor Auricular','mensagem'=>'Solicitar ao Técnico de Segurança ajuste ou substituição'],
        ['categoria'=>'Viseira','mensagem'=>'Solicitar ao Técnico de Segurança modelo adequado'],
        ['categoria'=>'Geral','mensagem'=>'Solicitar orientação ao Técnico de Segurança']
    ];

    foreach($alertas as $alerta){
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_alertas WHERE categoria = %s", $alerta['categoria']));
        if(!$exists){
            $wpdb->insert($table_alertas, $alerta);
        }
    }

    // Seed inicial de perguntas (só executa se a tabela estiver vazia)
    $perguntas_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_perguntas");
    if($perguntas_count == 0) {
        $perguntas = [
            ['pergunta'=>'A touca cobre completamente os cabelos, sem fios soltos?', 'categoria'=>'Touca', 'ordem'=>1],
            ['pergunta'=>'A touca está em bom estado, sem rasgos ou furos?', 'categoria'=>'Touca', 'ordem'=>2],
            ['pergunta'=>'A touca está limpa e higienizada?', 'categoria'=>'Touca', 'ordem'=>3],
            ['pergunta'=>'A máscara está íntegra, sem sujeira, rasgos ou umidade?', 'categoria'=>'Máscara', 'ordem'=>4],
            ['pergunta'=>'A máscara cobre corretamente nariz, boca e queixo?', 'categoria'=>'Máscara', 'ordem'=>5],
            ['pergunta'=>'Os elásticos da máscara estão firmes e ajustam bem ao rosto?', 'categoria'=>'Máscara', 'ordem'=>6],
            ['pergunta'=>'A máscara está dentro do tempo recomendado de uso?', 'categoria'=>'Máscara', 'ordem'=>7],
            ['pergunta'=>'O avental está em boas condições, sem rasgos ou desgaste?', 'categoria'=>'Avental', 'ordem'=>8],
            ['pergunta'=>'O avental está limpo e apropriado para a atividade?', 'categoria'=>'Avental', 'ordem'=>9],
            ['pergunta'=>'O material do avental é adequado ao risco da tarefa?', 'categoria'=>'Avental', 'ordem'=>10],
            ['pergunta'=>'As luvas estão em bom estado, sem furos ou rasgos?', 'categoria'=>'Luvas', 'ordem'=>11],
            ['pergunta'=>'As luvas estão limpas e adequadas para uso?', 'categoria'=>'Luvas', 'ordem'=>12],
            ['pergunta'=>'O tamanho das luvas é adequado ao usuário?', 'categoria'=>'Luvas', 'ordem'=>13],
            ['pergunta'=>'O tipo de luva é compatível com o risco da atividade?', 'categoria'=>'Luvas', 'ordem'=>14],
            ['pergunta'=>'A bota está em boas condições, sem rachaduras ou desgaste excessivo?', 'categoria'=>'Bota', 'ordem'=>15],
            ['pergunta'=>'O solado da bota está íntegro e antiderrapante?', 'categoria'=>'Bota', 'ordem'=>16],
            ['pergunta'=>'O fechamento da bota (cadarço, elástico ou velcro) está funcionando corretamente?', 'categoria'=>'Bota', 'ordem'=>17],
            ['pergunta'=>'O calçado é adequado ao ambiente de trabalho?', 'categoria'=>'Bota', 'ordem'=>18],
            ['pergunta'=>'As lentes estão em boas condições (sem riscos, trincas ou embaçamento)?', 'categoria'=>'Óculos', 'ordem'=>19],
            ['pergunta'=>'Os óculos estão limpos e oferecem boa visibilidade?', 'categoria'=>'Óculos', 'ordem'=>20],
            ['pergunta'=>'A fixação dos óculos está firme e confortável?', 'categoria'=>'Óculos', 'ordem'=>21],
            ['pergunta'=>'O modelo dos óculos é adequado ao risco da atividade?', 'categoria'=>'Óculos', 'ordem'=>22],
            ['pergunta'=>'O protetor auricular está em boas condições, sem sujeira ou deformações?', 'categoria'=>'Protetor Auricular', 'ordem'=>23],
            ['pergunta'=>'O protetor auricular se adapta corretamente ao ouvido do usuário?', 'categoria'=>'Protetor Auricular', 'ordem'=>24],
            ['pergunta'=>'O nível de atenuação do protetor é adequado ao ruído do ambiente?', 'categoria'=>'Protetor Auricular', 'ordem'=>25],
            ['pergunta'=>'A viseira está em bom estado, sem rachaduras ou arranhões?', 'categoria'=>'Viseira', 'ordem'=>26],
            ['pergunta'=>'A viseira cobre todo o rosto de forma adequada?', 'categoria'=>'Viseira', 'ordem'=>27],
            ['pergunta'=>'A viseira está limpa e permite boa visibilidade?', 'categoria'=>'Viseira', 'ordem'=>28],
            ['pergunta'=>'O modelo da viseira é adequado para a atividade realizada?', 'categoria'=>'Viseira', 'ordem'=>29],
            ['pergunta'=>'O EPI está sendo utilizado durante toda a atividade?', 'categoria'=>'Geral', 'ordem'=>30],
            ['pergunta'=>'O colaborador recebeu treinamento para uso correto deste EPI?', 'categoria'=>'Geral', 'ordem'=>31],
            ['pergunta'=>'Existe necessidade de substituição ou manutenção imediata deste EPI?', 'categoria'=>'Geral', 'ordem'=>32]
        ];

        foreach($perguntas as $p){
            $wpdb->insert($table_perguntas, $p);
        }
    }
}

