<?php
/**
 * Plugin Name: EPI Questionário
 * Description: Plugin para gerenciamento de questionário de EPIs com alertas.
 * Version: 1.0
 * Author: Você
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'epi-functions.php';
require_once plugin_dir_path(__FILE__) . 'admin/epi-admin.php';
require_once plugin_dir_path(__FILE__) . 'seed/epi-seed.php';

// Ativação do plugin: cria tabelas e seed inicial
register_activation_hook(__FILE__, 'epi_seed_initial');

// Shortcode front-end
add_shortcode('epi_questionario', 'epi_shortcode_questionario');

