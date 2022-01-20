<?php
/*
Plugin Name: Tax Calculation
Plugin URI: 
Description: This plugin allows you to calculate tax
Version: 0.1
Author: Shan Sarfraz
Author Email: shansarfraz@outlook.com 
*/


//add_action('frm_after_create_entry', 'calculate_tax', 30, 2);
//add_filter('frm_validate_entry', 'calculate_tax', 20, 2);
//print_r($_REQUEST);
//add_action('init','calculate_tax');
//add_filter('frm_pre_create_entry', 'calculate_tax');
//add_action('frm_after_update_entry', 'calculate_tax', 10, 2);
//add_filter('frm_validate_entry', 'calculate_tax', 20, 2);

//add_action('init', 'calculate_tax');

add_filter('frm_email_message', 'calculate_tax', 10, 2);

function calculate_tax(){

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

	
    // echo 'form_id - '. $form_id;

    // echo '<br>';

    // echo 'active_key - ' .$f_key;

    // echo '<br>';

    // echo 'array_search -'. array_search($forms,$form_id);

    // echo '<br>';

    // echo 'form_type -'; print_r($f_key);

    // echo '<br>';

    // echo 'fields setup -' ;  print_r($tax_fields);

    // echo '<br>';

    // echo 'price table -' ;  print_r( $tax->price_table($tax_fields,$meta['type'],$meta['lang']));

    // die;

    $_table = $tax->price_table($tax_fields,$meta['type'],$meta['lang']);

    $new_message = str_replace('[price_table]',$_p_table['email'],$message);

    return $new_message;

    //echo $_table['footer'];

    // if($_table)
    // {

    //     echo footer_table($_table);

    //    // echo email_shortcode($_table);

    //}

     
     //print_r($_table);

    // print_r( $tax->price_table($tax_fields,$meta['type'],$meta['lang']));

    // add_action('wp_footer', function($_table) {

    //     echo  $_table['footer'];
    // },'100');

    // add_shortcode('price_table',function($_table){

    //     echo $_table['email'];

    // });




}

add_action('wp_footer','footer_table','100');

function footer_table() {

    $_p = calculate_tax();

    if( $_p){

   
        echo '<style type="text/css">#price-terms {    position: absolute; top:-10000%}</style>';
    

        // echo '<script type="text/javascript">


        //     if ( jQuery(".price-table").length ) {

        //           jQuery("#price-terms").show();
                
        //         }
        //     else{

        //         jQuery("#price-terms").hide();
        //     }    

        // </script>';

    echo $_p['footer'];

    }




    
}

//add_filter('frm_email_message', 'email_shortcode', 10, 2);

//add_shortcode( 'price_table', 'email_shortcode' );

//add_shortcode( 'price_table', 'email_shortcode' );

function email_shortcode($message, $attr) {

   // $_p_table = calculate_tax();

   // print_r($_p_table);

    if($_p_table){

            $new_message = str_replace('[price_table]',$_p_table['email'],$message);

    }



    return $new_message;
    
}