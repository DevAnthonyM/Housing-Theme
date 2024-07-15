<?php
/**
 * Since 1.3.0
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 11/08/16
 * Time: 7:41 PM
 */

if(!function_exists('houzez_number_shorten')) {
    function houzez_number_shorten($number, $precision = 0, $divisors = null) {
        $number = houzez_clean_price_20($number);

        if( houzez_option('short_prices_indian_format') == 1 ) {
            $short_price = houzez_indian_number_shorten( $number, $precision = 0, $divisors = null );
        } else {
            $short_price = houzez_number_shorten_global( $number, $precision = 0, $divisors = null );
        }

        return $short_price;
    }
}

if(!function_exists('houzez_number_shorten_global')) {
    function houzez_number_shorten_global($number, $precision = 0, $divisors = null) {
        $number = houzez_clean_price_20($number);

        $number = intval($number);

        if (!isset($divisors)) {
            $divisors = array(
                pow(1000, 0) => '', // 1000^0 == 1
                pow(1000, 1) => 'K', // Thousand
                pow(1000, 2) => 'M', // Million
                pow(1000, 3) => 'B', // Billion
                pow(1000, 4) => 'T', // Trillion
                pow(1000, 5) => 'Qa', // Quadrillion
                pow(1000, 6) => 'Qi', // Quintillion
            );    
        }
        
        foreach ($divisors as $divisor => $shorthand) {
            if (abs($number) < ($divisor * 1000)) {
                // Match found
                break;
            }
        }
        //Match found or not found use the last defined value for divisor
        $price = number_format($number / $divisor, 1);
        $price = str_replace(".0","",$price);
        return $price . $shorthand;
    }
}

if( ! function_exists( 'houzez_indian_number_shorten' ) ) {
    function houzez_indian_number_shorten($number, $precision = 0, $divisors = null) {
        $no = preg_replace('/[.,]/', '', $number);

        $no = intval($no);

        if($no == 0) {
            return ' ';
        }else {
            $n =  strlen($no); // 7
            switch ($n) {
                case 1:
                case 2:
                    $finalval = $number;
                    break;
                case 3:
                    $val = $no/100;
                    $val = round($val, 2);
                    $finalval =  $val ." hundred";
                    break;
                case 4:
                    $val = $no/1000;
                    $val = round($val, 2);
                    $finalval =  $val ." thousand";
                    break;
                case 5:
                    $val = $no/1000;
                    $val = round($val, 2);
                    $finalval =  $val ." thousand";
                    break;
                case 6:
                    $val = $no/100000;
                    $val = round($val, 2);
                    $finalval =  $val ." lakh";
                    break;
                case 7:
                    $val = $no/100000;
                    $val = round($val, 2);
                    $finalval =  $val ." lakh";
                    break;
                case 8:
                    $val = $no/10000000;
                    $val = round($val, 2);
                    $finalval =  $val ." crore";
                    break;
                case 9:
                    $val = $no/10000000;
                    $val = round($val, 2);
                    $finalval =  $val ." crore";
                    break;
                default:
                    $val = $no/10000000;
                    $val = round($val, 2);
                    $finalval =  $val ." crore";
                    break;
            }
            return $finalval;
        }
    }
}

/*-----------------------------------------------------------------------------------*/
// Listing Price
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_listing_price') ) {
    function houzez_listing_price() {

        $sale_price = get_post_meta( get_the_ID(), 'fave_property_price', true);
        $second_price     = get_post_meta( get_the_ID(), 'fave_property_sec_price', true );
        $price_postfix = get_post_meta( get_the_ID(), 'fave_property_price_postfix', true);
        $price_prefix  = get_post_meta( get_the_ID(), 'fave_property_price_prefix', true );
        $price_separator = houzez_option('currency_separator');

        $output = '';
        $price_as_text = doubleval( $sale_price );
        if( !$price_as_text ) {
            $output .= '<span class="item-price item-price-text">'.$sale_price. '</span>';
            return $output;
        }

        if( !empty( $price_prefix ) ) {
            $price_prefix = '<span class="price-start">'.$price_prefix.'</span>';
        }

        if (!empty($sale_price)) {

            if (!empty($price_postfix)) {
                if( empty( $second_price ) ) {
                    $price_postfix = $price_separator . $price_postfix;
                } else {
                    $price_postfix = '';
                }
            }

            return $price_prefix.' '.houzez_get_property_price($sale_price) . $price_postfix;

        }
    }
}

/*-----------------------------------------------------------------------------------*/
// Get invoice price
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_get_invoice_price') ) {
    function houzez_get_invoice_price ( $invoice_price ) {

        $invoice_price = doubleval( $invoice_price );

        //if( $invoice_price ) {

            if ( class_exists( 'FCC_Rates' ) && houzez_currency_switcher_enabled() && isset( $_COOKIE[ "houzez_set_current_currency" ] ) ) {

                $listing_price = apply_filters( 'houzez_currency_switcher_filter', $invoice_price );
                return $listing_price;
            }

            $multi_currency = houzez_option('multi_currency');

            if($multi_currency == 1) {
                $default_currency = houzez_option('default_multi_currency');
                if(empty($default_currency)) {
                    $default_currency = 'USD';
                }
                $currency = Houzez_Currencies::get_currency_by_code($default_currency);
                $invoice_currency = $currency['currency_symbol'];
                $price_decimals  = $currency['currency_decimal'];
                $invoice_currency_pos  = $currency['currency_position'];
                $thousands_separator  = $currency['currency_thousand_separator'];
                $decimal_point_separator  = $currency['currency_decimal_separator'];

            } else {

                $invoice_currency = houzez_get_currency();
                $price_decimals = 2;
                $invoice_currency_pos = houzez_option( 'currency_position', '$' );
                $thousands_separator = houzez_option( 'thousands_separator', ',' );
                $decimal_point_separator = houzez_option( 'decimal_point_separator', '.' );
            }

            //number_format() — Format a number with grouped thousands
            $final_price = number_format ( $invoice_price , $price_decimals , $decimal_point_separator , $thousands_separator );

            if(  $invoice_currency_pos == 'before' ) {
                return $invoice_currency . $final_price;
            } else {
                return $final_price . $invoice_currency;
            }

        /*} else {
            $invoice_currency = $invoice_price;
        }*/

        return $invoice_currency;
    }
}

if( !function_exists('houzez_clean_price_20')) {
    function houzez_clean_price_20($string) {
       $string = preg_replace('/&#36;/', '', $string);
       $string = str_replace(' ', '', $string); 
       $string = preg_replace('/[,]/', '', $string);

       return  $string;
    }
}

/*-----------------------------------------------------------------------------------*/
// Get price
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_get_property_price') ) {
    function houzez_get_property_price ( $listing_price ) {

    
        if( $listing_price ) {
            $listing_price = houzez_clean_price_20($listing_price);
            
            $currency_maker = currency_maker();

            $listings_currency = $currency_maker['currency'];
            $price_decimals = $currency_maker['decimals'];
            $listing_currency_pos = $currency_maker['currency_position'];
            $price_thousands_separator = $currency_maker['thousands_separator'];
            $price_decimal_point_separator = $currency_maker['decimal_point_separator'];
        
            $short_prices = houzez_option('short_prices');

            if($short_prices != 1 ) {

                $listing_price = doubleval( $listing_price );
                if ( class_exists( 'FCC_Rates' ) && houzez_currency_switcher_enabled() && isset( $_COOKIE[ "houzez_set_current_currency" ] ) ) {

                    $listing_price = apply_filters( 'houzez_currency_switcher_filter', $listing_price );
                    return $listing_price;
                }
                
                $indian_format = houzez_option('indian_format');
                if($indian_format == 1) {
                    $final_price = houzez_moneyFormatIndia ($listing_price);
                } else {
                    //number_format() — Format a number with grouped thousands
                    $final_price = number_format ( $listing_price , $price_decimals , $price_decimal_point_separator , $price_thousands_separator );
                }


            } else {
                $final_price = houzez_number_shorten($listing_price, $price_decimals);
            }
            if(  $listing_currency_pos == 'before' ) {
                return $listings_currency . $final_price;
            } else {
                return $final_price . $listings_currency;
            }

        } else {
            $listings_currency = '';
        }

        return $listings_currency;
    }
}

if(!function_exists('houzez_moneyFormatIndia')) {
    function houzez_moneyFormatIndia($num) {
        $explrestunits = "" ;
        if(strlen($num)>3) {
            $lastthree = substr($num, strlen($num)-3, strlen($num));
            $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
            $expunit = str_split($restunits, 2);
            for($i=0; $i<sizeof($expunit); $i++) {
                // creates each of the 2's group and adds a comma to the end
                if($i==0) {
                    $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
                } else {
                    $explrestunits .= $expunit[$i].",";
                }
            }
            $thecash = $explrestunits.$lastthree;
        } else {
            $thecash = $num;
        }
        return $thecash; // writes the final format where $currency is the currency symbol.
    }
}

if( !function_exists('houzez_get_property_price_map_pins') ) {
    function houzez_get_property_price_map_pins ( $listing_price ) {

        if( $listing_price ) {

            $listing_price = houzez_clean_price_20($listing_price);

            $currency_maker = currency_maker();

            $listings_currency = $currency_maker['currency'];
            $price_decimals = $currency_maker['decimals'];
            $listing_currency_pos = $currency_maker['currency_position'];
            $price_thousands_separator = $currency_maker['thousands_separator'];
            $price_decimal_point_separator = $currency_maker['decimal_point_separator'];
        
            $short_prices = houzez_option('short_prices_pins');

            if($short_prices != 1 ) {

                $listing_price = doubleval( $listing_price );
                if ( class_exists( 'FCC_Rates' ) && houzez_currency_switcher_enabled() && isset( $_COOKIE[ "houzez_set_current_currency" ] ) ) {

                    $listing_price = apply_filters( 'houzez_currency_switcher_filter', $listing_price );
                    return $listing_price;
                }

                $indian_format = houzez_option('indian_format');
                if($indian_format == 1) {
                    $final_price = houzez_moneyFormatIndia ($listing_price);
                } else {
                    //number_format() — Format a number with grouped thousands
                    $final_price = number_format ( $listing_price , $price_decimals , $price_decimal_point_separator , $price_thousands_separator );
                }

            } else {
                $final_price = houzez_number_shorten($listing_price, $price_decimals);
            }
            if(  $listing_currency_pos == 'before' ) {
                return $listings_currency . $final_price;
            } else {
                return $final_price . $listings_currency;
            }

        } else {
            $listings_currency = '';
        }

        return $listings_currency;
    }
}

/*-----------------------------------------------------------------------------------*/
// Get price
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_get_property_price_for_print') ) {
    function houzez_get_property_price_for_print ( $listing_price, $prop_id ) {

        if( $listing_price ) {

            $listing_price = houzez_clean_price_20($listing_price);

            $currency_maker = currency_maker_for_print($prop_id);

            $listings_currency = $currency_maker['currency']; 
            $price_decimals = $currency_maker['decimals'];
            $listing_currency_pos = $currency_maker['currency_position'];
            $price_thousands_separator = $currency_maker['thousands_separator'];
            $price_decimal_point_separator = $currency_maker['decimal_point_separator'];
        
            $short_prices = houzez_option('short_prices');

            if($short_prices != 1 ) {

                $listing_price = doubleval( $listing_price );
                if ( class_exists( 'FCC_Rates' ) && houzez_currency_switcher_enabled() && isset( $_COOKIE[ "houzez_set_current_currency" ] ) ) {

                    $listing_price = apply_filters( 'houzez_currency_switcher_filter', $listing_price );
                    return $listing_price;
                }
                
                $indian_format = houzez_option('indian_format');
                if($indian_format == 1) {
                    $final_price = houzez_moneyFormatIndia ($listing_price);
                } else {
                    //number_format() — Format a number with grouped thousands
                    $final_price = number_format ( $listing_price , $price_decimals , $price_decimal_point_separator , $price_thousands_separator );
                }

            } else {
                $final_price = houzez_number_shorten($listing_price, $price_decimals);
            }
            if(  $listing_currency_pos == 'before' ) {
                return $listings_currency . $final_price;
            } else {
                return $final_price . $listings_currency;
            }

        } else {
            $listings_currency = '';
        }

        return $listings_currency;
    }
}

/*-----------------------------------------------------------------------------------*/
// Get price
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_get_property_price_for_saved_searches') ) {
    function houzez_get_property_price_for_saved_searches ( $listing_price ) {

        $fave_options = get_option('houzez_options');
    
        if( $listing_price ) {
            $listing_price = houzez_clean_price_20($listing_price);
            
            $currency_maker = currency_maker_for_saved_searches();

            $listings_currency = $currency_maker['currency'];
            $price_decimals = $currency_maker['decimals'];
            $listing_currency_pos = $currency_maker['currency_position'];
            $price_thousands_separator = $currency_maker['thousands_separator'];
            $price_decimal_point_separator = $currency_maker['decimal_point_separator'];
        
            $short_prices = fave_option('short_prices');

            if($short_prices != 1 ) {

                $listing_price = doubleval( $listing_price );
                if ( class_exists( 'FCC_Rates' ) && houzez_currency_switcher_enabled() && isset( $_COOKIE[ "houzez_set_current_currency" ] ) ) {

                    $listing_price = apply_filters( 'houzez_currency_switcher_filter', $listing_price );
                    return $listing_price;
                }
                
                $indian_format = fave_option('indian_format');
                if($indian_format == 1) {
                    $final_price = houzez_moneyFormatIndia ($listing_price);
                } else {
                    //number_format() — Format a number with grouped thousands
                    $final_price = number_format ( $listing_price , $price_decimals , $price_decimal_point_separator , $price_thousands_separator );
                }


            } else {
                $final_price = houzez_number_shorten($listing_price, $price_decimals);
            }
            if(  $listing_currency_pos == 'before' ) {
                return $listings_currency . $final_price;
            } else {
                return $final_price . $listings_currency;
            }

        } else {
            $listings_currency = '';
        }

        return $listings_currency;
    }
}

if( !function_exists('currency_maker')) {
    function currency_maker() {

        $price_maker_array = array();
        $multi_currency = houzez_option('multi_currency');
        $default_currency = houzez_option('default_multi_currency');
        if(empty($default_currency)) {
            $default_currency = 'USD';
        }

        if( $multi_currency == 1 ) {

            if(class_exists('Houzez_Currencies')) {
                $currencies = Houzez_Currencies::get_property_currency(get_the_ID());

                if($currencies) {

                    foreach ($currencies as $currency) {
                        $price_maker_array['currency'] = $currency->currency_symbol;
                        $price_maker_array['decimals']  = $currency->currency_decimal;
                        $price_maker_array['currency_position']  = $currency->currency_position;
                        $price_maker_array['thousands_separator']  = $currency->currency_thousand_separator;
                        $price_maker_array['decimal_point_separator']  = $currency->currency_decimal_separator;
                    }

                } else {

                        $currency = Houzez_Currencies::get_currency_by_code($default_currency);

                        $price_maker_array['currency'] = $currency['currency_symbol'];
                        $price_maker_array['decimals']  = $currency['currency_decimal'];
                        $price_maker_array['currency_position']  = $currency['currency_position'];
                        $price_maker_array['thousands_separator']  = $currency['currency_thousand_separator'];
                        $price_maker_array['decimal_point_separator']  = $currency['currency_decimal_separator'];
                }
            }

        } else {
            $price_maker_array['currency'] = houzez_get_currency();
            $price_maker_array['decimals']  = intval(houzez_option( 'decimals' ));
            $price_maker_array['currency_position']  = houzez_option( 'currency_position' );
            $price_maker_array['thousands_separator']  = houzez_option( 'thousands_separator' );
            $price_maker_array['decimal_point_separator']  = houzez_option( 'decimal_point_separator' );

        }
        return $price_maker_array;
    }
}

if( !function_exists('currency_maker_for_saved_searches')) {
    function currency_maker_for_saved_searches() {

        $price_maker_array = array();
        $multi_currency = fave_option('multi_currency');
        $default_currency = fave_option('default_multi_currency');
        if(empty($default_currency)) {
            $default_currency = 'USD';
        }

        if( $multi_currency == 1 ) {

            if(class_exists('Houzez_Currencies')) {
                $currencies = Houzez_Currencies::get_property_currency(get_the_ID());

                if($currencies) {

                    foreach ($currencies as $currency) {
                        $price_maker_array['currency'] = $currency->currency_symbol;
                        $price_maker_array['decimals']  = $currency->currency_decimal;
                        $price_maker_array['currency_position']  = $currency->currency_position;
                        $price_maker_array['thousands_separator']  = $currency->currency_thousand_separator;
                        $price_maker_array['decimal_point_separator']  = $currency->currency_decimal_separator;
                    }

                } else {

                        $currency = Houzez_Currencies::get_currency_by_code($default_currency);

                        $price_maker_array['currency'] = $currency['currency_symbol'];
                        $price_maker_array['decimals']  = $currency['currency_decimal'];
                        $price_maker_array['currency_position']  = $currency['currency_position'];
                        $price_maker_array['thousands_separator']  = $currency['currency_thousand_separator'];
                        $price_maker_array['decimal_point_separator']  = $currency['currency_decimal_separator'];
                }
            }

        } else {
            $price_maker_array['currency'] = houzez_get_currency();
            $price_maker_array['decimals']  = intval(fave_option( 'decimals' ));
            $price_maker_array['currency_position']  = fave_option( 'currency_position' );
            $price_maker_array['thousands_separator']  = fave_option( 'thousands_separator' );
            $price_maker_array['decimal_point_separator']  = fave_option( 'decimal_point_separator' );

        }
        return $price_maker_array;
    }
}

if( !function_exists('currency_maker_for_print')) {
    function currency_maker_for_print($prop_id) {

        $price_maker_array = array();
        $multi_currency = houzez_option('multi_currency');
        $default_currency = houzez_option('default_multi_currency');
        if(empty($default_currency)) {
            $default_currency = 'USD';
        }

        if( $multi_currency == 1 ) {

            if(class_exists('Houzez_Currencies')) {
                $currencies = Houzez_Currencies::get_property_currency_by_id($prop_id);

                if($currencies) {

                    foreach ($currencies as $currency) {
                        $price_maker_array['currency'] = $currency->currency_symbol;
                        $price_maker_array['decimals'] = $currency->currency_decimal;
                        $price_maker_array['currency_position']  = $currency->currency_position;
                        $price_maker_array['thousands_separator']  = $currency->currency_thousand_separator;
                        $price_maker_array['decimal_point_separator']  = $currency->currency_decimal_separator;
                    }

                } else {

                        $currency = Houzez_Currencies::get_currency_by_code($default_currency);

                        $price_maker_array['currency'] = $currency['currency_symbol'];
                        $price_maker_array['decimals']  = $currency['currency_decimal'];
                        $price_maker_array['currency_position']  = $currency['currency_position'];
                        $price_maker_array['thousands_separator']  = $currency['currency_thousand_separator'];
                        $price_maker_array['decimal_point_separator']  = $currency['currency_decimal_separator'];
                }
            }

        } else {
            $price_maker_array['currency'] = houzez_get_currency();
            $price_maker_array['decimals']  = intval(houzez_option( 'decimals' ));
            $price_maker_array['currency_position']  = houzez_option( 'currency_position' );
            $price_maker_array['thousands_separator']  = houzez_option( 'thousands_separator' );
            $price_maker_array['decimal_point_separator']  = houzez_option( 'decimal_point_separator' );

        }
        return $price_maker_array;
    }
}



if(!function_exists('houzez_available_currencies')) {
    function houzez_available_currencies() {
        $currencies_array = array( '' => esc_html__('Choose Currency', 'houzez'));
        if(class_exists('Houzez_Currencies')) {
            $currencies = Houzez_Currencies::get_currency_codes();
            if($currencies) {
                foreach ($currencies as $currency) {
                    $currencies_array[$currency->currency_code] = $currency->currency_code;
                }
            }
        }

        return $currencies_array;
    }
}


if( !function_exists('houzez_currency_switcher_filter') ) {
    function houzez_currency_switcher_filter($listing_price) {
        $current_currency = $_COOKIE[ "houzez_set_current_currency" ];
        if ( Fcc_currency_exists( $current_currency ) ) {    // validate current currency
            $base_currency = houzez_default_currency_for_switcher();
            $converted_price = Fcc_convert_currency( $listing_price, $base_currency, $current_currency );
            return Fcc_format_currency( $converted_price, $current_currency );
        }
    }
}
add_filter( 'houzez_currency_switcher_filter', 'houzez_currency_switcher_filter', 1, 9 );

/*-----------------------------------------------------------------------------------*/
// get user define currency from theme options, if empty return default
/*-----------------------------------------------------------------------------------*/
if(!function_exists('houzez_get_currency')){
    function houzez_get_currency(){
        //get default currency from theme options
        $houzez_default_currency = fave_option( 'currency_symbol' );

        if(empty($houzez_default_currency)){
            return esc_html__( '$' , 'houzez' );
        }
        return $houzez_default_currency;
    }
}

/*-----------------------------------------------------------------------------------*/
// Listing price by property ID
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_listing_price_by_id') ) {
    function houzez_listing_price_by_id( $propID )
    {

        $sale_price = get_post_meta( $propID, 'fave_property_price', true);
        $second_price     = get_post_meta( $propID, 'fave_property_sec_price', true );
        $price_postfix = get_post_meta( $propID, 'fave_property_price_postfix', true);
        $price_separator = houzez_option('currency_separator');

        $output = '';
        $price_as_text = doubleval( $sale_price );
        if( !$price_as_text ) {
            $output .= '<span class="item-price item-price-text">'.$sale_price. '</span>';
            return $output;
        }

        if (!empty($sale_price)) {

            if (!empty($price_postfix)) {
                if( empty( $second_price ) ) {
                    $price_postfix = $price_separator . $price_postfix;
                } else {
                    $price_postfix = '';
                }
            }

            return houzez_get_property_price($sale_price) . $price_postfix;

        }
    }
}

/*-----------------------------------------------------------------------------------*/
// Listing price for saved searches
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_listing_price_for_saved_searches') ) {
    function houzez_listing_price_for_saved_searches($listing_id = '') {

        if(empty($listing_id)) {
            $listing_id = get_the_ID();
        } 
        
        $output = '';
        $sale_price     = get_post_meta( $listing_id, 'fave_property_price', true );
        $second_price   = get_post_meta( $listing_id, 'fave_property_sec_price', true );
        $price_postfix  = get_post_meta( $listing_id, 'fave_property_price_postfix', true );
        $price_prefix   = get_post_meta( $listing_id, 'fave_property_price_prefix', true );
        $price_separator = houzez_option('currency_separator');

        $price_as_text = doubleval( $sale_price );
        if( !$price_as_text ) {
            if( is_singular( 'property' ) ) {
                $output .= '<li class="item-price item-price-text price-single-listing-text">'.$sale_price. '</li>';
                return $output;
            }
            $output .= '<li class="item-price item-price-text">'.$sale_price. '</li>';
            return $output;
        }

        if( !empty( $price_prefix ) ) {
            $price_prefix = '<span class="price-prefix">'.$price_prefix.' </span>';
        }

        if (!empty( $sale_price ) ) {

            if (!empty( $price_postfix )) {
                $price_postfix = $price_separator . $price_postfix;
            }

            if (!empty( $sale_price ) && !empty( $second_price ) ) {

                if( is_singular( 'property' ) ) {
                    $output .= '<li class="item-price">'.$price_prefix. houzez_get_property_price_for_saved_searches($sale_price) . '</li>';
                    if (!empty($second_price)) {
                        $output .= '<li class="item-sub-price">';
                        $output .= houzez_get_property_price_for_saved_searches($second_price) . $price_postfix;
                        $output .= '</li>';
                    }
                } else {
                    $output .= '<li class="item-price">'.$price_prefix.' '.houzez_get_property_price_for_saved_searches($sale_price) . '</li>';
                    if (!empty($second_price)) {
                        $output .= '<li class="item-sub-price">';
                        $output .= houzez_get_property_price_for_saved_searches($second_price) . $price_postfix;
                        $output .= '</li>';
                    }
                }
            } else {
                if (!empty( $sale_price )) {
                    if( is_singular( 'property' ) ) {
                        $output .= '<li class="item-price">';
                        $output .= $price_prefix. houzez_get_property_price_for_saved_searches($sale_price) . $price_postfix;
                        $output .= '</li>';
                    } else {
                        $output .= '<li class="item-price">';
                        $output .= $price_prefix;
                        $output .= houzez_get_property_price_for_saved_searches($sale_price) . $price_postfix;
                        $output .= '</li>';
                    }
                }
            }

        }
        return $output;
    }
}

/*-----------------------------------------------------------------------------------*/
// Listing price version 1
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_listing_price_v1') ) {
    function houzez_listing_price_v1($listing_id = '') {

        if(empty($listing_id)) {
            $listing_id = get_the_ID();
        } 
        
        $output = '';
        $sale_price     = get_post_meta( $listing_id, 'fave_property_price', true );
        $second_price   = get_post_meta( $listing_id, 'fave_property_sec_price', true );
        $price_postfix  = get_post_meta( $listing_id, 'fave_property_price_postfix', true );
        $price_prefix   = get_post_meta( $listing_id, 'fave_property_price_prefix', true );
        $price_separator = houzez_option('currency_separator');

        $price_as_text = doubleval( $sale_price );
        if( !$price_as_text ) {
            if( is_singular( 'property' ) ) {
                $output .= '<li class="item-price item-price-text price-single-listing-text">'.$sale_price. '</li>';
                return $output;
            }
            $output .= '<li class="item-price item-price-text">'.$sale_price. '</li>';
            return $output;
        }

        if( !empty( $price_prefix ) ) {
            $price_prefix = '<span class="price-prefix">'.$price_prefix.' </span>';
        }

        if (!empty( $sale_price ) ) {

            if (!empty( $price_postfix )) {
                $price_postfix = $price_separator . $price_postfix;
            }

            if (!empty( $sale_price ) && !empty( $second_price ) ) {

                if( is_singular( 'property' ) ) {
                    $output .= '<li class="item-price">'.$price_prefix. houzez_get_property_price($sale_price) . '</li>';
                    if (!empty($second_price)) {
                        $output .= '<li class="item-sub-price">';
                        $output .= houzez_get_property_price($second_price) . $price_postfix;
                        $output .= '</li>';
                    }
                } else {
                    $output .= '<li class="item-price">'.$price_prefix.' '.houzez_get_property_price($sale_price) . '</li>';
                    if (!empty($second_price)) {
                        $output .= '<li class="item-sub-price">';
                        $output .= houzez_get_property_price($second_price) . $price_postfix;
                        $output .= '</li>';
                    }
                }
            } else {
                if (!empty( $sale_price )) {
                    if( is_singular( 'property' ) ) {
                        $output .= '<li class="item-price">';
                        $output .= $price_prefix. houzez_get_property_price($sale_price) . $price_postfix;
                        $output .= '</li>';
                    } else {
                        $output .= '<li class="item-price">';
                        $output .= $price_prefix;
                        $output .= houzez_get_property_price($sale_price) . $price_postfix;
                        $output .= '</li>';
                    }
                }
            }

        }
        return $output;
    }
}


/*-----------------------------------------------------------------------------------*/
// Listing price v5
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_listing_price_v5') ) {
    function houzez_listing_price_v5() {
        $output = '';
        $sale_price     = get_post_meta( get_the_ID(), 'fave_property_price', true );
        $second_price     = get_post_meta( get_the_ID(), 'fave_property_sec_price', true );
        $price_postfix  = get_post_meta( get_the_ID(), 'fave_property_price_postfix', true );
        $price_prefix  = get_post_meta( get_the_ID(), 'fave_property_price_prefix', true );
        $price_separator = houzez_option('currency_separator');

        $price_as_text = doubleval( $sale_price );
        if( !$price_as_text ) {
            $output .= '<span class="item-price-text">'.$sale_price. '</span>';
            return $output;
        }

        if( !empty( $price_prefix ) ) {
            $price_prefix = '<span class="price-prefix">'.$price_prefix. '</span>';
        }

        if (!empty( $price_postfix )) {
            $price_postfix = $price_separator . $price_postfix;
        }
        
        if(empty($second_price)) {
            $output .= $price_prefix.' '. houzez_get_property_price($sale_price).''.$price_postfix;
        } else {
            $output .= $price_prefix.' '. houzez_get_property_price($sale_price);
        }
        
        return $output;
    }
}


if( !function_exists('houzez_listing_price_map_pins') ) {
    function houzez_listing_price_map_pins()
    { 
        $output = '';
        $sale_price     = get_post_meta( get_the_ID(), 'fave_property_price', true );
        $second_price   = get_post_meta( get_the_ID(), 'fave_property_sec_price', true );
        $price_postfix  = get_post_meta( get_the_ID(), 'fave_property_price_postfix', true );
        $price_prefix   = get_post_meta( get_the_ID(), 'fave_property_price_prefix', true );
        $price_separator = houzez_option('currency_separator');

        $price_as_text = doubleval( $sale_price );
        if( !$price_as_text ) {
            $output .= $sale_price;
            return $output;
        }


        if (!empty( $sale_price ) ) {

            if (!empty( $price_postfix )) {
                $price_postfix = $price_separator . $price_postfix;
            }

            if (!empty( $sale_price ) && !empty( $second_price ) ) {

                $output .= houzez_get_property_price_map_pins($sale_price);
                
            } else {
                if (!empty( $sale_price )) {
                    
                        
                    $output .= houzez_get_property_price_map_pins($sale_price) . $price_postfix;
                        
                    
                }
            }

        }
        return $output;
    }
}



/*-----------------------------------------------------------------------------------*/
// Price for print property
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_listing_price_for_print') ) {
    function houzez_listing_price_for_print( $propID )
    {

        $sale_price     = get_post_meta( $propID, 'fave_property_price', true );
        $second_price     = get_post_meta( $propID, 'fave_property_sec_price', true );
        $price_postfix  = get_post_meta( $propID, 'fave_property_price_postfix', true );
        $price_prefix  = get_post_meta( $propID, 'fave_property_price_prefix', true );
        $price_separator = houzez_option('currency_separator');

        $output = '';
        $price_as_text = doubleval( $sale_price );
        if( !$price_as_text ) {
            $output .= '<span class="item-price item-price-text">'.$sale_price. '</span>';
            return $output;
        }

        if( !empty( $price_prefix ) ) {
            $price_prefix = '<span class="price-start">'.$price_prefix.'</span>';
        }

        $output = '';

        if (!empty( $sale_price ) ) {

            if (!empty( $price_postfix )) {
                $price_postfix = $price_separator . $price_postfix;
            }

            if (!empty( $sale_price ) && !empty( $second_price ) ) {

                $output .= $price_prefix. '<li class="item-price">'. houzez_get_property_price_for_print($sale_price, $propID) . '</li>';
                if (!empty($second_price)) {
                    $output .= '<li class="item-sub-price">';
                    $output .= houzez_get_property_price_for_print($second_price, $propID) . $price_postfix;
                    $output .= '</li>';
                }
            } else {
                if (!empty( $sale_price )) {
                    $output .= '<li class="item-price">';
                    $output .= $price_prefix.' '.houzez_get_property_price_for_print($sale_price, $propID) . $price_postfix;
                    $output .= '</li>';
                }
            }

        }
        return $output;
    }
}

/*-----------------------------------------------------------------------------------*/
// Price for admin property custom post type
/*-----------------------------------------------------------------------------------*/
if( !function_exists( 'houzez_property_price_admin' ) ) {
    function houzez_property_price_admin () {
        global $post;
        $sale_price     = get_post_meta( get_the_ID(), 'fave_property_price', true );
        $second_price     = get_post_meta( get_the_ID(), 'fave_property_sec_price', true );
        $price_postfix  = get_post_meta( get_the_ID(), 'fave_property_price_postfix', true );
        $price_separator = houzez_option('currency_separator');

        $output = '';
        $price_as_text = doubleval( $sale_price );
        if( !$price_as_text ) {
            $output .= '<b>'.$sale_price. '</b>';
            echo $output;
            return;
        }

        if (!empty( $sale_price ) ) {

            if (!empty( $price_postfix )) {
                $price_postfix = $price_separator . $price_postfix;
            }

            if (!empty( $sale_price ) && !empty( $second_price ) ) {
                echo '<b>' . houzez_get_property_price($sale_price) . '</b><br/>';

                if (!empty( $second_price )) {
                    echo houzez_get_property_price($second_price) . $price_postfix;
                }
            } else {
                if (!empty( $sale_price )) {
                    echo '<b>';
                    echo houzez_get_property_price($sale_price) . $price_postfix;
                    echo '</b>';
                }
            }
        } else {
            echo '-';
        }
    }
}

/*-----------------------------------------------------------------------------------*/
// Price for CRM
/*-----------------------------------------------------------------------------------*/
if( !function_exists( 'houzez_property_price_crm' ) ) {
    function houzez_property_price_crm () {
        global $post;
        $sale_price     = get_post_meta( get_the_ID(), 'fave_property_price', true );
        $second_price     = get_post_meta( get_the_ID(), 'fave_property_sec_price', true );
        $price_postfix  = get_post_meta( get_the_ID(), 'fave_property_price_postfix', true );
        $price_separator = houzez_option('currency_separator');

        $output = '';
        $price_as_text = doubleval( $sale_price );
        if( !$price_as_text ) {
            $output .= $sale_price;
            echo $output;
            return;
        }

        if (!empty( $sale_price ) ) {

            if (!empty( $price_postfix )) {
                $price_postfix = $price_separator . $price_postfix;
            }

            if (!empty( $sale_price ) && !empty( $second_price ) ) {
                echo houzez_get_property_price($sale_price) . '<br/>';


            } else {
                if (!empty( $sale_price )) {
                   
                    echo houzez_get_property_price($sale_price) . $price_postfix;
                   
                }
            }

        }
    }
}


/*-----------------------------------------------------------------------------------*/
// Minimum Price List
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_adv_searches_min_price') ) {
    function houzez_adv_searches_min_price() {
        $prices_array = array( 500, 1000, 5000, 10000, 15000, 20000, 25000, 50000, 100000, 200000, 300000, 400000, 500000, 600000, 700000 );
        $searched_price = '';

        $minimum_price_theme_options = houzez_option('min_price');
        $srh_any = houzez_option('srh_any');

        if( !empty($minimum_price_theme_options) ) {
            $minimum_prices_array = explode( ',', $minimum_price_theme_options );

            if( is_array( $minimum_prices_array ) && !empty( $minimum_prices_array ) ) {
                $temp_min_price_array = array();
                foreach( $minimum_prices_array as $min_price ) {
                    $min_price_integer = floatval( $min_price );
                    if( $min_price_integer > 0 ) {
                        $temp_min_price_array[] = $min_price_integer;
                    }
                }

                if( !empty( $temp_min_price_array ) ) {
                    $prices_array = $temp_min_price_array;
                }
            }
        }

        if( isset( $_GET['min-price'] ) ) {
            $searched_price = $_GET['min-price'];
        }

        if( $searched_price == 'any' )  {
            echo '<option value="any" selected="selected">'.esc_attr($srh_any).'</option>';
        } else {
            echo '<option value="any">'.esc_attr($srh_any).'</option>';
        }

        if( !empty( $prices_array ) ) {
            foreach( $prices_array as $min_price ) {
                if( $searched_price == $min_price ) {
                    echo '<option value="'.esc_attr( $min_price ).'" selected="selected">'.houzez_get_property_price( $min_price ).'</option>';
                } else {
                    echo '<option value="'.esc_attr( $min_price ).'">'.houzez_get_property_price( $min_price ).'</option>';
                }
            }
        }

    }
}

/*-----------------------------------------------------------------------------------*/
// Minimum Price List For advanced searches rent only
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_adv_searches_min_price_rent_only') ) {
    function houzez_adv_searches_min_price_rent_only() {
        $price_array = array( 500, 1000, 2000, 3000, 4000, 5000, 7500, 10000, 15000 );
        $searched_price = '';

        $minimum_price_theme_options = houzez_option('min_price_rent');
        $srh_any = houzez_option('srh_any');

        if( !empty($minimum_price_theme_options) ) {
            $minimum_prices_array = explode( ',', $minimum_price_theme_options );

            if( is_array( $minimum_prices_array ) && !empty( $minimum_prices_array ) ) {
                $temp_min_price_array = array();
                foreach( $minimum_prices_array as $min_price ) {
                    $min_price_integer = floatval( $min_price );
                    if( $min_price_integer > 0 ) {
                        $temp_min_price_array[] = $min_price_integer;
                    }
                }

                if( !empty( $temp_min_price_array ) ) {
                    $price_array = $temp_min_price_array;
                }
            }
        }

        if( isset( $_GET['min-price'] ) ) {
            $searched_price = $_GET['min-price'];
        }

        if( $searched_price == 'any' )  {
            echo '<option value="any" selected="selected">'.esc_attr($srh_any).'</option>';
        } else {
            echo '<option value="any">'.esc_attr($srh_any).'</option>';
        }

        if( !empty( $price_array ) ) {
            foreach( $price_array as $min_price ) {
                if( $searched_price == $min_price ) {
                    echo '<option value="'.esc_attr( $min_price ).'" selected="selected">'.houzez_get_property_price( $min_price ).'</option>';
                } else {
                    echo '<option value="'.esc_attr( $min_price ).'">'.houzez_get_property_price( $min_price ).'</option>';
                }
            }
        }

    }
}

/*-----------------------------------------------------------------------------------*/
// Maximum Price List
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_adv_searches_max_price') ) {
    function houzez_adv_searches_max_price() {
        $price_array = array( 5000, 10000, 50000, 100000, 200000, 300000, 400000, 500000, 600000, 700000, 800000, 900000 );
        $searched_price = '';

        $maximum_price_theme_options = houzez_option('max_price');
        $srh_any = houzez_option('srh_any');

        if( !empty($maximum_price_theme_options) ) {
            $maximum_price_array = explode( ',', $maximum_price_theme_options );

            if( is_array( $maximum_price_array ) && !empty( $maximum_price_array ) ) {
                $temp_max_price_array = array();
                foreach( $maximum_price_array as $max_price ) {
                    $max_price_integer = floatval( $max_price );
                    if( $max_price_integer > 0 ) {
                        $temp_max_price_array[] = $max_price_integer;
                    }
                }

                if( !empty( $temp_max_price_array ) ) {
                    $price_array = $temp_max_price_array;
                }
            }
        }

        if( isset( $_GET['max-price'] ) ) {
            $searched_price = $_GET['max-price'];
        }

        if( $searched_price == 'any' )  {
            echo '<option value="any" selected="selected">'.esc_attr($srh_any).'</option>';
        } else {
            echo '<option value="any">'.esc_attr($srh_any).'</option>';
        }

        if( !empty( $price_array ) ) {
            foreach( $price_array as $max_price ) {
                if( $searched_price == $max_price ) {
                    echo '<option value="'.esc_attr( $max_price ).'" selected="selected">'.houzez_get_property_price( $max_price ).'</option>';
                } else {
                    echo '<option value="'.esc_attr( $max_price ).'">'.houzez_get_property_price( $max_price ).'</option>';
                }
            }
        }

    }
}

/*-----------------------------------------------------------------------------------*/
// Advanced searches max price for rent only
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_adv_searches_max_price_rent_only') ) {
    function houzez_adv_searches_max_price_rent_only() {
        $price_array = array( 1000, 2000, 3000, 4000, 5000, 7500, 10000, 15000, 20000, 25000, 30000 );
        $searched_price = '';

        $maximum_price_theme_options = houzez_option('max_price_rent');
        $srh_any = houzez_option('srh_any');

        if( !empty($maximum_price_theme_options) ) {
            $maximum_price_array = explode( ',', $maximum_price_theme_options );

            if( is_array( $maximum_price_array ) && !empty( $maximum_price_array ) ) {
                $temp_max_price_array = array();
                foreach( $maximum_price_array as $max_price ) {
                    $max_price_integer = floatval( $max_price );
                    if( $max_price_integer > 0 ) {
                        $temp_max_price_array[] = $max_price_integer;
                    }
                }

                if( !empty( $temp_max_price_array ) ) {
                    $price_array = $temp_max_price_array;
                }
            }
        }

        if( isset( $_GET['max-price'] ) ) {
            $searched_price = $_GET['max-price'];
        }

        if( $searched_price == 'any' )  {
            echo '<option value="any" selected="selected">'.esc_attr($srh_any).'</option>';
        } else {
            echo '<option value="any">'.esc_attr($srh_any).'</option>';
        }

        if( !empty( $price_array ) ) {
            foreach( $price_array as $max_price ) {
                if( $searched_price == $max_price ) {
                    echo '<option value="'.esc_attr( $max_price ).'" selected="selected">'.houzez_get_property_price( $max_price ).'</option>';
                } else {
                    echo '<option value="'.esc_attr( $max_price ).'">'.houzez_get_property_price( $max_price ).'</option>';
                }
            }
        }

    }
}

/*-----------------------------------------------------------------------------------*/
// get default based currecncy for currency conversion
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'houzez_default_currency_for_switcher' ) ) {

    function houzez_default_currency_for_switcher() {

        $default_currency = houzez_option('houzez_base_currency');
        if ( !empty( $default_currency ) ) {
            return $default_currency;
        } else {
            $default_currency = 'USD';
        }

        return $default_currency;
    }
}

/*-----------------------------------------------------------------------------------*/
// Get Supported currencies list from theme option for currency switcher
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'houzez_get_list_of_supported_currencies' ) ) {

    function houzez_get_list_of_supported_currencies() {

        $currencies_array = array();
        $get_currencies_list = houzez_option('houzez_supported_currencies');
        if ( ! empty( $get_currencies_list ) ) {
            $currencies_array = explode( ',', $get_currencies_list );
        } else {
            $currencies_array = array(
                'AUD','CAD','CHF','EUR','GBP','HKD','JPY','NOK','SEK','USD','NGN'
            );
        }

        return $currencies_array;
    }
}

/*-----------------------------------------------------------------------------------*/
// get current currency for currencies switcher
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'houzez_get_wpc_current_currency' ) ) {

    function houzez_get_wpc_current_currency() {

        if ( isset( $_COOKIE[ "houzez_set_current_currency" ] ) ) {
            $get_current_currency = $_COOKIE[ "houzez_set_current_currency" ];
            if ( Fcc_currency_exists( $get_current_currency ) ) {
                $current_currency = $get_current_currency;
            } else {
                $current_currency = houzez_default_currency_for_switcher();
            }
        } else {
            $current_currency = houzez_default_currency_for_switcher();
        }

        return $current_currency;
    }
}

/*-----------------------------------------------------------------------------------*/
// Ajax function for currency conversion
/*-----------------------------------------------------------------------------------*/
add_action('wp_ajax_nopriv_houzez_currency_converter', 'houzez_currency_converter');
add_action('wp_ajax_houzez_currency_converter', 'houzez_currency_converter');

if ( ! function_exists( 'houzez_currency_converter' ) ) {

    function houzez_currency_converter()
    {

        if (isset($_POST['currency_converter'])) {

            $current_currency_expire = houzez_option('houzez_currency_expiry');


            if (class_exists('FCC_Rates')) {

                $currency_converter = $_POST['currency_converter'];

                // check current currency expiry time
                $currency_expiry_period = intval($current_currency_expire);
                if (!$currency_expiry_period) {
                    $currency_expiry_period = 60 * 60;
                }
                $current_currency_expiry = time() + $currency_expiry_period;

                if (Fcc_currency_exists($currency_converter) && setcookie('houzez_set_current_currency', $currency_converter, $current_currency_expiry, '/')) {
                    echo json_encode(array(
                        'success' => true
                    ));
                } else {
                    echo json_encode(array(
                        'success' => false,
                        'msg' => __("Cookie update failed", 'houzez')
                    ));
                }

            } else {
                echo json_encode(array(
                    'success' => false,
                    'msg' => __('Please install and activate wp-currencies plugin!', 'houzez')
                ));
            }

        } else {
            echo json_encode(array(
                    'success' => false,
                    'msg' => __("Request not valid", 'houzez')
                )
            );
        }

        wp_die();

    }
}

