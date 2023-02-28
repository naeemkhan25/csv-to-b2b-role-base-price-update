<?php
/**
 * Plugin Name: Import B2B Price
 * Description: This plugin import b2b role based on price.
 * Plugin URI: 
 * Author: pilar
 * Version: 1.0
**/

//* Don't access this file directly
defined( 'ABSPATH' ) or die();

/**
 * Importing our css and js files
**/
function import_b2b_plugin() {
     if (file_exists(get_home_path().'/import/asituotehinnat001.csv')) {
        $path = get_home_path().'/import/asituotehinnat001.csv';
		$f_pointer = fopen( $path,"r" ); // file pointer
		$i = 0;
		$new_arra = [];
		while(! feof( $f_pointer ) ){
				$ar = fgetcsv( $f_pointer );
				if ( $i != 0 ) {
					if(is_array($ar)){
						$value = $ar[0];
						$d = explode(';',$value);
						if ( is_array($d) ) {
							if(array_key_exists($d[1],$new_arra)){
								$new_arra[$d[1]][$i-1]['user_role'] = 'yritys-'.$d[0];
								$new_arra[$d[1]][$i-1]['discount_type'] = 'fixed_price';
								$new_arra[$d[1]][$i-1]['discount_value'] = $d[2];
								$new_arra[$d[1]][$i-1]['min_qty'] = 1;
								$new_arra[$d[1]][$i-1]['max_qty'] = 10000;
							} else{
								$new_arra[$d[1]][$i-1]['user_role'] = 'yritys-'.$d[0];
								$new_arra[$d[1]][$i-1]['discount_type'] = 'fixed_price';
								$new_arra[$d[1]][$i-1]['discount_value'] = $d[2];
								$new_arra[$d[1]][$i-1]['min_qty'] = 1;
								$new_arra[$d[1]][$i-1]['max_qty'] = 10000;
							}
						}
					}
				}
			$i++;
		}
		fclose($f_pointer);
		foreach ($new_arra as $key=>$value){
			$product_id = b2b_rc_get_product_by_sku($key);
			if( $product_id ) {
				// $datss = array_values($value);
				update_post_meta( $product_id, '_role_base_price', $new_arra[$key] );
			}
		}
    }
}
add_action('admin_init', 'import_b2b_plugin');
function b2b_rc_get_product_by_sku( $sku ) {
    global $wpdb;
	$table = $wpdb->prefix. 'postmeta';
    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $table WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );
    if ($product_id) {
        return $product_id;
    }
    return null;
}