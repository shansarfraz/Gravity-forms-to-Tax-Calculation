<?php
/*
Plugin Name: Tax Calculation
Plugin URI: 
Description: This plugin allows you to calculate tax
Version: 0.1
Author: Shan Sarfraz
Author Email: shansarfraz@outlook.com 
*/

session_start();

global $_price_table;

add_action('init', 'get_price_table');

function get_price_table(){

    if(!isset($_REQUEST['form_id'])){ return;}

    $form_id = $_REQUEST['form_id']; 

    require( plugin_dir_path( __FILE__ ).'/inc/fields.php');

    require( plugin_dir_path( __FILE__ ).'/inc/Tax_Calculation.php');

    $tax = new Tax_Calculation;

    $forms = array('solvik_a' => '7','solvik_b' => '17','eng_a' => '41','eng_b' => '49');

    $form_a_en = $fields['form_type_a_en'];

    $form_b_en = $fields['form_type_b_en'];

    $form_a_sl = $fields['form_type_a_sl'];

    $form_b_sl = $fields['form_type_b_sl'];

    if($form_id):

        $f_key = array_search($form_id,$forms);

    endif;

    $tax_fields = '';

    if($f_key != FALSE ){

        if($f_key == 'solvik_a'):

            $meta = array('type'=>'A','lang'=>'sl');

            $tax_fields = $tax->get_fields($form_a_sl,'A','sl');

        elseif ($f_key == 'solvik_b'):

            $meta = array('type'=>'B','lang'=>'sl');

            $tax_fields =$tax->get_fields($form_b_sl,'B','sl');

        elseif($f_key == 'eng_a'):

            $meta = array('type'=>'A','lang'=>'eng');

            $tax_fields = $tax->get_fields($form_a_en,'A','eng');

        else:

            $meta = array('type'=>'B','lang'=>'eng');

            $tax_fields = $tax->get_fields($form_b_en,'B','eng');

        endif;

    
    }

   $_SESSION["price_table"] =  $tax->price_table($tax_fields,$meta['type'],$meta['lang']);

}



add_shortcode('price_table', 'price_table_email');

function price_table_email($message,$attr){

    return $_SESSION["price_table"]['email'];

}

add_action('wp_footer','footer_table','100');

function footer_table() {

    print_r($_SESSION);

    global $_price_table;

    echo '<style type="text/css">#price-terms {    position: absolute; top:-10000%}</style>';

    echo 
    '<script type="text/javascript">
        jQuery(\'.terms input[type=radio]\').change(function(){
        var value = jQuery( this ).val();

        if(value.toLowerCase() == \'no\' || value.toLowerCase() ==\'nie\'){
            window.location.href = \'https://danovepriznanieonline.sk/\';
        }
        });
    </script>';

    echo $_SESSION["price_table"]['footer'];


}