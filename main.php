<?php
/**
Plugin name: Custom Product Popup
Description: Lightweight product popup plugin for showing products in popup. easy to install and use shortcode [popup_product_widget] for diplay icon on any pages.
version: 1.0.0
author: Paul Elliott
**/
add_action('plugins_loaded', 'check_for_woocommerce');
function check_for_woocommerce() {
    if (!class_exists('Woocommerce')) 
    {
        add_action( 'admin_notices', 'my_plugin_woocommerce_check_notice' );
        return;
    }
}

function my_plugin_woocommerce_check_notice() {
    ?>
    <div class="alert alert-danger notice is-dismissible" style="background: #d63638; color: white;">
        <p>Sorry, but this plugin requires WooCommerce in order to work.
            So please ensure that WooCommerce is both installed and activated.
        </p>
    </div>
    <?php
    deactivate_plugins(plugin_basename(__FILE__));
}
add_action( 'admin_menu', 'register_my_custom_menu_page' );
function register_my_custom_menu_page() {
  // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
  add_menu_page( 'Product Popup', 'Product Popup', 'manage_options', 'custom_product_popup', 'show_custom_product_popup', 'dashicons-welcome-widgets-menus', 40 );
 
}

function show_custom_product_popup(){
     $popup_config = get_option('custom_prodcut_popup_confuig');
    if(isset($_POST['submit_popup_settings'])){
        
       $popup_config['title'] = $_POST['cp_title'];
       $popup_config['icon_url'] = $_POST['image_url'];
       $popup_config['icon_id'] = $_POST['hidden_img_id'];
       $popup_config['select_cat'] = $_POST['select_cat'];
       $popup_config['product_num'] = $_POST['product_num'];
       $popup_config['cp_stripe_key'] = $_POST['cp_stripe_key'];
       
       update_option('custom_prodcut_popup_confuig',$popup_config);
        
        $msg = '<p style="background: green; color: white; padding: 10px; width: 34%; font-weight: bold;">Save Successfully..!!</p>';
    }
    $p_title            = (!empty($popup_config) && !empty($popup_config['title']) ) ? $popup_config['title'] : '';
    $p_icon_url         = (!empty($popup_config) && !empty($popup_config['icon_url']) ) ? $popup_config['icon_url'] : '';
    $p_icon_id          = (!empty($popup_config) && !empty($popup_config['icon_id']) ) ? $popup_config['icon_id'] : '';
    $p_select_cat       = (!empty($popup_config) && !empty($popup_config['select_cat']) ) ? $popup_config['select_cat'] : '';
    $p_product_num      = (!empty($popup_config) && !empty($popup_config['product_num'] ) ) ? $popup_config['product_num']  : '';
    $cp_stripe_key = (!empty($popup_config) && !empty($popup_config['cp_stripe_key'] ) ) ? $popup_config['cp_stripe_key']  : '';
    
    $product_categories = get_terms( 'product_cat' );
    $cat_drop = '';
    if(!empty($product_categories)):
        foreach($product_categories as $single_cat){
            $show_cat_name = '';
            $show_cat_name = $single_cat->name;
             if ( $single_cat->parent != 0 ){
                    $parent  = get_term_by( 'id', $single_cat->parent, 'product_cat' );
                    $show_cat_name = $parent->name." > ".$single_cat->name;
                }
                 $selected = '';
                if(!empty($p_select_cat) && $p_select_cat == $single_cat->term_id ){ $selected = 'selected'; }
            $cat_drop .= '<option value="'.$single_cat->term_id.'" '.$selected.'>'.$show_cat_name.'</option>';
        }
    endif;
    echo "<h2>Custom Product Popup</h2>
            <p><small>Use this shortcode [popup_product_widget] for diplay icon on any pages.</small></p>
        <div class='settings_form'>$msg
        <form method='post'>
            <div class='single-group'>
                <div class='label-div'>Popup Top Title:</div>
                <input type='text' name='cp_title' value='$p_title' style='width:35%;'>
            </div>
            <div class='single-group'>
                <div class='label-div'>Icon Image:</div>
                 <input type='text' name='image_url' id='image_url' class='regular-text' value='$p_icon_url'>
                 <input type='hidden' name='hidden_img_id' id='hidden_img_id' value='$p_icon_id'>
                <input type='button' name='upload-btn' id='upload-btn' class='button-secondary' value='Upload Image'>
                
            </div>
            <div class='single-group'>
                    <div class='label-div'>Select Category:</div>
                    <select name='select_cat'  style='width:100%'>
                                  <option value=''> -- Select Category ---</option>'.$cat_drop.'
                   </select>
               </div>
              <div class='single-group'>
                <div class='label-div'>Number of Products to show:</div>
                    <input type='number' name='product_num' value='$p_product_num' style='width:35%;'>
                    <p style='width: 45%;'>Note : You can set limit for showing products in popup if blank then show 5 products. Write -1 for showing all products of selected category.</p>
                </div>
                <div class='single-group'>
                <div class='label-div'>Stripe Key:</div>
                    <input type='text' name='cp_stripe_key' value='$cp_stripe_key' style='width:35%;'>

                </div>

            <input type='submit' name='submit_popup_settings' value='Save'>
        </from></div>
        <style>
            .single-group .label-div {
                padding-bottom: 4px;
                font-weight: 500;
                font-size: 14px;
            }
            .single-group {
                margin: 10px 0;
            }
            .single-group p {
                padding: 0;
                margin: 2px 0 10px;
            }
            input[name='submit_google'] {
                background-color: #2271b1;
                color: white;
                border: 0;
                padding: 10px 25px;
                font-weight: bold;
            }
        </style>
        <script type='text/javascript'>
jQuery(document).ready(function(){
    jQuery('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_obj = uploaded_image.toJSON();
            jQuery('#hidden_img_id').val(image_obj.id);
            jQuery('#image_url').val(image_obj.url);
        });
    });
});
</script>
        ";
}


function load_wp_media_files() {
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'load_wp_media_files' );

add_shortcode("popup_product_widget", "popup_product_widget");
function popup_product_widget(){

    $popup_config       = get_option('custom_prodcut_popup_confuig');

    $p_title            = (!empty($popup_config) && !empty($popup_config['title']) ) ? $popup_config['title'] : 'Product Widget';
    $p_icon_url         = (!empty($popup_config) && !empty($popup_config['icon_url']) ) ? $popup_config['icon_url'] : plugin_dir_url( __FILE__ ).'assets/img/chat.png';
    $p_icon_id          = (!empty($popup_config) && !empty($popup_config['icon_id']) ) ? $popup_config['icon_id'] : '';
    $p_select_cat       = (!empty($popup_config) && !empty($popup_config['select_cat']) ) ? $popup_config['select_cat'] : '';
    $p_product_num      = (!empty($popup_config) && !empty($popup_config['product_num'] ) ) ? $popup_config['product_num']  : '5';

    $pro_args = array('post_type' => 'product','numberposts' => $p_product_num,'post_status' => 'publish');

    if(!empty($p_select_cat)){
        $pro_args['tax_query'] = array( array('taxonomy' => 'product_cat','field' => 'term_id','terms' => $p_select_cat ) );
    }

    $all_products = get_posts($pro_args);

    $prod_drop = '<select class="cp_product_options"><option value=""> Select A Product... </option>';
                foreach($all_products as $single_pro){
                    $prod_drop .= '<option value="'.$single_pro->ID.'">'.$single_pro->post_title.'</option>';
                }
    $prod_drop .= '</select>';

    echo ' <button class="open-button">
            <div class="cp-chat-img" id="cp-chat-icon" onclick="openForm()">
                <img src="'.$p_icon_url.'" >
            </div>
            <div class="cp-chat-img-1">
                <img id="cp-close-icon" src="'.plugin_dir_url( __FILE__ ).'assets/img/close.png" onclick="closeForm()" style="display:none;">
            </div>
            </button>

    <div class="chat-popup" id="myForm">
      <div  class="form-container">
        <div class="test-f">
            <p>'.$p_title.'</p>
        </div>
    
        <div class="tawk-body">
            <div class="tawk-form">
            <form action="" class="form-container" onsubmit="return false;">
                <div class="tabs-container">
                    <div class="tabs">
                        <input type="radio" name="tabs" id="tab-1" checked="checked">
                        <label for="tab-1">Product List</label>

                        <input type="radio" name="tabs" id="tab-2">
                        <label for="tab-2">Custom Payment</label>

                        <div class="tab cp-tab">
                            <div class="tawk-product">
                                <label>Products List</label>
                                '.$prod_drop.'
                                <p class="cp_error_p" style="display:none">Please select Product from Dropdown.</p>
								<div class="tawk-product-description">
                                    <div class="product-description-sec">
                                        <label>Product Description</label>
                                        <p>Deep Peach Lucknowi Embroidered Anarkali Suit features traditional lucknowi embroidered georgette top with all over resham thread and sequence work detail paired with matching satin silk bottom and lining.</p>
                                    </div>
                                    <div class="product-price-sec">
                                        <label>Price</label>
                                        <span>$200.00</span>    
                                    </div>
                                </div>
                            </div>
                            <div class="live-btn mp-live">
                                <button type="button" class="live-che cp-make-payment">Proceed To Checkout</button>
                            </div>
                        </div>
                        <div class="tab cp_tebbing">
                            <div class="cp-enter-amout cp_amout">
                                <label>Amount</label>
                                <div class="currency-div"><input type="number" id="quantity" name="quantity" min="1" max="10" placeholder="0.00" autocomplete="off"></div>
                                <p class="cp_error_p_amt" style="display:none">Please enter amount.</p>
                            </div>
                            <div class="cp-enter-amout">
                                <label>Description</label>
                                <textarea rows="2" name="comment" form="usrform" placeholder="Enter Description..."></textarea>
                            </div>
                            <div class="live-btn">
                                <button type="button" class="live-che cp-live">Proceed To Checkout</button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
                <div class="cell example example4" id="example-4">
                    <a href="javascript:void(0);" class="back_to_form">&#8592;  Back</a>
                        <form>
                          <div id="example4-paymentRequest">
                            <!--Stripe paymentRequestButton Element inserted here-->
                          </div>
                          <fieldset>
                            <legend class="card-only" data-tid="elements_examples.form.pay_with_card">Pay with card</legend>
                            <legend class="payment-request-available" data-tid="elements_examples.form.enter_card_manually">Or enter card details</legend>
                            <div class="container">
                              <div id="example4-card"></div>
                              
                              <div class="powered_by_stripe"><img src="'.plugin_dir_url( __FILE__ ).'assets/img/powered_by_stripe.png"></div>
                            </div>

                          </fieldset>
                          <button type="submit" data-tid="elements_examples.form.donate_button" class="cp-make-pay">Pay Now</button>
                          <div class="error" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
                              <path class="base" fill="#000" d="M8.5,17 C3.80557963,17 0,13.1944204 0,8.5 C0,3.80557963 3.80557963,0 8.5,0 C13.1944204,0 17,3.80557963 17,8.5 C17,13.1944204 13.1944204,17 8.5,17 Z"></path>
                              <path class="glyph" fill="#FFF" d="M8.5,7.29791847 L6.12604076,4.92395924 C5.79409512,4.59201359 5.25590488,4.59201359 4.92395924,4.92395924 C4.59201359,5.25590488 4.59201359,5.79409512 4.92395924,6.12604076 L7.29791847,8.5 L4.92395924,10.8739592 C4.59201359,11.2059049 4.59201359,11.7440951 4.92395924,12.0760408 C5.25590488,12.4079864 5.79409512,12.4079864 6.12604076,12.0760408 L8.5,9.70208153 L10.8739592,12.0760408 C11.2059049,12.4079864 11.7440951,12.4079864 12.0760408,12.0760408 C12.4079864,11.7440951 12.4079864,11.2059049 12.0760408,10.8739592 L9.70208153,8.5 L12.0760408,6.12604076 C12.4079864,5.79409512 12.4079864,5.25590488 12.0760408,4.92395924 C11.7440951,4.59201359 11.2059049,4.59201359 10.8739592,4.92395924 L8.5,7.29791847 L8.5,7.29791847 Z"></path>
                            </svg>
                            <span class="message"></span></div>
                        </form>
                        <div class="success">
                          <div class="icon">
                            <svg width="40px" height="40px" viewBox="0 0 84 84" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                              <circle class="border" cx="42" cy="42" r="40" stroke-linecap="round" stroke-width="4" stroke="#000" fill="none"></circle>
                              <path class="checkmark" stroke-linecap="round" stroke-linejoin="round" d="M23.375 42.5488281 36.8840688 56.0578969 64.891932 28.0500338" stroke-width="4" stroke="#000" fill="none"></path>
                            </svg>
                          </div>
                          <h3 class="title" data-tid="elements_examples.success.title">Payment successful</h3>
                          <p class="message"><span data-tid="elements_examples.success.message">Thanks for trying Stripe Elements. No money was charged, but we generated a token: </span><span class="token">tok_189gMN2eZvKYlo2CwTBv9KKh</span></p>
                          <a class="reset" href="#">
                            <svg width="32px" height="32px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                              <path fill="#000000" d="M15,7.05492878 C10.5000495,7.55237307 7,11.3674463 7,16 C7,20.9705627 11.0294373,25 16,25 C20.9705627,25 25,20.9705627 25,16 C25,15.3627484 24.4834055,14.8461538 23.8461538,14.8461538 C23.2089022,14.8461538 22.6923077,15.3627484 22.6923077,16 C22.6923077,19.6960595 19.6960595,22.6923077 16,22.6923077 C12.3039405,22.6923077 9.30769231,19.6960595 9.30769231,16 C9.30769231,12.3039405 12.3039405,9.30769231 16,9.30769231 L16,12.0841673 C16,12.1800431 16.0275652,12.2738974 16.0794108,12.354546 C16.2287368,12.5868311 16.5380938,12.6540826 16.7703788,12.5047565 L22.3457501,8.92058924 L22.3457501,8.92058924 C22.4060014,8.88185624 22.4572275,8.83063012 22.4959605,8.7703788 C22.6452866,8.53809377 22.5780351,8.22873685 22.3457501,8.07941076 L22.3457501,8.07941076 L16.7703788,4.49524351 C16.6897301,4.44339794 16.5958758,4.41583275 16.5,4.41583275 C16.2238576,4.41583275 16,4.63969037 16,4.91583275 L16,7 L15,7 L15,7.05492878 Z M16,32 C7.163444,32 0,24.836556 0,16 C0,7.163444 7.163444,0 16,0 C24.836556,0 32,7.163444 32,16 C32,24.836556 24.836556,32 16,32 Z"></path>
                            </svg>
                          </a>
                        </div>

                        <div class="caption">
                          <span data-tid="elements_examples.caption.no_charge" class="no-charge">Your card won"t be charged</span>
                          <a class="source" href="https://github.com/stripe/elements-examples/#example-4">
                            <svg width="16px" height="10px" viewBox="0 0 16 10" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                              <path d="M1,8 L12,8 C12.5522847,8 13,8.44771525 13,9 C13,9.55228475 12.5522847,10 12,10 L1,10 C0.44771525,10 6.76353751e-17,9.55228475 0,9 C-6.76353751e-17,8.44771525 0.44771525,8 1,8 L1,8 Z M1,4 L8,4 C8.55228475,4 9,4.44771525 9,5 C9,5.55228475 8.55228475,6 8,6 L1,6 C0.44771525,6 6.76353751e-17,5.55228475 0,5 C-6.76353751e-17,4.44771525 0.44771525,4 1,4 L1,4 Z M1,0 L15,0 C15.5522847,-1.01453063e-16 16,0.44771525 16,1 L16,1 C16,1.55228475 15.5522847,2 15,2 L1,2 C0.44771525,2 6.76353751e-17,1.55228475 0,1 L0,1 L0,1 C-6.76353751e-17,0.44771525 0.44771525,1.01453063e-16 1,0 L1,0 Z" fill="#AAB7C4"></path>
                            </svg>
                          </a>
                        </div>
                    </div>
                
            </div>
        </div>
      </div>
      <input type="hidden" id="hidden_cp_stripe_key" value="'.$popup_config['cp_stripe_key'].'">
      <script src="https://js.stripe.com/v3/"></script>
      <script type="text/javascript" src="'.plugin_dir_url( __FILE__ ).'assets/js/stripe_index.js"></script>
      <link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'assets/css/example4.css">


    </div>
    <link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'assets/css/cp_styles.css">
    <script>
        function openForm() {
          document.getElementById("myForm").style.display = "block";
          document.getElementById("cp-chat-icon").style.display = "none";
          document.getElementById("cp-close-icon").style.display = "block";
        }

        function closeForm() {
          document.getElementById("myForm").style.display = "none";
          document.getElementById("cp-chat-icon").style.display = "block";
          document.getElementById("cp-close-icon").style.display = "none";
        }
        document.onreadystatechange = function () {
            jQuery(document).on("click", ".cp-make-payment", function(){
                var selected_val = jQuery("select.cp_product_options").val();
                if(selected_val != ""){
                        jQuery(".cp_error_p").hide();
                        jQuery.ajax({
                                type:"get",
                                url:"'.get_site_url().'/?add-to-cart="+selected_val+"&quantity=1",
                                success:function(res){
                                    window.location.href="'.wc_get_checkout_url().'";
                                }

                            })
                    } else {
                        jQuery(".cp_error_p").show();
                    }
                });
                jQuery(document).on("input",".cp_amout input",function(event) {
                  var v = this.value, dollar = "0", cents = "00";
                  if (v.indexOf(".") !== -1) {
                    var price = v.split(".");
                    dollar = price[0] || "0";
                    cents = price[1] || "00";
                  }
                  if (cents.length === 1) {
                    if (dollar) {
                      var dollarNumbers = dollar.split("");
                      var dollarLength = dollarNumbers.length;
                      cents = dollarNumbers[dollarLength-1]+cents
                      dollar = "";
                      for (var i = 0; i < dollarLength-1 ; i++) {
                        dollar += dollarNumbers[i];
                      }
                      if (!dollar) {dollar = "0";}
                    }
                  }
                  if (v.length === 1) {
                    cents = "0"+v;
                  }
                  if (cents.length === 3) {
                    var centNumbers = cents.split("");
                    dollar = dollar === "0" ? centNumbers[0] : dollar+centNumbers[0];
                    cents = centNumbers[1]+centNumbers[2];
                  }
                  this.value = dollar+"."+cents;
                });

            }
       
    </script>

    <script>
        document.addEventListener("DOMContentLoaded",function(){
          "use strict";
    
          var elements = stripe.elements({
            fonts: [
              {
                cssSrc: "https://rsms.me/inter/inter.css"
              }
            ],
            // Stripe"s examples are localized to specific languages, but if
            // you wish to have Elements automatically detect your user"s locale,
            // use `locale: "auto"` instead.
            locale: window.__exampleLocale
          });

          /**
           * Card Element
           */
          var card = elements.create("card", {
            style: {
              base: {
                color: "#000",
                fontWeight: 400,
                fontFamily: "Inter, Open Sans, Segoe UI, sans-serif",
                fontSize: "16px",
                fontSmoothing: "antialiased",

                "::placeholder": {
                  color: "#000"
                }
              },
              invalid: {
                color: "#000"
              }
            }
          });

          card.mount("#example4-card");

          /**
           * Payment Request Element
           */
          var paymentRequest = stripe.paymentRequest({
            country: "US",
            currency: "usd",
            total: {
              amount: 2000,
              label: "Total"
            }
          });
          paymentRequest.on("token", function(result) {
            var example = document.querySelector(".example4");
            example.querySelector(".token").innerText = result.token.id;
            example.classList.add("submitted");
            result.complete("success");
          });

          var paymentRequestElement = elements.create("paymentRequestButton", {
            paymentRequest: paymentRequest,
            style: {
              paymentRequestButton: {
                type: "donate"
              }
            }
          });

          paymentRequest.canMakePayment().then(function(result) {
            if (result) {
              document.querySelector(".example4 .card-only").style.display = "none";
              document.querySelector(
                ".example4 .payment-request-available"
              ).style.display =
                "block";
              paymentRequestElement.mount("#example4-paymentRequest");
            }
          });

          registerElements([card, paymentRequestElement], "example4");
        });
    </script>

    <script>
    jQuery(document).on("click",".cp-live", function(){
        var cp_amt = jQuery(".cp-enter-amout input#quantity").val();
        
        if(cp_amt < 0 || cp_amt == ""){
            jQuery(".cp_error_p_amt").show();
            return false;
        } 
        jQuery(".cp_error_p_amt").hide();
        jQuery(".tabs-container").addClass("intro");
        jQuery(".cell").addClass("cp-intro");
    });
	jQuery(document).on("change", ".cp_product_options", function(){
		var pro_id = jQuery(this).val();
		if(pro_id == ""){
				jQuery(".tawk-product-description").hide();
		} else {
			jQuery.ajax({
				type:"post",
				url:"'.admin_url('admin-ajax.php').'",
				data:{action:"get_custom_product_info",pro_id:pro_id},
				success:function(res){
				     jQuery(".tawk-product-description").html(res);
					jQuery(".tawk-product-description").show();
					
				}
			});
		}
	});
    jQuery(document).on("click",".cp-make-payment",function(){
      jQuery(".cp-tab").addClass("cp-demo");
    });
    jQuery(document).on("click",".back_to_form", function(){
        jQuery(".cell").removeClass("cp-intro");
        jQuery(".tabs-container").removeClass("intro");
    });

    </script>

    ';

    
}

add_action("wp_ajax_get_custom_product_info", "get_custom_product_info");
add_action("wp_ajax_nopriv_get_custom_product_info", "get_custom_product_info");
function get_custom_product_info(){
	if(!empty($_POST['pro_id'])){
		$product = wc_get_product( $_POST['pro_id'] );
		echo ' <div class="product-description-sec">
                         <label>Product Description</label>
                             <p>'.$product->get_short_description().'</p>
                         </div>
                             <div class="product-price-sec">
                                    <label>Price</label>
                                     <span>'.wc_price($product->get_price()).'</span>    
                                </div>';
	}
	die();
}

?>