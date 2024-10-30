<?php

class JVZoo_ipn_handler
{
    var $JVZoo_ipn_log_file;
    var $ipn_data = array();
    var $post_string;
    var $ss_ipn_log;

    var $products_table_name;
    var $customer_table_name;
    var $sales_table_name;
    var $eMember_members_table_name;
    var $members_table_name;

    function JVZoo_ipn_handler()
    {
        global $wpdb;
        $this->JVZoo_ipn_log_file = SS_JVZOO_WPESTORE_ABSPATH . '/JVZoo_ipn_handle_debug.log';
        $this->products_table_name = $wpdb->prefix . "wp_eStore_tbl";
        $this->customer_table_name = $wpdb->prefix . "wp_eStore_customer_tbl";
        $this->sales_table_name = $wpdb->prefix . "wp_eStore_sales_tbl";
        $this->eMember_members_table_name = $wpdb->prefix . "wp_eMember_members_tbl";
        $this->membership_level_table = $wpdb->prefix . "wp_eMember_membership_tbl";
        $this->members_table_name = $wpdb->prefix . "wp_eMember_members_tbl";
    }

    /**
     * Create debug log
     * @param $message
     * @param $success
     * @param bool $end
     */
    function debug_log($message, $success, $end = false)
    {
        $option_name = 'ss_jvzoo_estore_enable_debug';
        $debug_enabled = get_option($option_name);

        if (!$debug_enabled) {
            return;
        }
        $text = '[' . date('m/d/Y g:i:s A') . '] - ' . (($success) ? 'SUCCESS :' : 'FAILURE :') . $message . "\n";

        if ($end) {
            $text .= "\n------------------------------------------------------------------\n\n";
        }
        // Write to log
        $fp = fopen($this->JVZoo_ipn_log_file, 'a');
        fwrite($fp, $text);
        fclose($fp);
    }

    /**
     * Check if eMember and eStore plugin is activated.
     */
    function check_plugin_active()
    {
        global $ss_error_msg;

        $plugin_path_eMember = 'wp-eMember/wp_eMember.php';
        $plugin_path_eStore = 'wp-cart-for-digital-products/wp_cart_for_digital_products.php';

        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        if (is_plugin_active($plugin_path_eMember) && is_plugin_active($plugin_path_eStore)) {
            $this->debug_log('WPestore and WPemember Plugin Is Activated', true);
            return true;
        } else {
            $ss_error_msg .= '</br>eMember and eStore is not available.';
            $this->debug_log('eMember and eStore is not available.', false, true);
            return false;
        }
    }

    /**
     * This PHP snippet will create the cverify value and verify if it is correct using the plain text values in
     * the HTTP POST and your secret key (e.g. not URL-encoded).
     * @return bool
     */
    function jvzipnVerification()
    {
        global $ss_error_msg;
        $option_name = 'ss_jvzoo_estore';
        if (get_option($option_name) !== false) {
            $secretKey = get_option($option_name);
        } else {
            $secretKey = null;
            $ss_error_msg .= '</br>JVZoo api not found.';
            $this->debug_log('JVZoo api not found.', false, true);
            return false;
        }
        $pop = "";
        $ipnFields = array();
        foreach ($_POST AS $key => $value) {
            if ($key == "cverify") {
                continue;
            }
            $ipnFields[] = $key;
        }
        sort($ipnFields);
        foreach ($ipnFields as $field) {
            // if Magic Quotes are enabled $_POST[$field] will need to be
            // un-escaped before being appended to $pop
            $pop = $pop . $_POST[$field] . "|";
        }
        $pop = $pop . $secretKey;
        if ('UTF-8' != mb_detect_encoding($pop)) {
            $pop = mb_convert_encoding($pop, "UTF-8");
        }
        $calcedVerify = sha1($pop);
        $calcedVerify = strtoupper(substr($calcedVerify, 0, 8));
        return $calcedVerify == $_POST["cverify"];
    }

    /**
     * Curl to store eMember values
     *
     * @param $secret_key
     * @param $first_name
     * @param $last_name
     * @param $email
     * @param $membership_level_id
     * @return Return the result of eMember
     */
    function eMember_save($secret_key, $first_name, $last_name, $email, $membership_level_id)
    {
        //Need to change the site url
        $site_url = site_url() . '/wp-content/plugins/wp-eMember/api/create.php';
//        $site_url = 'https://wpdating.com/wp-content/plugins/wp-eMember/api/create.php';
        $curl_connection =
            curl_init($site_url);
        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_connection, CURLOPT_USERAGENT,
            "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);

        $post_data['secret_key'] = $secret_key;
        $post_data['username'] = $email;
        $post_data['first_name'] = $first_name;
        $post_data['last_name'] = $last_name;
        $post_data['email'] = $email;
        $post_data['membership_level_id'] = $membership_level_id;

        foreach ($post_data as $key => $value) {
            $post_items[] = $key . '=' . $value;
        }
        $post_string = implode('&', $post_items);

        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);

        $result = curl_exec($curl_connection);

        curl_close($curl_connection);
        return $result;
    }

    /**
     * Split customer name to first name and last name
     * @param $string
     * @return array|bool
     */
    function split_name($string)
    {
        $arr = explode(' ', $string);
        $num = count($arr);

        if ($num == 2) {
            list($first_name, $last_name) = $arr;
        } else {
            list($first_name, $middle_name, $last_name) = $arr;
        }

        return (empty($first_name) || $num > 3) ? false : array(
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
        );
    }

    /**
     * Map jvzoo product id and estore product id
     *
     * @param $jvzoo_product_number JVZoo product number
     * @return bool
     */
    function product_map($jvzoo_product_number)
    {
        global $wpdb;
        global $ss_error_msg;

        $wpestore_product_id = $wpdb->get_var($wpdb->prepare(
            "
                SELECT wpestore_product_id 
                FROM " . SS_MAP_PRODUCTS . "
                WHERE  jvzoo_product_id = %s
	        ",
            $jvzoo_product_number
        ));

        if ($wpestore_product_id) {
            $this->debug_log('Product Map Success(WPSTORE PRODUCT ID) : ' . $wpestore_product_id, true);
        } else {
            $ss_error_msg .= '</br>Product Map not found';
            $this->debug_log('Product Map not found', false, true);
            return false;
        }

        return $wpestore_product_id;

    }

    function validate_and_dispatch_product()
    {
        global $wpdb;
        global $ss_error_msg;
        $post_string = '';

        $firstname = '';
        $firstname_sql = '';
        $lastname = '';
        $lastname_sql = '';

        if (isset($this->ipn_data['ccustname']) && !empty($this->ipn_data['ccustname'])) {
            $name = $this->split_name($this->ipn_data['ccustname']);

            $firstname = $name['first_name'];
            $firstname_sql = esc_sql($firstname);

            $lastname = $name['last_name'];
            $lastname_sql = esc_sql($lastname);
        }

        $emailaddress = '';
        if (isset($this->ipn_data['ccustemail']) && !empty($this->ipn_data['ccustemail'])) {
            $emailaddress = $this->ipn_data['ccustemail'];
        }

        $current_product_id = '';
        if (isset($this->ipn_data['cproditem']) && !empty($this->ipn_data['cproditem'])) {
            $current_product_id_check = $this->product_map($this->ipn_data['cproditem']);
            if (!$current_product_id_check) {
                return false;
            }
            $current_product_id = $current_product_id_check;
        }

        $transaction_id = '';
        if (isset($this->ipn_data['ctransreceipt']) && !empty($this->ipn_data['ctransreceipt'])) {
            $transaction_id = $this->ipn_data['ctransreceipt'];
        }

        $clientdate = (date("Y-m-d"));
        $clienttime = (date("H:i:s"));

        $sale_price = '';
        if (isset($this->ipn_data['ctransamount']) && !empty($this->ipn_data['ctransamount'])) {
            $sale_price = $this->ipn_data['ctransamount'];
        }

        $coupon_code = '';

        $eMember_details = $wpdb->get_row(
            "
                            SELECT 	*
                            FROM " . $this->eMember_members_table_name . " 
                            WHERE email = '$emailaddress'
            "
        );

        if (!$eMember_details) {
            $ss_error_msg .= '</br>eMember details not found with the provided email address.';
            $this->debug_log('eMember details not found with the provided email address.', false, true);
            return false;
        }

        $eMember_username = $eMember_details->user_name;
        $eMember_id = $eMember_details->member_id;

        $prod_id = $current_product_id;
        $product_name = '';

        $retrieved_product = $wpdb->get_row(
            "
                SELECT * 
                FROM $this->products_table_name 
                WHERE id = '$prod_id'
            ", OBJECT
        );

        if (!$retrieved_product) {
            $ss_error_msg .= '</br>The Purchased Product was not found in WP estore';
            $this->debug_log('The Purchased Product was not found in WP estore', false, true);
            return false;
        }

        $product_name = '';
        $product_name = esc_sql(stripslashes($retrieved_product->name));

        $address = '';
        $phone = '';
        $subscr_id = '';
        $cart_item_qty = 1;
        $customer_ip = '0.0.0.0';
        $status = "Paid";
        $product_key_data = '';

        $notes = 'Bought through JVZoo';
        $street = "";
        $city = '';

        $state = '';
        if (isset($this->ipn_data['ccuststate']) && !empty($this->ipn_data['ccuststate'])) {
            $state = esc_sql($this->ipn_data['ccuststate']);
        }

        $zip = '';

        $country = '';
        if (isset($this->ipn_data['ccustcc']) && !empty($this->ipn_data['ccustcc'])) {
            $country = esc_sql($this->ipn_data['ccustcc']);
        }

        $payment_data = array(
            'gateway' => 'JVZoo',
            'custom' => '',
            'txn_id' => $transaction_id,
            'txn_type' => 'JVZoo',
            'transaction_subject' => 'JVZoo Purchase',
            'first_name' => $firstname_sql,
            'last_name' => $lastname_sql,
            'payer_email' => $emailaddress,
            'num_cart_items' => $cart_item_qty,
            'subscr_id' => $subscr_id,
            'address' => $address,
            'phone' => $phone,
            'coupon_used' => $coupon_code,
            'eMember_username' => $eMember_username,
            'eMember_userid' => $eMember_id,
            'address_street' => $street,
            'address_city' => $city,
            'address_state' => $state,
            'address_country' => $country,
        );

        $updatedb = "INSERT INTO $this->customer_table_name (first_name, last_name, email_address, purchased_product_id,txn_id,date,sale_amount,coupon_code_used,member_username,product_name,address,phone,subscr_id,purchase_qty,ipaddress,status,serial_number,notes,address_street,address_city,address_state,address_zip,address_country) 
                                 VALUES ('$firstname_sql', '$lastname_sql','$emailaddress','$current_product_id','$transaction_id','$clientdate','$sale_price','$coupon_code','$eMember_username','$product_name','$address','$phone','$subscr_id','$cart_item_qty','$customer_ip','$status','$product_key_data','$notes','$street','$city','$state','$zip','$country')";

        $results = $wpdb->query($updatedb);

        if (!$results) {
            $ss_error_msg .= '</br>Could not store the customer information WP estore customer database.';
            $this->debug_log('Could not store the customer information WP estore customer database.', false, true);
            return false;
        }

        $updatedb2 = "INSERT INTO $this->sales_table_name (cust_email, date, time, item_id, sale_price) 
                                  VALUES ('$emailaddress','$clientdate','$clienttime','$current_product_id','$sale_price')";

        $results = $wpdb->query($updatedb2);

        if (!$results) {
            $ss_error_msg .= '</br>Could not store the sales information WP estore sales database.';
            $this->debug_log('Could not store the sales information WP estore sales database.', false, true);
            return false;
        }

        $download_link = generate_download_link_for_product($current_product_id, $product_name, $payment_data);
        $this->debug_log('Download Link: [hidden]', true); //$download_link

        $notify_email = get_option('eStore_notify_email_address');  // Email which will receive notification of sale (sellers email)
        $download_email = get_option('eStore_download_email_address'); // Email from which the mail wil be sent from
        $email_subject = get_option('eStore_buyer_email_subj');
        $email_body = get_option('eStore_buyer_email_body');
        $notify_subject = get_option('eStore_seller_email_subj');
        $notify_body = get_option('eStore_seller_email_body');
        // How long the download link remain valid (hours)
        $download_url_life = get_option('eStore_download_url_life');

        $product_specific_instructions = "";
        $product_specific_instructions .= eStore_get_product_specific_instructions($retrieved_product);

        //Get currency code and symbol from settings
        $payment_currency = get_option('cart_payment_currency');
        $currency_symbol = get_option('cart_currency_symbol');


        $constructed_products_details .= "\n" . $product_name . " x " . $cart_item_qty . " - " . $currency_symbol . $sale_price . " (" . $payment_currency . ")";
        $tax_inc_price = eStore_get_tax_include_price_by_prod_id($current_product_id, $sale_price);


        $constructed_products_details_tax_inc .= "\n" . $product_name . " x " . $cart_item_qty . " - " . $currency_symbol . $tax_inc_price . " (" . $payment_currency . ")";

        $buyer_shipping_info = "\n" . $state;
        $buyer_shipping_info .= "\n" . $country;


        //Product license key generation if using the license manager
        if (function_exists('wp_lic_manager_install')) {
            $product_license_data = eStore_check_and_generate_license_key($retrieved_product, $payment_data);
        }

        $purchase_date = (date("Y-m-d"));

        $total_purchase_amt = $sale_price;

        $shipping_option = "Default";

        $total_tax = 0;
        $total_shipping = 0;

        $total_minus_total_tax = 0;
        $buyer_phone = '';

        $last_records_id = get_option('eStore_custom_receipt_counter');
        if (empty($last_records_id)) {
            $last_records_id = 0;
        }
        $receipt_counter = $last_records_id + 1;

        $tags = array("{first_name}", "{last_name}", "{payer_email}", "{product_name}", "{product_link}", "{product_price}", "{product_id}", "{download_life}",
            "{product_specific_instructions}", "{product_details}", "{product_details_tax_inclusive}", "{shipping_info}", "{license_data}", "{purchase_date}",
            "{purchase_amt}", "{transaction_id}", "{shipping_option_selected}", "{product_link_digital_items_only}", "{total_tax}", "{total_shipping}",
            "{total_minus_total_tax}", "{customer_phone}", "{counter}", "{coupon_code}", "{serial_key}", "{eMember_id}");

        $vals = array($firstname_sql, $lastname_sql, $emailaddress, $product_name, $download_link,
            $sale_price, $current_product_id, $download_url_life, $product_specific_instructions, $constructed_products_details,
            $constructed_products_details_tax_inc, $buyer_shipping_info, $product_license_data, $purchase_date, $total_purchase_amt, $transaction_id, $shipping_option,
            $download_link, $total_tax, $total_shipping, $total_minus_total_tax, $buyer_phone, $receipt_counter, $coupon_code,
            $product_key_data, $eMember_username);


        $subject = str_replace($tags, $vals, $email_subject);
        $body = stripslashes(str_replace($tags, $vals, $email_body));
        $headers = 'From: ' . $download_email . "\r\n";
        $headers .= 'Reply-To: ' . $download_email . "\r\n";
        $attachment = '';

        if (get_option('eStore_send_buyer_email')) {
            if (get_option('eStore_use_wp_mail')) {
                wp_eStore_send_wp_mail($emailaddress, $subject, $body, $headers);
                $this->debug_log('Product Email successfully sent to ' . $emailaddress . '.', true);
            } else {
                if (@eStore_send_mail($emailaddress, $body, $subject, $download_email, $attachment)) {
                    $this->debug_log('Product Email successfully sent (using PHP mail) to ' . $emailaddress . '.', true);
                } else {
                    $ss_error_msg .= '</br>Error sending product Email (using PHP mail) to ' . $emailaddress;
                    $this->debug_log('Error sending product Email (using PHP mail) to ' . $emailaddress, false);
                }
            }
        } else {
            $this->debug_log('Buyer email sending option is turned off. No email will be sent to buyer.', true);
        }

        // Notify seller
        $n_subject = str_replace($tags, $vals, $notify_subject);
        $n_body = str_replace($tags, $vals, $notify_body);
        if (get_option('eStore_add_payment_parameters_to_admin_email') == '1') {
            $n_body .= "\n\n------- User Email ----------\n" .
                $body .
                "\n\n------- Paypal Parameters (Only admin will receive this) -----\n" .
                $this->post_string;
        }
        $n_body = stripslashes($n_body);

        $notify_emails_array = explode(",", $notify_email);
        foreach ($notify_emails_array as $notify_email_address) {
            if (!empty($notify_email_address)) {
                $recipient_email_address = trim($notify_email_address);
                if (get_option('eStore_use_wp_mail')) {
                    wp_eStore_send_wp_mail($recipient_email_address, $n_subject, $n_body, $headers);
                    $this->debug_log('Notify Email successfully sent to ' . $recipient_email_address . '.', true, true);
                } else {
                    if (@eStore_send_mail($recipient_email_address, $n_body, $n_subject, $download_email)) {
                        $this->debug_log('Notify Email successfully sent (using PHP mail) to ' . $recipient_email_address . '.', true, true);
                    } else {
                        $ss_error_msg .= 'Error sending notify Email (using PHP mail) to ' . $recipient_email_address;
                        $this->debug_log('Error sending notify Email (using PHP mail) to ' . $recipient_email_address, false, true);
                    }
                }
            }
        }
        return true;
    }

    /**
     * Check to see if transaction is already processed
     *
     * @param $payment_data
     * @return bool
     */
    function jvzoo_is_txn_already_processed($payment_data)
    {
        global $wpdb;
        $txn_id = $payment_data['ctransreceipt'];
        $emailaddress = $payment_data['ccustemail'];
        $resultset = $wpdb->get_results("SELECT * FROM $this->customer_table_name WHERE txn_id = '$txn_id' and email_address = '$emailaddress'", OBJECT);
        if ($resultset) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the page is directly accessed
     * @return bool
     */
    function check_post_values()
    {
        //Need to remove the post values
//        $_POST['ctransreceipt'] = '0CN210036W4715104';
        if (!isset($_POST['ctransreceipt'])) {
            $this->debug_log('Site is normally opened', true, true);
            return false;
        } else {
            $this->debug_log('Site is opened after payment from JVZoo', true);
            return true;
        }
    }

    /**
     * This function is used to handle refund
     * @param $payment_data
     * @return bool|void
     */
    function eStore_handle_refund($payment_data)
    {
        $this->debug_log('Handling refund request...', true);
        global $wpdb, $ss_error_msg;
        $parent_txn_id = $payment_data['ctransreceipt'];
        $emailaddress = $payment_data['ccustemail'];
        $clientdate = (date("Y-m-d"));
        $clienttime = (date("H:i:s"));
        $product_id = $this->product_map($payment_data['cproditem']);
        $sale_price = $payment_data['ctransamount'];

        //Check if this txn exists
        $resultsets = $wpdb->get_results("SELECT * FROM $this->customer_table_name WHERE txn_id = '$parent_txn_id'", OBJECT);

        if (!$resultsets) {
            $this->debug_log('Could not find the record associated with this transaction in the database.', false);
            return;
        }

        if (function_exists('wp_eMember_install')) {
            // deactivate eMember account if applicable
            $this->eMember_handle_subsc_cancel($payment_data, true);
        }

        $sale_price = $sale_price * -1;
        $sale_price = number_format($sale_price, 2, '.', '');

        $this->debug_log('Updating sales database table with the refund amount: ' . $sale_price, true);

        $updatedb2 = "INSERT INTO $this->sales_table_name (cust_email, date, time, item_id, sale_price) VALUES ('$emailaddress','$clientdate','$clienttime','$product_id','$sale_price')";
        $results = $wpdb->query($updatedb2);

        if (!$results) {
            $ss_error_msg .= '</br>Could not store the refund customer information WP Sales database.';
            $this->debug_log('Could not store the refund customer information WP Sales database.', false, true);
            return false;
        } else {
            $this->debug_log('Stored the refund information in WP Sales database.', true);
        }

        if (get_option('eStore_auto_customer_removal')) {
            $updatedb = "DELETE FROM $this->customer_table_name WHERE txn_id='$parent_txn_id'";
            $results = $wpdb->query($updatedb);

            if (!$results) {
                $ss_error_msg .= '</br>Could not delete the customer information WP estore customer database.';
                $this->debug_log('Could not delete the customer information WP estore customer database.', false);
                return false;
            } else {
                $this->debug_log('Deleted customer information from WP estore customer database.', false);
            }
        }
        return;
    }

    /**
     * This function is used to handle refund for wpemember
     * @param $ipn_data
     * @param bool $refund
     */
    function eMember_handle_subsc_cancel($ipn_data, $refund = false)
    {
        if (!function_exists('wp_eMember_install')) {
            $this->debug_log("WP eMember plugin is not active so no action is necessary for this subscription cancellation notification.", true);
            return;
        }

        if ($refund) {
            $email = $ipn_data['ccustemail'];
        } else {
            return;
        }

        global $wpdb;

        $this->debug_log("Retrieving member account from the database...", true);

        $resultset = $wpdb->get_row("SELECT * FROM $this->members_table_name where email='$email'", OBJECT);

        if ($resultset) {
            $account_state = 'inactive';
            $updatedb = "UPDATE $this->members_table_name SET account_state='$account_state' WHERE email='$email'";
            $results = $wpdb->query($updatedb);
            $this->debug_log("Member account deactivated.", true);
        } else {
            $this->debug_log("No member found for the given EMAIL ID:" . $email, false);
            return;
        }
    }

    /**
     * Prepare items for purchase
     *
     * @return bool
     */
    function prepare_items()
    {
        global $wpdb;
        global $ss_error_msg;

        $check_plugin_active = $this->check_plugin_active();

        if (!$check_plugin_active) {
            return false;
        }

        /**
         * Test parameters set
         */

        //Need to comment out
//        $_POST['ccustname'] = "Ravin Bhattarai";
//        $_POST['ccustemail'] = "contact@wpdating.com";
//        $_POST['cproditem'] = 223869;
//        $_POST['ctransreceipt'] = '654500594Y195770V';
//        $_POST['ctransamount'] = '1';
//        $_POST['ccuststate'] = 'Atlanta';
//        $_POST['ccustcc'] = 'US';
//        $_POST['cverify'] = '79BB9158';
//        $_POST['ctransaction'] = 'RFND';

        foreach ($_POST as $field => $value) {
            $this->ipn_data["$field"] = $value;
            $post_string .= $field . '=' . urlencode(stripslashes($value)) . '&';
        }

        $this->post_string = $post_string;

        $this->debug_log('Post string : ' . $this->post_string, true);

        $jvzoo_verification = $this->jvzipnVerification();

        //Need to revert
        if (!$jvzoo_verification) {
            $this->debug_log('JVZoo Verification failed.', false, true);
            $ss_error_msg .= '</br>Purchase is not valid';
            return false;
        }

        $this->debug_log('JVZoo Verification Passed.', true);

        //Check for refund payment
        $transaction_status = $this->ipn_data['ctransaction'];

        if ($transaction_status == 'RFND') {
            // This is a refund or reversal so handle the refund
            $this->eStore_handle_refund($this->ipn_data);
            $this->debug_log('This is a refund/reversal. Refund amount: ' . $this->ipn_data['ctransamount'], true, true);
            return true;
        }

        if ($this->jvzoo_is_txn_already_processed($this->ipn_data)) {
            $this->debug_log('The transaction ID and the email address already exists in the database. So this seems to be a duplicate transaction notification. ', false, true);
            $ss_error_msg .= 'The transaction ID and the email address already exists in the database. So this seems to be a duplicate transaction notification. ';
            return true;
        }

        $Ss_Jvzoo_Wpestore_Config = Ss_Jvzoo_Wpestore_Config::getInstance();

        $secret_key = $Ss_Jvzoo_Wpestore_Config->getValue('secret_word');

        if ($secret_key == '') {
            $this->debug_log('Secret Word not found', false, true);
            $ss_error_msg .= 'Secret Word not found';
            return false;
        }

        $name = $this->split_name($this->ipn_data['ccustname']);

        $firstname = $name['first_name'];
        $lastname = $name['last_name'];
        $email = $this->ipn_data['ccustemail'];
        $membership_level_id = $Ss_Jvzoo_Wpestore_Config->getValue('membership_level');

        if ($membership_level_id == '') {
            $this->debug_log('Membership level not found', false);
            $ss_error_msg .= 'Membership level not found';
        } else {
            $this->debug_log('Membership level found', true);
            $ss_error_msg .= 'Membership level found';
        }

        if (emember_wp_email_exists($email) || emember_email_exists($email)) {
            $this->debug_log('Email address found. No need to create new emember', true);
        } else {
            $eMember_result = $this->eMember_save($secret_key, $firstname, $lastname, $email, $membership_level_id);
        }

        $member_count = $wpdb->get_var(
            "
                            SELECT COUNT(*) 
                            FROM " . $this->eMember_members_table_name . " 
                            WHERE email = '$email'
            "
        );

        if ($member_count == 0) {
            $ss_error_msg .= '</br>eMember not found';
            $this->debug_log('eMember not found', false, true);
            return false;
        }

        $this->debug_log('eMember found', true);

        $validate_and_dispath_product = $this->validate_and_dispatch_product();

        if (!$validate_and_dispath_product) {
            return false;
        }

        return true;
    }


}

global $wpdb;
global $ss_error_msg;
global $ss_display_msg;
$JVZoo_ipn_handler_instance = new JVZoo_ipn_handler();
$Ss_Jvzoo_Wpestore_Config = Ss_Jvzoo_Wpestore_Config::getInstance();

$content_success = $Ss_Jvzoo_Wpestore_Config->getValue('success_message');
$content_error = $Ss_Jvzoo_Wpestore_Config->getValue('error_message');

$post_values = false;
$post_values = $JVZoo_ipn_handler_instance->check_post_values();

if ($post_values) {
    if ($JVZoo_ipn_handler_instance->prepare_items()) {
        $ss_display_msg .= $content_success;
    } else {
        $ss_display_msg .= $content_error;
    }
}

if (!empty($ss_display_msg)) {
    echo '<div class = "ss-final-message ">';
    echo $ss_display_msg;
    echo '</div>';
}
