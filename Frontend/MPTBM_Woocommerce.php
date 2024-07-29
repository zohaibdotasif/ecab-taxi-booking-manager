<?php
/*
* @Author 		engr.sumonazma@gmail.com
* Copyright: 	mage-people.com
*/
if (!defined('ABSPATH')) {
	die;
} // Cannot access pages directly.
if (!class_exists('MPTBM_Woocommerce')) {
	class MPTBM_Woocommerce
	{
		public function __construct()
		{
			add_filter('woocommerce_add_cart_item_data', array($this, 'add_cart_item_data'), 90, 3);
			add_action('woocommerce_before_calculate_totals', array($this, 'before_calculate_totals'), 90);
			add_filter('woocommerce_cart_item_thumbnail', array($this, 'cart_item_thumbnail'), 90, 3);
			add_filter('woocommerce_get_item_data', array($this, 'get_item_data'), 90, 2);
			//************//
			add_action('woocommerce_after_checkout_validation', array($this, 'after_checkout_validation'));
			add_action('woocommerce_checkout_create_order_line_item', array($this, 'checkout_create_order_line_item'), 90, 4);
			add_action('woocommerce_checkout_order_processed', array($this, 'checkout_order_processed'));
			add_filter('woocommerce_order_status_changed', array($this, 'order_status_changed'));
			/*****************************/
			add_action('wp_ajax_mptbm_add_to_cart', [$this, 'mptbm_add_to_cart']);
			add_action('wp_ajax_nopriv_mptbm_add_to_cart', [$this, 'mptbm_add_to_cart']);
		}
		public function add_cart_item_data($cart_item_data, $product_id)
		{
			$linked_id = MP_Global_Function::get_post_info($product_id, 'link_mptbm_id', $product_id);
			$post_id = is_string(get_post_status($linked_id)) ? $linked_id : $product_id;
			if (get_post_type($post_id) == MPTBM_Function::get_cpt()) {
				$distance = isset($_COOKIE['mptbm_distance']) ? absint($_COOKIE['mptbm_distance']) : '';
				$duration = isset($_COOKIE['mptbm_duration']) ? absint($_COOKIE['mptbm_duration']) : '';
				$start_place = isset($_POST['mptbm_start_place']) ? sanitize_text_field($_POST['mptbm_start_place']) : '';
				$end_place = isset($_POST['mptbm_end_place']) ? sanitize_text_field($_POST['mptbm_end_place']) : '';
				$waiting_time = isset($_POST['mptbm_waiting_time']) ? sanitize_text_field($_POST['mptbm_waiting_time']) : 0;
				$return = isset($_POST['mptbm_taxi_return']) ? sanitize_text_field($_POST['mptbm_taxi_return']) : 1;
				$fixed_hour = isset($_POST['mptbm_fixed_hours']) ? sanitize_text_field($_POST['mptbm_fixed_hours']) : 0;
				$total_price = $this->get_cart_total_price($post_id);
				$price = MPTBM_Function::get_price($post_id, $distance, $duration, $start_place, $end_place, $waiting_time, $return, $fixed_hour);
				$wc_price = MP_Global_Function::wc_price($post_id, $price);
				$raw_price = MP_Global_Function::price_convert_raw($wc_price);
				$cart_item_data['mptbm_date'] = isset($_POST['mptbm_date']) ? sanitize_text_field($_POST['mptbm_date']) : '';
				$cart_item_data['mptbm_taxi_return'] = $return;
				$cart_item_data['mptbm_waiting_time'] = $waiting_time;
				$cart_item_data['mptbm_start_place'] = wp_strip_all_tags($start_place);
				$cart_item_data['mptbm_end_place'] = wp_strip_all_tags($end_place);
				$cart_item_data['mptbm_distance'] = $distance;
				$cart_item_data['mptbm_distance_text'] = isset($_COOKIE['mptbm_distance_text']) ? sanitize_text_field($_COOKIE['mptbm_distance_text']) : '';
				$cart_item_data['mptbm_duration'] = $duration;
				$cart_item_data['mptbm_fixed_hours'] = $fixed_hour;
				$cart_item_data['mptbm_duration_text'] = isset($_COOKIE['mptbm_duration_text']) ? sanitize_text_field($_COOKIE['mptbm_duration_text']) : '';
				$cart_item_data['mptbm_base_price'] = $raw_price;
				$cart_item_data['mptbm_extra_service_info'] = self::cart_extra_service_info($post_id);
				$cart_item_data['mptbm_tp'] = $total_price;
				$cart_item_data['line_total'] = $total_price;
				$cart_item_data['line_subtotal'] = $total_price;
				if ($return > 1 && MP_Global_Function::get_settings('mptbm_general_settings', 'enable_return_in_different_date') == 'yes') {
					$return_target_date = isset($_POST['mptbm_return_date']) ? sanitize_text_field($_POST['mptbm_return_date']) : '';
					$return_target_time = isset($_POST['mptbm_return_time']) ? sanitize_text_field($_POST['mptbm_return_time']) : '';
					$cart_item_data['mptbm_return_target_date'] = $return_target_date;
					$cart_item_data['mptbm_return_target_time'] = $return_target_time;
				}
				$cart_item_data = apply_filters('mptbm_add_cart_item', $cart_item_data, $post_id);
			}
			$cart_item_data['mptbm_id'] = $post_id;
			//echo '<pre>';print_r($cart_item_data);echo '</pre>';die();
			return $cart_item_data;
		}
		public function before_calculate_totals($cart_object)
		{
			foreach ($cart_object->cart_contents as $value) {
				$post_id = array_key_exists('mptbm_id', $value) ? $value['mptbm_id'] : 0;
				if (get_post_type($post_id) == MPTBM_Function::get_cpt()) {
					$total_price = $value['mptbm_tp'];
					if (isset($_SESSION['geo_fence_post_' . $post_id])) {
						// Extract amount from session
						$session_data = $_SESSION['geo_fence_post_' . $post_id];
						// Check if session data contains the amount
						if (isset($session_data[0])) {
							// Add the amount to the price
							$total_price += (float)$session_data[0];
						}
					}
					$value['data']->set_price($total_price);
					$value['data']->set_regular_price($total_price);
					$value['data']->set_sale_price($total_price);
					$value['data']->set_sold_individually('yes');
					$value['data']->get_price();
				}
			}
		}
		public function cart_item_thumbnail($thumbnail, $cart_item)
		{
			$mptbm_id = array_key_exists('mptbm_id', $cart_item) ? $cart_item['mptbm_id'] : 0;
			if (get_post_type($mptbm_id) == MPTBM_Function::get_cpt()) {
				$thumbnail = '<div class="bg_image_area" data-href="' . get_the_permalink($mptbm_id) . '"><div data-bg-image="' . MP_Global_Function::get_image_url($mptbm_id) . '"></div></div>';
			}
			return $thumbnail;
		}
		public function get_item_data($item_data, $cart_item)
		{
			$post_id = array_key_exists('mptbm_id', $cart_item) ? $cart_item['mptbm_id'] : 0;
			if (get_post_type($post_id) == MPTBM_Function::get_cpt()) {
				ob_start();
				$this->show_cart_item($cart_item, $post_id);
				do_action('mptbm_show_cart_item', $cart_item, $post_id);
				$item_data[] = array('key' => esc_html__('Booking Details ', 'ecab-taxi-booking-manager'), 'value' => ob_get_clean());
			}
			return $item_data;
		}
		//**************//
		public function after_checkout_validation()
		{
			global $woocommerce;
			$items = $woocommerce->cart->get_cart();
			foreach ($items as $values) {
				$post_id = array_key_exists('mptbm_id', $values) ? $values['mptbm_id'] : 0;
				if (get_post_type($post_id) == MPTBM_Function::get_cpt()) {
					do_action('mptbm_validate_cart_item', $values, $post_id);
				}
			}
		}
		public function checkout_create_order_line_item($item, $cart_item_key, $values)
		{
			$post_id = array_key_exists('mptbm_id', $values) ? $values['mptbm_id'] : 0;
			if (get_post_type($post_id) == MPTBM_Function::get_cpt()) {
				$date = $values['mptbm_date'] ?? '';
				$start_location = $values['mptbm_start_place'] ?? '';
				$end_location = $values['mptbm_end_place'] ?? '';
				$distance = $values['mptbm_distance'] ?? '';
				$distance_text = $values['mptbm_distance_text'] ?? '';
				$duration = $values['mptbm_duration'] ?? '';
				$duration_text = $values['mptbm_duration_text'] ?? '';
				$base_price = $values['mptbm_base_price'] ?? '';
				$return = $values['mptbm_taxi_return'] ?? '';
				$waiting_time = $values['mptbm_waiting_time'] ?? '';
				$fixed_time = $values['mptbm_fixed_hours'] ?? 0;
				$extra_service = $values['mptbm_extra_service_info'] ?? [];
				$price = $values['mptbm_tp'] ?? '';
				$item->add_meta_data(esc_html__('Pickup Location ', 'ecab-taxi-booking-manager'), $start_location);
				$item->add_meta_data(esc_html__('Drop-Off Location ', 'ecab-taxi-booking-manager'), $end_location);
				$price_type = MP_Global_Function::get_post_info($post_id, 'mptbm_price_based');
				if ($price_type !== 'manual') {
					$item->add_meta_data(esc_html__('Approximate Distance ', 'ecab-taxi-booking-manager'), $distance_text);
					$item->add_meta_data(esc_html__('Approximate Time ', 'ecab-taxi-booking-manager'), $duration_text);
				}

				if ($waiting_time && $waiting_time > 0) {
					$item->add_meta_data(esc_html__('Extra Waiting Hours', 'ecab-taxi-booking-manager'), $waiting_time . ' ' . esc_html__('Hour ', 'ecab-taxi-booking-manager'));
				}
				if ($fixed_time && $fixed_time > 0) {
					$item->add_meta_data(esc_html__('Service Times', 'ecab-taxi-booking-manager'), $fixed_time . ' ' . esc_html__('Hour ', 'ecab-taxi-booking-manager'));
				}
				$item->add_meta_data(esc_html__('Date ', 'ecab-taxi-booking-manager'), esc_html(MP_Global_Function::date_format($date)));

				$item->add_meta_data(esc_html__('Time ', 'ecab-taxi-booking-manager'), esc_html(MP_Global_Function::date_format($date, 'time')));
				if ($return && $return > 1) {
					$item->add_meta_data(esc_html__('Transfer Type', 'ecab-taxi-booking-manager'), esc_html__('Return ', 'ecab-taxi-booking-manager'));
					if (MP_Global_Function::get_settings('mptbm_general_settings', 'enable_return_in_different_date') == 'yes') {
						$return_date = $values['mptbm_return_target_date'] ?? '';
						$return_time = $values['mptbm_return_target_time'] ?? '';

						if ($return_time !== "") {
							if ($return_time !== "0") {
								// Convert start time to hours and minutes
								list($hours, $decimal_part) = explode('.', $return_time);
								$interval_time = MPTBM_Function::get_general_settings('mptbm_pickup_interval_time');
								if ($interval_time == "5" || $interval_time == "15") {
									$minutes = isset($decimal_part) ? (int) $decimal_part * 1 : 0; // Multiply by 1 to convert to minutes
								} else {
									$minutes = isset($decimal_part) ? (int) $decimal_part * 10 : 0; // Multiply by 10 to convert to minutes
								}
							} else {
								$hours = 0;
								$minutes = 0;
							}
						} else {
							$hours = 0;
							$minutes = 0;
						}

						// Format hours and minutes
						$return_time_formatted = sprintf('%02d:%02d', $hours, $minutes);

						// Combine date and time if both are available
						$return_date_time = $return_date ? gmdate("Y-m-d", strtotime($return_date)) : "";
						if ($return_date_time && $return_time !== "") {
							$return_date_time .= " " . $return_time_formatted;
						}


						$item->add_meta_data(esc_html__('Return Date', 'ecab-taxi-booking-manager'), esc_html(MP_Global_Function::date_format($return_date_time)));
						$item->add_meta_data(esc_html__('Return Time', 'ecab-taxi-booking-manager'), esc_html(MP_Global_Function::date_format($return_date_time, 'time')));
						$item->add_meta_data('_mptbm_return_date', $return_date);
						$item->add_meta_data('_mptbm_return_time', $return_time);
					}
				}
				$item->add_meta_data(esc_html__('Price ', 'ecab-taxi-booking-manager'), wp_kses_post(wc_price($base_price)));
				if (sizeof($extra_service) > 0) {
					$item->add_meta_data(esc_html__('Optional Service ', 'ecab-taxi-booking-manager'), '');
					foreach ($extra_service as $service) {
						$item->add_meta_data(esc_html__('Services Name ', 'ecab-taxi-booking-manager'), $service['service_name']);
						$item->add_meta_data(esc_html__('Services Quantity ', 'ecab-taxi-booking-manager'), $service['service_quantity']);
						$item->add_meta_data(esc_html__('Price ', 'ecab-taxi-booking-manager'), esc_html(' ( ') . wp_kses_post(wc_price($service['service_price'])) . esc_html(' X ') . esc_html($service['service_quantity']) . esc_html(') = ') . wp_kses_post(wc_price($service['service_price'] * $service['service_quantity'])));
					}
				}
				$item->add_meta_data('_mptbm_id', $post_id);
				$item->add_meta_data('_mptbm_date', $date);
				$item->add_meta_data('_mptbm_start_place', $start_location);
				$item->add_meta_data('_mptbm_end_place', $end_location);
				$item->add_meta_data('_mptbm_taxi_return', $return);
				$item->add_meta_data('_mptbm_waiting_time', $waiting_time);
				$item->add_meta_data('_mptbm_fixed_hours', $fixed_time);
				$item->add_meta_data('_mptbm_distance', $distance);
				$item->add_meta_data('_mptbm_distance_text', $distance_text);
				$item->add_meta_data('_mptbm_duration', $duration);
				$item->add_meta_data('_mptbm_duration_text', $duration_text);
				$item->add_meta_data('_mptbm_base_price', $base_price);
				$item->add_meta_data('_mptbm_tp', $price);
				$item->add_meta_data('_mptbm_service_info', $extra_service);
				do_action('mptbm_checkout_create_order_line_item', $item, $values);
			}
		}
		public function checkout_order_processed($order_id)
		{

			if ($order_id) {

				$order = wc_get_order($order_id);


				$order_status = $order->get_status();
				$order_meta = get_post_meta($order_id);
				$payment_method = $order_meta['_payment_method_title'][0] ?? '';
				$user_id = $order_meta['_customer_user'][0] ?? '';
				if ($order_status != 'failed') {

					//$item_id = current( array_keys( $order->get_items() ) );
					foreach ($order->get_items() as $item_id => $item) {
						$post_id = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_id');
						if (get_post_type($post_id) == MPTBM_Function::get_cpt()) {
							$date = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_date');
							$date = $date ? MP_Global_Function::data_sanitize($date) : '';
							$start_place = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_start_place');
							$start_place = $start_place ? MP_Global_Function::data_sanitize($start_place) : '';
							$end_place = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_end_place');
							$end_place = $end_place ? MP_Global_Function::data_sanitize($end_place) : '';
							$waiting_time = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_waiting_time');
							$waiting_time = $waiting_time ? MP_Global_Function::data_sanitize($waiting_time) : '';
							$return = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_taxi_return');
							$return = $return ? MP_Global_Function::data_sanitize($return) : '';
							if ($return > 1 && MP_Global_Function::get_settings('mptbm_general_settings', 'enable_return_in_different_date') == 'yes') {
								$return_target_date = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_return_date');
								$return_target_time = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_return_time');
								$data['mptbm_return_target_date'] = $return_target_date;
								$data['mptbm_return_target_time'] = $return_target_time;
							}
							$fixed_time = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_fixed_hours');
							$fixed_time = $fixed_time ? MP_Global_Function::data_sanitize($fixed_time) : '';
							$distance = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_distance');
							$distance = $distance ? MP_Global_Function::data_sanitize($distance) : '';
							$duration = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_duration');
							$duration = $duration ? MP_Global_Function::data_sanitize($duration) : '';
							$base_price = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_base_price');
							$base_price = $base_price ? MP_Global_Function::data_sanitize($base_price) : '';
							$service = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_service_info');
							$service_info = $service ? MP_Global_Function::data_sanitize($service) : [];
							$price = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_tp');
							$price = $price ? MP_Global_Function::data_sanitize($price) : [];
							$data['mptbm_id'] = $post_id;
							$data['mptbm_date'] = $date;
							$data['mptbm_start_place'] = $start_place;
							$data['mptbm_end_place'] = $end_place;
							$data['mptbm_waiting_time'] = $waiting_time;
							$data['mptbm_taxi_return'] = $return;
							$data['mptbm_fixed_hours'] = $fixed_time;
							$data['mptbm_distance'] = $distance;
							$data['mptbm_duration'] = $duration;
							$data['mptbm_base_price'] = $base_price;
							$data['mptbm_order_id'] = $order_id;
							$data['mptbm_order_status'] = $order_status;
							$data['mptbm_payment_method'] = $order->get_payment_method_title();
							$data['mptbm_user_id'] = $user_id;
							$data['mptbm_tp'] = $price;
							$data['mptbm_service_info'] = $service_info;
							$data['mptbm_billing_name'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
							$data['mptbm_billing_email'] = $order->get_billing_email();
							$data['mptbm_billing_phone'] = $order->get_billing_phone();
							$data['mptbm_target_pickup_interval_time'] =  MPTBM_Function::get_general_settings('mptbm_pickup_interval_time', '30');
							// $data['mptbm_billing_address'] = $order->get_billing_address();
							$booking_data = apply_filters('add_mptbm_booking_data', $data, $post_id);
							self::add_cpt_data('mptbm_booking', $booking_data['mptbm_billing_name'], $booking_data);
							if (sizeof($service_info) > 0) {
								foreach ($service_info as $service) {
									$ex_data['mptbm_id'] = $post_id;
									$ex_data['mptbm_date'] = $date;
									$ex_data['mptbm_order_id'] = $order_id;
									$ex_data['mptbm_order_status'] = $order_status;
									$ex_data['mptbm_service_name'] = $service['service_name'];
									$ex_data['mptbm_service_quantity'] = $service['service_quantity'];
									$ex_data['mptbm_service_price'] = $service['service_price'];
									$ex_data['mptbm_payment_method'] = $payment_method;
									$ex_data['mptbm_user_id'] = $user_id;
									self::add_cpt_data('mptbm_service_booking', '#' . $order_id . $ex_data['mptbm_service_name'], $ex_data);
								}
							}
						}
					}
				}
			}
		}
		public function order_status_changed($order_id)
		{
			$order = wc_get_order($order_id);
			$order_status = $order->get_status();
			foreach ($order->get_items() as $item_id => $item_values) {
				$post_id = MP_Global_Function::get_order_item_meta($item_id, '_mptbm_id');
				if (get_post_type($post_id) == MPTBM_Function::get_cpt()) {
					if ($order->has_status('processing') || $order->has_status('pending') || $order->has_status('on-hold') || $order->has_status('completed') || $order->has_status('cancelled') || $order->has_status('refunded') || $order->has_status('failed') || $order->has_status('requested')) {
						$this->wc_order_status_change($order_status, $post_id, $order_id);
					}
				}
			}
		}
		//**************************//
		public function show_cart_item($cart_item, $post_id)
		{
			$date = array_key_exists('mptbm_date', $cart_item) ? $cart_item['mptbm_date'] : '';

			$start_location = array_key_exists('mptbm_start_place', $cart_item) ? $cart_item['mptbm_start_place'] : '';
			$end_location = array_key_exists('mptbm_end_place', $cart_item) ? $cart_item['mptbm_end_place'] : '';
			$base_price = array_key_exists('mptbm_base_price', $cart_item) ? $cart_item['mptbm_base_price'] : '';
			$return = array_key_exists('mptbm_taxi_return', $cart_item) ? $cart_item['mptbm_taxi_return'] : '';
			$waiting_time = array_key_exists('mptbm_waiting_time', $cart_item) ? $cart_item['mptbm_waiting_time'] : '';
			$fixed_time = array_key_exists('mptbm_fixed_hours', $cart_item) ? $cart_item['mptbm_fixed_hours'] : '';
			$extra_service = array_key_exists('mptbm_extra_service_info', $cart_item) ? $cart_item['mptbm_extra_service_info'] : [];
?>
			<div class="mpStyle">
				<?php do_action('mptbm_before_cart_item_display', $cart_item, $post_id); ?>
				<div class="dLayout_xs">
					<ul class="cart_list">
						<li>
							<span class="fas fa-map-marker-alt"></span>
							<h6 class="_mR_xs"><?php esc_html_e('Pickup Location', 'ecab-taxi-booking-manager'); ?> :</h6>
							<span><?php echo esc_html($start_location); ?></span>
						</li>
						<li>
							<span class="fas fa-map-marker-alt"></span>
							<h6 class="_mR_xs"><?php esc_html_e('Drop-Off Location', 'ecab-taxi-booking-manager'); ?> :</h6>
							<span><?php echo esc_html($end_location); ?></span>
						</li>
						<?php
						$price_type = MP_Global_Function::get_post_info($post_id, 'mptbm_price_based');
						if ($price_type !== 'manual') {
						?>
							<li>
								<span class="fas fa-route"></span>
								<h6 class="_mR_xs"><?php esc_html_e('Approximate Distance', 'ecab-taxi-booking-manager'); ?> :</h6>
								<span><?php echo esc_html($cart_item['mptbm_distance_text']); ?></span>
							</li>
							<li>
								<span class="far fa-clock"></span>
								<h6 class="_mR_xs"><?php esc_html_e('Approximate Time', 'ecab-taxi-booking-manager'); ?> :</h6>
								<span><?php echo esc_html($cart_item['mptbm_duration_text']); ?></span>
							</li>
						<?php } ?>
						<li>
							<span class="far fa-calendar-alt"></span>
							<h6 class="_mR_xs"><?php esc_html_e('Date', 'ecab-taxi-booking-manager'); ?> :</h6>
							<span><?php echo esc_html(MP_Global_Function::date_format($date)); ?></span>
						</li>
						<li>
							<span class="far fa-clock"></span>
							<h6 class="_mR_xs"><?php esc_html_e('Time : ', 'ecab-taxi-booking-manager'); ?></h6>
							<span><?php echo esc_html(MP_Global_Function::date_format($date, 'time')); ?></span>
						</li>
						<?php if ($return && $return > 1) { ?>
							<li>
								<h6 class="_mR_xs"><?php esc_html_e('Transfer Type', 'ecab-taxi-booking-manager'); ?> :</h6>
								<span><?php esc_html_e('Return', 'ecab-taxi-booking-manager'); ?></span>
							</li>

							<?php if (MP_Global_Function::get_settings('mptbm_general_settings', 'enable_return_in_different_date') == 'yes') {

								$return_date = array_key_exists('mptbm_return_target_date', $cart_item) ? $cart_item['mptbm_return_target_date'] : '';
								$return_time = array_key_exists('mptbm_return_target_time', $cart_item) ? $cart_item['mptbm_return_target_time'] : '';
								if ($return_time !== "") {
									if ($return_time !== "0") {
										// Convert start time to hours and minutes
										list($hours, $decimal_part) = explode('.', $return_time);
										$interval_time = MPTBM_Function::get_general_settings('mptbm_pickup_interval_time');
										if ($interval_time == "5" || $interval_time == "15") {
											$minutes = isset($decimal_part) ? (int) $decimal_part * 1 : 0; // Multiply by 1 to convert to minutes
										} else {
											$minutes = isset($decimal_part) ? (int) $decimal_part * 10 : 0; // Multiply by 10 to convert to minutes
										}
									} else {
										$hours = 0;
										$minutes = 0;
									}
								} else {
									$hours = 0;
									$minutes = 0;
								}

								// Format hours and minutes
								$return_time_formatted = sprintf('%02d:%02d', $hours, $minutes);

								// Combine date and time if both are available
								$return_date_time = $return_date ? gmdate("Y-m-d", strtotime($return_date)) : "";
								if ($return_date_time && $return_time !== "") {
									$return_date_time .= " " . $return_time_formatted;
								}



							?>
								<li>
									<span class="far fa-calendar-alt"></span>
									<h6 class="_mR_xs"><?php esc_html_e('Return Date', 'ecab-taxi-booking-manager'); ?> :</h6>
									<span><?php echo esc_html(MP_Global_Function::date_format($return_date_time)); ?></span>
								</li>
								<li>
									<span class="far fa-clock"></span>
									<h6 class="_mR_xs"><?php esc_html_e('Return Time', 'ecab-taxi-booking-manager'); ?> :</h6>
									<span><?php echo esc_html(MP_Global_Function::date_format($return_date_time, 'time')); ?></span>
								</li>
							<?php } ?>
						<?php } ?>
						<?php if ($waiting_time && $waiting_time > 0) { ?>
							<li>
								<h6 class="_mR_xs"><?php esc_html_e('Extra Waiting Hours', 'ecab-taxi-booking-manager'); ?> :</h6>
								<span><?php echo esc_html($waiting_time); ?><?php esc_html_e('Hours', 'ecab-taxi-booking-manager'); ?></span>
							</li>
						<?php } ?>
						<?php if ($fixed_time && $fixed_time > 0) { ?>
							<li>
								<h6 class="_mR_xs"><?php esc_html_e('Service Times', 'ecab-taxi-booking-manager'); ?> :</h6>
								<span><?php echo esc_html($fixed_time); ?><?php esc_html_e('Hours', 'ecab-taxi-booking-manager'); ?></span>
							</li>
						<?php } ?>

						<li>
							<span class="fa fa-tag"></span>
							<h6 class="_mR_xs"><?php esc_html_e('Base Price : ', 'ecab-taxi-booking-manager'); ?></h6>
							<span><?php echo wp_kses_post(wc_price($base_price)); ?></span>
						</li>
					</ul>
				</div>
				<?php if (sizeof($extra_service) > 0) { ?>
					<h5 class="_mB_xs"><?php esc_html_e('Extra Services', 'ecab-taxi-booking-manager'); ?></h5>
					<?php foreach ($extra_service as $service) { ?>
						<div class="dLayout_xs">
							<ul class="cart_list">
								<li>
									<h6 class="_mR_xs"><?php esc_html_e('Name : ', 'ecab-taxi-booking-manager'); ?></h6>
									<span><?php echo esc_html($service['service_name']); ?></span>
								</li>
								<li>
									<h6 class="_mR_xs"><?php esc_html_e('Quantity : ', 'ecab-taxi-booking-manager'); ?></h6>
									<span><?php echo esc_html($service['service_quantity']); ?></span>
								</li>
								<li>
									<h6 class="_mR_xs"><?php esc_html_e('Price : ', 'ecab-taxi-booking-manager'); ?></h6>
									<span><?php echo esc_html(' ( ') . wp_kses_post(wc_price($service['service_price'])) . esc_html(' X ') . esc_html($service['service_quantity']) . esc_html(' ) =') . wp_kses_post(wc_price($service['service_price'] * $service['service_quantity'])); ?></span>
								</li>
							</ul>
						</div>
					<?php } ?>
				<?php } ?>
				<?php do_action('mptbm_after_cart_item_display', $cart_item, $post_id); ?>
			</div>
			<?php
		}
		public function wc_order_status_change($order_status, $post_id, $order_id)
		{
			$args = array(
				'post_type' => 'mptbm_booking',
				'posts_per_page' => -1,
				'meta_query' => array(
					'relation' => 'AND',
					array(
						array(
							'key' => 'mptbm_id',
							'value' => $post_id,
							'compare' => '='
						),
						array(
							'key' => 'mptbm_order_id',
							'value' => $order_id,
							'compare' => '='
						)
					)
				)
			);
			$loop = new WP_Query($args);
			foreach ($loop->posts as $user) {
				$user_id = $user->ID;
				update_post_meta($user_id, 'mptbm_order_status', $order_status);
			}
			$args = array(
				'post_type' => 'mptbm_service_booking',
				'posts_per_page' => -1,
				'meta_query' => array(
					'relation' => 'AND',
					array(
						array(
							'key' => 'mptbm_id',
							'value' => $post_id,
							'compare' => '='
						),
						array(
							'key' => 'mptbm_order_id',
							'value' => $order_id,
							'compare' => '='
						)
					)
				)
			);
			$loop = new WP_Query($args);
			foreach ($loop->posts as $user) {
				$user_id = $user->ID;
				update_post_meta($user_id, 'mptbm_order_status', $order_status);
			}
		}
		//**********************//
		public static function cart_extra_service_info($post_id): array
		{
			$start_date = isset($_POST['mptbm_date']) ? sanitize_text_field($_POST['mptbm_date']) : '';
			$service_name = isset($_POST['mptbm_extra_service']) ? array_map('sanitize_text_field', $_POST['mptbm_extra_service']) : [];
			$service_quantity = isset($_POST['mptbm_extra_service_qty']) ? array_map('sanitize_text_field', $_POST['mptbm_extra_service_qty']) : [];
			$extra_service = array();
			if (sizeof($service_name) > 0) {
				for ($i = 0; $i < count($service_name); $i++) {
					if ($service_name[$i] && $service_quantity[$i] > 0) {
						$price = MPTBM_Function::get_extra_service_price_by_name($post_id, $service_name[$i]);
						$wc_price = MP_Global_Function::wc_price($post_id, $price);
						$raw_price = MP_Global_Function::price_convert_raw($wc_price);
						$extra_service[$i]['service_name'] = $service_name[$i];
						$extra_service[$i]['service_quantity'] = $service_quantity[$i];
						$extra_service[$i]['service_price'] = $raw_price;
						$extra_service[$i]['mptbm_date'] = $start_date ?? '';
					}
				}
			}
			return $extra_service;
		}
		public function get_cart_total_price($post_id)
		{
			$distance = isset($_COOKIE['mptbm_distance']) ? absint($_COOKIE['mptbm_distance']) : '';
			$duration = isset($_COOKIE['mptbm_duration']) ? absint($_COOKIE['mptbm_duration']) : '';
			$start_place = isset($_POST['mptbm_start_place']) ? sanitize_text_field($_POST['mptbm_start_place']) : '';
			$end_place = isset($_POST['mptbm_end_place']) ? sanitize_text_field($_POST['mptbm_end_place']) : '';
			$waiting_time = isset($_POST['mptbm_waiting_time']) ? sanitize_text_field($_POST['mptbm_waiting_time']) : 0;
			$return = isset($_POST['mptbm_taxi_return']) ? sanitize_text_field($_POST['mptbm_taxi_return']) : 1;
			$fixed_hour = isset($_POST['mptbm_fixed_hours']) ? sanitize_text_field($_POST['mptbm_fixed_hours']) : 0;
			$price = MPTBM_Function::get_price($post_id, $distance, $duration, $start_place, $end_place, $waiting_time, $return, $fixed_hour);
			$wc_price = MP_Global_Function::wc_price($post_id, $price);
			$raw_price = MP_Global_Function::price_convert_raw($wc_price);
			$service_name = isset($_POST['mptbm_extra_service']) ? array_map('sanitize_text_field', $_POST['mptbm_extra_service']) : [];
			$service_quantity = isset($_POST['mptbm_extra_service_qty']) ? array_map('absint', $_POST['mptbm_extra_service_qty']) : [];
			if (sizeof($service_name) > 0) {
				for ($i = 0; $i < count($service_name); $i++) {
					if ($service_name[$i]) {
						if (array_key_exists($i, $service_quantity) && isset($service_quantity[$i])) {
							$raw_price = $raw_price + MPTBM_Function::get_extra_service_price_by_name($post_id, $service_name[$i]) * $service_quantity[$i];
						} else {
							$raw_price = $raw_price + MPTBM_Function::get_extra_service_price_by_name($post_id, $service_name[$i]);
						}
					}
				}
			}
			$wc_price = MP_Global_Function::wc_price($post_id, $raw_price);
			return MP_Global_Function::price_convert_raw($wc_price);
		}
		public static function add_cpt_data($cpt_name, $title, $meta_data = array(), $status = 'publish', $cat = array())
		{
			$new_post = array(
				'post_title' => $title,
				'post_content' => '',
				'post_category' => $cat,
				'tags_input' => array(),
				'post_status' => $status,
				'post_type' => $cpt_name
			);
			$post_id = wp_insert_post($new_post);
			if (sizeof($meta_data) > 0) {
				foreach ($meta_data as $key => $value) {
					update_post_meta($post_id, $key, $value);
				}
			}
			if ($cpt_name == 'mptbm_booking') {
				$mptbm_pin = $meta_data['mptbm_user_id'] . $meta_data['mptbm_order_id'] . $meta_data['mptbm_id'] . $post_id;
				update_post_meta($post_id, 'mptbm_pin', $mptbm_pin);
			}
		}
		/****************************/
		public function mptbm_add_to_cart() {
			$link_id = absint($_POST['link_id']);
			$product_id = apply_filters('woocommerce_add_to_cart_product_id', $link_id);
			$quantity = 1;
			$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
			$product_status = get_post_status($product_id);
			WC()->cart->empty_cart();
			ob_start();
			if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity) && 'publish' === $product_status) {
				$checkout_system = MP_Global_Function::get_settings('mptbm_general_settings', 'single_page_checkout', 'no');
				if ($checkout_system == 'yes') {
					echo wc_get_checkout_url();
				} else {
					?>
                    <div class="dLayout woocommerce-page">
						<?php echo do_shortcode('[woocommerce_checkout]'); ?>
						<?php //do_action('woocommerce_ajax_checkout');
						?>
                    </div>
                    <div class="divider"></div>
                    <div class="justifyBetween">
                        <button type="button" class="_themeButton_min_200 mptbm_summary_prev">
                            <span>&larr; &nbsp;<?php esc_html_e('Previous', 'ecab-taxi-booking-manager'); ?></span>
                        </button>
                        <div></div>
                    </div>
					<?php
				}
			}
			echo ob_get_clean();
			die();
		}
	}
	new MPTBM_Woocommerce();
}
