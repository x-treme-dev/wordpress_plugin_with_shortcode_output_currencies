<?php
/*
Plugin Name: View Currency Plugin
Description: Плагин для отображения курса валют
Version: 1.0
Author: Extreme
*/

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

register_activation_hook(__FILE__, 'vc_plugin_activate');

// активировать плагин
function vc_plugin_activate() {
    
}

// Деактивировать плагин
register_deactivation_hook(__FILE__, 'vc_plugin_deactivate');

function vc_plugin_deactivate() {
 
}

//----------------------------------------------------------------------------

 
 // Регистрация страницы настроек
add_action('admin_menu', 'currency_selector_menu');

function currency_selector_menu() {
    add_options_page('View Currency', 'My Plugin', 'manage_options', 'my-plugin', 'currency_selector_options');
}

// Вывод страницы настроек
function currency_selector_options() {
    // Проверка прав доступа
    if (!current_user_can('manage_options')) {
        return;
    }

    // Сохранение выбранной валюты
    if (isset($_POST['currency_selector_submit'])) {
        update_option('selected_currency', sanitize_text_field($_POST['selected_currency']));
    }

    // Получение текущего значения валюты
    $selected_currency = get_option('selected_currency', 'USD');
    
    ?>
    <div class="wrap">
        <h1>Настройки выбора валюты</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th>Выберите валюту:</th>
                    <td>
                        <select name="selected_currency">
                            <option value="USD" <?php selected($selected_currency, 'USD'); ?>>USD</option>
                            <option value="EUR" <?php selected($selected_currency, 'EUR'); ?>>EUR</option>
                            <option value="GBP" <?php selected($selected_currency, 'BRL'); ?>>GBP</option>
                            <option value="JPY" <?php selected($selected_currency, 'JPY'); ?>>JPY</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button('Получить текущий курс', 'primary', 'currency_selector_submit'); ?>
        </form>
    </div>

    <div>
        <!--Вывести функцию на странице настроек-->
       <p><?php echo $selected_currency; ?> стоит
        <?php  echo do_shortcode('[currency cur='.  $selected_currency .']'); ?> рублей
        </p>
    </div>
    <?php

    
}


 // создать shortcode для конвертации валют
 // получить курс продажи валют через API и вернуть текущий курс выбранной валюты к рублю 
function get_exchange_rate_shortcode($atts){
    // создать атрибут по умолчанию
    // если ничего не придет 
    $atts= shortcode_atts(
		array('cur' => 'USD'), 
		$atts, 
		'my_shorcode'
	);

    $url = 'https://www.cbr-xml-daily.ru/daily_json.js';
    $response = wp_remote_get($url);
    $body = json_decode(wp_remote_retrieve_body( $response ));
    
    foreach($body->Valute as $key=>$val){
        //print_r($val);
        if($atts['cur'] == $val->CharCode) return $val->Previous;
    }
     
}

add_shortcode('currency', 'get_exchange_rate_shortcode');
 