<?php
if (!defined('ABSPATH')) exit;

// Função para mostrar questionário no front-end
function epi_shortcode_questionario($atts){
    if(!is_user_logged_in()){
        return '<p>Por favor, faça login para preencher o questionário.</p>';
    }

    global $wpdb;
    $table_respostas = $wpdb->prefix . 'epi_respostas';
    $table_perguntas = $wpdb->prefix . 'epi_perguntas';
    $table_alertas = $wpdb->prefix . 'epi_alertas';
    $mensagem_feedback = '';

    // Processa o envio do formulário
    if(isset($_POST['resposta']) && is_array($_POST['resposta'])){
        $user_id = get_current_user_id();
        $respostas = [];
        foreach($_POST['resposta'] as $pergunta_id => $valor){
            $respostas[intval($pergunta_id)] = sanitize_text_field($valor);
        }

        $dados_para_salvar = [
            'user_id' => $user_id,
            'respostas' => maybe_serialize($respostas),
            'data_hora' => current_time('mysql')
        ];

        if($wpdb->insert($table_respostas, $dados_para_salvar)){
            $mensagem_feedback = '<p style="color:green;">Questionário enviado com sucesso!</p>';
        } else {
            $mensagem_feedback = '<p style="color:red;">Ocorreu um erro ao enviar o questionário.</p>';
        }
    }

    $perguntas = $wpdb->get_results("SELECT * FROM $table_perguntas ORDER BY ordem ASC");
    $alertas_db = $wpdb->get_results("SELECT categoria, mensagem FROM $table_alertas", OBJECT_K);

    ob_start();

    echo $mensagem_feedback;

    ?>
    <form method="POST" id="epi-form" action="">
        <?php foreach($perguntas as $p): ?>
            <p data-categoria="<?php echo esc_attr($p->categoria); ?>">
                <strong><?php echo esc_html($p->categoria); ?>:</strong> <?php echo esc_html($p->pergunta); ?><br>
                <label><input type="radio" name="resposta[<?php echo $p->id; ?>]" value="Sim" required> Sim</label>
                <label><input type="radio" name="resposta[<?php echo $p->id; ?>]" value="Não"> Não</label>
                <span class="epi-alerta" style="color:red; display:none; margin-left: 10px;"></span>
            </p>
        <?php endforeach; ?>
        <input type="submit" value="Enviar Questionário">
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertas = <?php echo json_encode($alertas_db); ?>;

            const form = document.getElementById('epi-form');
            if(form) {
                form.addEventListener('change', function(e) {
                    if (e.target.type === 'radio' && e.target.name.startsWith('resposta[')) {
                        const p = e.target.closest('p');
                        const categoria = p.dataset.categoria;
                        const alertaSpan = p.querySelector('.epi-alerta');
                        
                        if (e.target.value === 'Não' && alertas[categoria]) {
                            alertaSpan.textContent = '[' + alertas[categoria].mensagem + ']';
                            alertaSpan.style.display = 'inline';
                        } else {
                            alertaSpan.style.display = 'none';
                        }
                    }
                });
            }
        });
    </script>
    <?php
    return ob_get_clean();
}

