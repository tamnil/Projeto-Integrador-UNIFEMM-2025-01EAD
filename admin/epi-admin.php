<?php
if (!defined('ABSPATH')) exit;

function epi_admin_menu() {
    add_menu_page('EPI Questionário', 'EPI Questionário', 'manage_options', 'epi-respostas', 'epi_admin_respostas');
    add_submenu_page('epi-respostas', 'Respostas', 'Respostas', 'manage_options', 'epi-respostas', 'epi_admin_respostas');
    add_submenu_page('epi-respostas', 'Perguntas', 'Perguntas', 'manage_options', 'epi-perguntas', 'epi_admin_perguntas');
    add_submenu_page('epi-respostas', 'Alertas', 'Alertas', 'manage_options', 'epi-alertas', 'epi_admin_alertas');
    add_submenu_page('epi-respostas', 'Análise Gráfica', 'Análise Gráfica', 'manage_options', 'epi-analise-grafica', 'epi_admin_analise_grafica');
}
add_action('admin_menu', 'epi_admin_menu');

// ----------------------------------------
// Admin Perguntas (CRUD simples)
function epi_admin_perguntas(){
    global $wpdb;
    $table = $wpdb->prefix . 'epi_perguntas';

    if(isset($_POST['acao']) && !empty($_POST['pergunta'])){
        $pergunta = sanitize_text_field($_POST['pergunta']);
        $categoria = sanitize_text_field($_POST['categoria']);
        $ordem = intval($_POST['ordem']);
        if($_POST['acao'] == 'adicionar'){
            $wpdb->insert($table, ['pergunta'=>$pergunta, 'categoria'=>$categoria, 'ordem'=>$ordem]);
            echo '<div class="notice notice-success is-dismissible"><p>Pergunta adicionada com sucesso!</p></div>';
        }
    }

    $perguntas = $wpdb->get_results("SELECT * FROM $table ORDER BY ordem ASC");
    echo '<div class="wrap"><h1>Perguntas</h1>';
    echo '<div style="background:white; padding: 20px; border-radius: 4px; margin-top: 20px;"><h2>Adicionar Nova Pergunta</h2><form method="POST">';
    echo '<input type="hidden" name="acao" value="adicionar">';
    echo '<p><strong>Pergunta:</strong><br><input type="text" name="pergunta" required style="width:100%;"></p>';
    echo '<p><strong>Categoria:</strong><br><input type="text" name="categoria" required></p>';
    echo '<p><strong>Ordem:</strong><br><input type="number" name="ordem" required></p>';
    echo '<p><input type="submit" value="Adicionar Pergunta" class="button button-primary"></p>';
    echo '</form></div>';

    echo '<hr><h2>Perguntas existentes</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th style="width:50px;">ID</th><th>Pergunta</th><th>Categoria</th><th style="width:80px;">Ordem</th></tr></thead>';
    echo '<tbody>';
    foreach($perguntas as $p){
        echo "<tr><td>$p->id</td><td>".esc_html($p->pergunta)."</td><td>".esc_html($p->categoria)."</td><td>$p->ordem</td></tr>";
    }
    echo '</tbody></table></div>';
}

// ----------------------------------------
// Admin Respostas com alertas e filtro de datas
function epi_admin_respostas(){
    global $wpdb;
    $table_respostas = $wpdb->prefix . 'epi_respostas';
    $table_perguntas = $wpdb->prefix . 'epi_perguntas';
    $table_alertas = $wpdb->prefix . 'epi_alertas';

    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';
    
    $query = "SELECT * FROM $table_respostas";
    $params = [];
    $where = [];

    if($start_date) {
        $where[] = "data_hora >= %s";
        $params[] = $start_date . ' 00:00:00';
    }
    if($end_date) {
        $where[] = "data_hora <= %s";
        $params[] = $end_date . ' 23:59:59';
    }

    if(!empty($where)){
        $query .= " WHERE " . implode(" AND ", $where);
    }
    $query .= " ORDER BY data_hora DESC";

    $respostas = $wpdb->get_results($wpdb->prepare($query, $params));

    $alertas_db = $wpdb->get_results("SELECT * FROM $table_alertas");
    $alertas = [];
    foreach($alertas_db as $a){
        $alertas[$a->categoria] = $a->mensagem;
    }

    echo '<div class="wrap"><h1>Respostas do Questionário</h1>';
    echo '<form method="GET"><input type="hidden" name="page" value="epi-respostas">';
    echo 'Data início: <input type="date" name="start_date" value="'.esc_attr($start_date).'"> ';
    echo 'Data fim: <input type="date" name="end_date" value="'.esc_attr($end_date).'"> ';
    echo '<input type="submit" value="Filtrar" class="button">';
    echo '</form><hr>';

    foreach($respostas as $r){
        $user = get_userdata($r->user_id);
        $resp_array = maybe_unserialize($r->respostas);
        echo '<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; background:white;">';
        echo '<p><strong>Usuário:</strong> '.($user ? esc_html($user->user_login) : 'Desconhecido').' | <strong>Data:</strong> '.esc_html($r->data_hora).'</p>';

        if(is_array($resp_array)){
            foreach($resp_array as $pid => $resposta){
                $pergunta = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_perguntas WHERE id = %d", intval($pid)));
                
                if($pergunta) { // Verifica se a pergunta ainda existe
                    $alerta = '';
                    if($resposta == 'Não' && isset($alertas[$pergunta->categoria])){
                        $alerta = ' <span style="color:red;">['.esc_html($alertas[$pergunta->categoria]).']</span>';
                    }
                    echo '<p><strong>'.esc_html($pergunta->categoria).':</strong> '.esc_html($pergunta->pergunta).'<br><strong>Resposta:</strong> '.esc_html($resposta).$alerta.'</p>';
                }
            }
        }
        echo '</div>';
    }
    echo '</div>';
}

// ----------------------------------------
// Admin Alertas
function epi_admin_alertas(){
    global $wpdb;
    $table_alertas = $wpdb->prefix . 'epi_alertas';

    if(isset($_POST['acao']) && $_POST['acao'] == 'atualizar'){
        foreach($_POST['mensagem'] as $categoria => $mensagem){
            $wpdb->update(
                $table_alertas, 
                ['mensagem' => sanitize_text_field($mensagem)], 
                ['categoria' => sanitize_text_field($categoria)]
            );
        }
        echo '<div class="notice notice-success is-dismissible"><p>Alertas atualizados com sucesso!</p></div>';
    }

    $alertas = $wpdb->get_results("SELECT * FROM $table_alertas");

    echo '<div class="wrap"><h1>Mensagens de Alerta para “Não”</h1>';
    echo '<div style="background:white; padding: 20px; border-radius: 4px; margin-top: 20px;"><form method="POST">';
    echo '<input type="hidden" name="acao" value="atualizar">';
    foreach($alertas as $a){
        echo '<p><strong>'.esc_html($a->categoria).':</strong><br>';
        echo '<input type="text" name="mensagem['.esc_attr($a->categoria).']" value="'.esc_attr($a->mensagem).'" style="width:100%;"></p>';
    }
    echo '<p><input type="submit" value="Salvar Alterações" class="button button-primary"></p>';
    echo '</form></div></div>';
}

// ----------------------------------------
// Admin Análise Gráfica
function epi_admin_analise_grafica(){
    global $wpdb;
    $table_respostas = $wpdb->prefix . 'epi_respostas';
    $table_perguntas = $wpdb->prefix . 'epi_perguntas';

    // 1. Mapear ID da pergunta para sua categoria
    $perguntas_map = [];
    $perguntas = $wpdb->get_results("SELECT id, categoria FROM $table_perguntas");
    foreach ($perguntas as $p) {
        $perguntas_map[$p->id] = $p->categoria;
    }

    // 2. Processar respostas
    $respostas = $wpdb->get_results("SELECT respostas FROM $table_respostas");
    $data = [];
    $categorias = array_unique(array_values($perguntas_map));

    // Inicializa a estrutura de dados
    foreach ($categorias as $cat) {
        if(!empty($cat)) $data[$cat] = ['Sim' => 0, 'Não' => 0];
    }

    foreach ($respostas as $r) {
        $resp_array = maybe_unserialize($r->respostas);
        if (is_array($resp_array)) {
            foreach ($resp_array as $pid => $resposta) {
                if (isset($perguntas_map[$pid])) {
                    $categoria = $perguntas_map[$pid];
                    if (isset($data[$categoria])) {
                        if ($resposta == 'Sim') {
                            $data[$categoria]['Sim']++;
                        } elseif ($resposta == 'Não') {
                            $data[$categoria]['Não']++;
                        }
                    }
                }
            }
        }
    }

    // 3. Preparar dados para o Chart.js
    $chart_labels = json_encode(array_keys($data));
    $chart_data_sim = json_encode(array_column(array_values($data), 'Sim'));
    $chart_data_nao = json_encode(array_column(array_values($data), 'Não'));

    // 4. Renderizar a página
    ?>
    <div class="wrap">
        <h1>Análise Gráfica das Respostas</h1>
        <div style="max-width: 900px; margin-top: 20px; background: white; padding: 20px; border-radius: 4px;">
            <canvas id="epiChart"></canvas>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('epiChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo $chart_labels; ?>,
                    datasets: [{
                        label: 'Sim',
                        data: <?php echo $chart_data_sim; ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Não',
                        data: <?php echo $chart_data_nao; ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Contagem de Respostas por Categoria'
                        }
                    }
                }
            });
        });
    </script>
    <?php
}

