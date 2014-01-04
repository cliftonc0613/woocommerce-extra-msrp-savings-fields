<?php
/*
  Plugin Name: Woocoomerce Extra MSRP Savings Fields
  Description: Adds MSRP and Savings fields to products prices
  Based on: woocommerce-extra-price-fields by Aman Saini
  Author: Peter Song
  Author URI: psonghi@gmail.com
  Plugin URI: github.com/deepthunk/woocommerce-extra-msrp-savings-fields
  Version: 1.0
  Requires at least: WP: 3.0.0; WC: 2.0.0
  Tested on: WP: 3.8; WC: 2.0.20
 */

/*
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


function add_custom_price_box() {

    woocommerce_wp_text_input(array('id' => '_msrp', 'class' => 'wc_input_msrp short', 'label' => __('MSRP', 'woocommerce') . ' (' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'custom_attributes' => array(
            'step' => 'any',
            'min' => '0'
            )));
            
    woocommerce_wp_text_input(array('id' => '_savings', 'class' => 'wc_input_savings short', 'label' => __('Savings', 'woocommerce') . ' (%)', 'type' => 'number', 'custom_attributes' => array(
            'step' => 'any',
            'min' => '0'
            )));
}

add_action('woocommerce_product_options_pricing', 'add_custom_price_box');

function custom_woocommerce_process_product_meta($post_id, $post) {

    update_post_meta($post_id, '_msrp', stripslashes($_POST['_msrp']));
    update_post_meta($post_id, '_savings', stripslashes($_POST['_savings']));
}

add_action('woocommerce_process_product_meta', 'custom_woocommerce_process_product_meta', 2, 2);

function add_custom_price_front($p, $obj) {

    $post_id = $obj->post->ID;

    $link = get_permalink($post_id);
    $msrp = get_post_meta($post_id, '_msrp', true);
    $price = get_post_meta($post_id, '_regular_price', true);
    $sale = get_post_meta($post_id, '_sale_price', true);

    if (is_admin()) {
        $tag = 'div'; //show in new line
    } else {
        $tag = 'span';
    }

    if (!empty($msrp)) {
        $additional_price.= "<br><$tag style='font-size:80%;color:gray;' class='price_msrp'> MSRP: " . get_woocommerce_currency_symbol() . "$msrp</$tag>";
    }
    
    if (empty($savings)) {
        if (!empty($sale) && !empty($msrp)) {
            if ($sale < $msrp) {
                $savings= round( (($msrp - $sale) / $msrp * 100), 2);
                $additional_price.= "<br><$tag style='font-size:80%;color:red;' class='price_savings'> $savings% off</$tag>";
            }
        } elseif (!empty($price) && !empty($msrp)){
            if ($price < $msrp) {
                $savings= round( (($msrp - $price) / $msrp * 100), 2);
                $additional_price.= "<br><$tag style='font-size:80%;color:red;' class='price_savings'> $savings% off</$tag>";
            }
        }
    }

    return "<a href='$link'>" . $p . $additional_price . "</a>";
}

add_filter('woocommerce_get_price_html', 'add_custom_price_front', 10, 2);
add_filter('woocommerce_get_price_html', 'add_custom_price_front', 10, 2);


?>
