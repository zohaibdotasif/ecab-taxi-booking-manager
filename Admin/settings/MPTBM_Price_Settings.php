<?php
/*
   * @Author 		engr.sumonazma@gmail.com
   * Copyright: 	mage-people.com
   */
if (!defined('ABSPATH')) {
	die;
} // Cannot access pages directly.
if (!class_exists('MPTBM_Price_Settings')) {
	class MPTBM_Price_Settings
	{
		public function __construct()
		{
			add_action('add_mptbm_settings_tab_content', [$this, 'price_settings'], 10, 1);
			add_action('save_post', [$this, 'save_price_settings'], 10, 1);
		}
		public function price_settings($post_id)
		{
			$initial_price = MP_Global_Function::get_post_info($post_id, 'mptbm_initial_price');
			$display_map = MP_Global_Function::get_settings('mptbm_map_api_settings', 'display_map', 'enable');
			$price_based = MP_Global_Function::get_post_info($post_id, 'mptbm_price_based');
			$price_based = $display_map == 'disable' ? 'manual' : $price_based;
			$distance_price = MP_Global_Function::get_post_info($post_id, 'mptbm_km_price');
			$time_price = MP_Global_Function::get_post_info($post_id, 'mptbm_hour_price');
			$manual_prices = MP_Global_Function::get_post_info($post_id, 'mptbm_manual_price_info', []);
			$terms_location_prices = MP_Global_Function::get_post_info($post_id, 'mptbm_terms_price_info', []);
			$location_terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false));
			$waiting_time_check = MPTBM_Function::get_general_settings('taxi_waiting_time', 'enable');
			$waiting_price = MP_Global_Function::get_post_info($post_id, 'mptbm_waiting_price');
			$distance_selected = $price_based == 'distance' ? 'selected' : '';
			$distance_selected = $display_map == 'disable' ? 'disabled' : $distance_selected;
			
			$duration_selected = $price_based == 'duration' ? 'selected' : '';
			$duration_selected = $display_map == 'disable' ? 'disabled' : $duration_selected;
			$distance_duration_selected = $price_based == 'distance_duration' ? 'selected' : '';
			$distance_duration_selected = $display_map == 'disable' ? 'disabled' : $distance_duration_selected;
			$fixed_hourly_selected = $price_based == 'fixed_hourly' ? 'selected' : '';
			$fixed_hourly_selected = $display_map == 'disable' ? 'disabled' : $fixed_hourly_selected;
			$gm_api_url = admin_url('edit.php?post_type=mptbm_rent&page=mptbm_settings_page');

?>
			<div class="tabsItem" data-tabs="#mptbm_settings_pricing">
				<h2><?php esc_html_e('Price Settings', 'ecab-taxi-booking-manager'); ?></h2>
				<p><?php esc_html_e('Price Settings', 'ecab-taxi-booking-manager'); ?></p>

				<section class="bg-light">
					<div>
						<label><?php esc_html_e('Price Settings', 'ecab-taxi-booking-manager'); ?></label>
						<span><?php esc_html_e('Price Settings', 'ecab-taxi-booking-manager'); ?></span>
					</div>
				</section>
				<section>
					<div>
						<label><?php esc_html_e('Initial Price', 'ecab-taxi-booking-manager'); ?></label>
						<span><?php MPTBM_Settings::info_text('mptbm_initial_price'); ?></span>
					</div>
					<input class="formControl mp_price_validation" name="mptbm_initial_price" value="<?php echo esc_attr($initial_price); ?>" type="text" placeholder="<?php esc_html_e('EX:10', 'ecab-taxi-booking-manager'); ?>" />
				</section>
				<?php if ($waiting_time_check == 'enable') { ?>
					<section class="<?php echo esc_attr($price_based == 'duration' || $price_based == 'distance' || $price_based == 'distance_duration' || $price_based == 'manual' ? 'mActive' : ''); ?>">
						<div >
							<label for=""><?php esc_html_e('Waiting Time Price/Hour', 'ecab-taxi-booking-manager'); ?></label>
							<span><?php MPTBM_Settings::info_text('mptbm_waiting_price'); ?></span>
						</div>
						<input class="formControl mp_price_validation" name="mptbm_waiting_price" value="<?php echo esc_attr($waiting_price); ?>" type="text" placeholder="<?php esc_html_e('EX:10', 'ecab-taxi-booking-manager'); ?>" />
					</section>
				<?php } ?>
				<section>
					<div>
						<label for=""><?php esc_html_e('Pricing based on', 'ecab-taxi-booking-manager'); ?></label>
						<span><?php MPTBM_Settings::info_text('mptbm_price_based'); ?></span>
					</div>
					<div>
						<select class="formControl" name="mptbm_price_based" data-collapse-target>
							<option disabled><?php esc_html_e('Please select ...', 'ecab-taxi-booking-manager'); ?></option>
							<option value="distance" data-option-target data-option-target-multi="#mp_distance" <?php echo esc_attr($distance_selected); ?>><?php esc_html_e('Distance as google map', 'ecab-taxi-booking-manager'); ?></option>
							<option value="duration" data-option-target data-option-target-multi="#mp_duration" <?php echo esc_attr($duration_selected); ?>><?php esc_html_e('Duration/Time as google map', 'ecab-taxi-booking-manager'); ?></option>
							<option value="distance_duration" data-option-target data-option-target-multi="#mp_distance #mp_duration" <?php echo esc_attr($distance_duration_selected); ?>><?php esc_html_e('Distance + Duration as google map', 'ecab-taxi-booking-manager'); ?></option>
							<option value="manual" data-option-target data-option-target-multi="#mp_manual" <?php echo esc_attr($price_based == 'manual' ? 'selected' : ''); ?>><?php esc_html_e('Manual as fixed Location', 'ecab-taxi-booking-manager'); ?></option>
							<option value="fixed_hourly" data-option-target="#mp_duration" <?php echo esc_attr($fixed_hourly_selected); ?>><?php esc_html_e('Fixed Hourly', 'ecab-taxi-booking-manager'); ?></option>
						</select>
						<?php if ($display_map == 'disable') { ?>
							<p><?php esc_html_e('To enable google map pricing option you must enable  ', 'ecab-taxi-booking-manager'); ?><a href="<?php echo esc_attr($gm_api_url); ?>"><?php esc_html_e('google map base pricing option', 'ecab-taxi-booking-manager'); ?></a></p>
						<?php } ?>
					</div>
				</section>
				<section data-collapse="#mp_distance" class="<?php echo esc_attr($price_based == 'distance' || $price_based == 'distance_duration' ? 'mActive' : ''); ?>">
					<div>
						<label for=""><?php esc_html_e('Price/KM', 'ecab-taxi-booking-manager'); ?></label>
						<span><?php MPTBM_Settings::info_text('mptbm_km_price'); ?></span>
					</div>
					<input class="formControl mp_price_validation" name="mptbm_km_price" value="<?php echo esc_attr($distance_price); ?>" type="text" placeholder="<?php esc_html_e('EX:10', 'ecab-taxi-booking-manager'); ?>" />
				</section>
				<section data-collapse="#mp_duration" class="<?php echo esc_attr($price_based == 'duration' || $price_based == 'distance_duration' || $price_based == 'fixed_hourly' ? 'mActive' : ''); ?>">
					<div >
						<label for=""><?php esc_html_e('Price/Hour', 'ecab-taxi-booking-manager'); ?></label>
						<span><?php MPTBM_Settings::info_text('mptbm_hour_price'); ?></span>
					</div>
					<input class="formControl mp_price_validation" name="mptbm_hour_price" value="<?php echo esc_attr($time_price); ?>" type="text" placeholder="<?php esc_html_e('EX:10', 'ecab-taxi-booking-manager'); ?>" />
				</section>
				
				<!-- Manual price -->
				<section class="bg-light" style="margin-top: 20px;" data-collapse="#mp_manual">
					<div>
						<label><?php esc_html_e('Manual Price Settings', 'ecab-taxi-booking-manager'); ?></label>
						<span><?php esc_html_e('Manual Price Settings', 'ecab-taxi-booking-manager'); ?></span>
					</div>
				</section>
				<section class="<?php echo esc_attr($price_based == 'manual' ? 'mActive' : ''); ?>" data-collapse="#mp_manual">
					<div class="mp_settings_area">
						<table>
							<thead>
								<tr>
									<th><?php esc_html_e('Start Location', 'ecab-taxi-booking-manager'); ?><span class="textRequired">&nbsp;*</span></th>
									<th><?php esc_html_e('End Location', 'ecab-taxi-booking-manager'); ?><span class="textRequired">&nbsp;*</span></th>
									<th><?php esc_html_e('Price', 'ecab-taxi-booking-manager'); ?><span class="textRequired">&nbsp;*</span></th>
									<th class="_w_100"><?php esc_html_e('Action', 'ecab-taxi-booking-manager'); ?></th>
								</tr>
							</thead>
							<tbody class="mp_sortable_area mp_item_insert">
								<?php
								if (sizeof($manual_prices) > 0) {
									foreach ($manual_prices as $manual_price) {
										$this->manual_price_item($manual_price);
									}
								}
								if (sizeof($location_terms) > 0) {
									$this->location_terms_price_item($location_terms, $terms_location_prices);
								}
								?>
								<?php
								?>
							</tbody>
						</table>
						<div class="my-2"></div>
						<?php MP_Custom_Layout::add_new_button(esc_html__('Add New Price', 'ecab-taxi-booking-manager')); ?>
						<?php $this->hidden_manual_price_item($location_terms); ?>
					</div>
				</section>
				
			</div>
		<?php
		}
		public function hidden_manual_price_item($location_terms)
		{
		?>
			<div class="mp_hidden_content">
				<table>
					<tbody class="mp_hidden_item">
						<?php $this->location_terms_add_price_item($location_terms); ?>
					</tbody>
				</table>
			</div>
		<?php
		}
		public function manual_price_item($manual_price = array())
		{

			$manual_price = $manual_price && is_array($manual_price) ? $manual_price : array();
			$start_location = array_key_exists('start_location', $manual_price) ? $manual_price['start_location'] : '';
			$end_location = array_key_exists('end_location', $manual_price) ? $manual_price['end_location'] : '';
			$price = array_key_exists('price', $manual_price) ? $manual_price['price'] : '';
		?>
			<tr class="mp_remove_area">
				<td>
					<label>
						<input type="text" name="mptbm_manual_start_location[]" class="formControl mp_name_validation" value="<?php echo esc_attr($start_location); ?>" placeholder="<?php esc_attr_e('EX:Dhaka', 'ecab-taxi-booking-manager'); ?>" />
					</label>
				</td>
				<td>
					<label>
						<input type="text" name="mptbm_manual_end_location[]" class="formControl mp_name_validation" value="<?php echo esc_attr($end_location); ?>" placeholder="<?php esc_attr_e('EX:Dhaka', 'ecab-taxi-booking-manager'); ?>" />
					</label>
				</td>
				<td>
					<label>
						<input type="text" name="mptbm_manual_price[]" class="formControl mp_price_validation" value="<?php echo esc_attr($price); ?>" placeholder="<?php esc_attr_e('EX:10 ', 'ecab-taxi-booking-manager'); ?>" />
					</label>
				</td>
				<td>
					<?php MP_Custom_Layout::move_remove_button(); ?>
				</td>
			</tr>
			<?php
		}
		public function location_terms_price_item($location_terms = array(), $terms_location_prices = array())
		{

			foreach ($terms_location_prices as $terms_location_price) {
				$start_location = $terms_location_price['start_location'];
				$end_location = $terms_location_price['end_location'];
				$terms_price = $terms_location_price['price'];
			?>


				<tr class="mp_remove_area">
					<td>
						<label>
							<select name="mptbm_terms_start_location[]" class="formControl mp_name_validation">
								<option value="">Select Start Location</option>
								<?php
								foreach ($location_terms as $term) {
									if ($start_location == $term->slug) {
										$selected = 'selected';
									} else {
										$selected = '';
									}
								?>
									<option value="<?php echo esc_attr($term->slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($term->name); ?></option>
								<?php } ?>
							</select>
						</label>
					</td>

					<td>
						<label>
							<select name="mptbm_terms_end_location[]" class="formControl mp_name_validation">
								<option value="">Select End Location</option>
								<?php foreach ($location_terms as $term) : ?>
									<?php
									$selected = ($end_location == $term->slug) ? 'selected' : '';
									?>
									<option value="<?php echo esc_attr($term->slug); ?>" <?php echo  esc_attr($selected); ?>><?php echo esc_html($term->name); ?></option>
								<?php endforeach; ?>
							</select>

						</label>
					</td>

					<td>
						<label>
							<input type="text" name="mptbm_location_terms_price[]" class="formControl mp_price_validation" value="<?php echo esc_attr($terms_price); ?>" placeholder="<?php esc_attr_e('EX:10 ', 'ecab-taxi-booking-manager'); ?>" />
						</label>
					</td>

					<td>
						<?php MP_Custom_Layout::move_remove_button(); ?>
					</td>
				</tr>
			<?php
			}
		}
		public function location_terms_add_price_item($location_terms = array())
		{
			?>


			<tr class="mp_remove_area">
				<td>
					<label>
						<select name="mptbm_terms_start_location[]" class="formControl mp_name_validation">
							<option value="">Select Start Location</option>
							
							<?php foreach ($location_terms as $term) : ?>
								
								<?php
								
								// $selected = ($start_location == $term->slug) ? 'selected' : '';
								?>
								<option value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</td>

				<td>
					<label>
						<select name="mptbm_terms_end_location[]" class="formControl mp_name_validation">
							<option value="">Select End Location</option>
							<?php foreach ($location_terms as $term) : ?>
								<option value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></option>
							<?php endforeach; ?>
						</select>

					</label>
				</td>

				<td>
					<label>
						<input type="text" name="mptbm_location_terms_price[]" class="formControl mp_price_validation" value="" placeholder="<?php esc_attr_e('EX:10 ', 'ecab-taxi-booking-manager'); ?>" />
					</label>
				</td>

				<td>
					<?php MP_Custom_Layout::move_remove_button(); ?>
				</td>
			</tr>
<?php

		}
		public function save_price_settings($post_id)
		{
			if (!isset($_POST['mptbm_transportation_type_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mptbm_transportation_type_nonce'])), 'mptbm_transportation_type_nonce') && defined('DOING_AUTOSAVE') && DOING_AUTOSAVE && !current_user_can('edit_post', $post_id)) {
				return;
			}
			if (get_post_type($post_id) == MPTBM_Function::get_cpt()) {
				$initial_price = isset($_POST['mptbm_initial_price']) ? sanitize_text_field($_POST['mptbm_initial_price']) : '';
				update_post_meta($post_id, 'mptbm_initial_price', $initial_price);
				$price_based = isset($_POST['mptbm_price_based']) ? sanitize_text_field($_POST['mptbm_price_based']) : '';
				update_post_meta($post_id, 'mptbm_price_based', $price_based);
				$distance_price = isset($_POST['mptbm_km_price']) ? sanitize_text_field($_POST['mptbm_km_price']) : 0;
				update_post_meta($post_id, 'mptbm_km_price', $distance_price);
				$hour_price = isset($_POST['mptbm_hour_price']) ? sanitize_text_field($_POST['mptbm_hour_price']) : 0;
				update_post_meta($post_id, 'mptbm_hour_price', $hour_price);
				$manual_price_infos = array();
				$start_location = isset($_POST['mptbm_manual_start_location']) ? array_map('sanitize_text_field', $_POST['mptbm_manual_start_location']) : [];
				$end_location = isset($_POST['mptbm_manual_end_location']) ? array_map('sanitize_text_field', $_POST['mptbm_manual_end_location']) : [];
				$manual_price = isset($_POST['mptbm_manual_price']) ? array_map('sanitize_text_field', $_POST['mptbm_manual_price']) : [];

				if (sizeof($start_location) > 1 && sizeof($end_location) > 1 && sizeof($manual_price) > 0) {
					$count = 0;
					foreach ($start_location as $key => $location) {
						if ($location && $end_location[$key] && $manual_price[$key]) {
							$manual_price_infos[$count]['start_location'] = $location;
							$manual_price_infos[$count]['end_location'] = $end_location[$key];
							$manual_price_infos[$count]['price'] = $manual_price[$key];
							$count++;
						}
					}
				}

				update_post_meta($post_id, 'mptbm_manual_price_info', $manual_price_infos);
				$terms_price_infos = array();
				$start_terms_location = isset($_POST['mptbm_terms_start_location']) ? array_map('sanitize_text_field', $_POST['mptbm_terms_start_location']) : [];
				$end_terms_location = isset($_POST['mptbm_terms_end_location']) ? array_map('sanitize_text_field', $_POST['mptbm_terms_end_location']) : [];
				$terms_price = isset($_POST['mptbm_location_terms_price']) ? array_map('sanitize_text_field', $_POST['mptbm_location_terms_price']) : [];
				if (sizeof($start_terms_location) > 1 && sizeof($end_terms_location) > 1 && sizeof($terms_price) > 0) {
					$count = 0;
					foreach ($start_terms_location as $key => $location) {
						if ($location && $end_terms_location[$key] && $terms_price[$key]) {
							$terms_price_infos[$count]['start_location'] = $location;
							$terms_price_infos[$count]['end_location'] = $end_terms_location[$key];
							$terms_price_infos[$count]['price'] = $terms_price[$key];
							$count++;

						}
					}
				}
				update_post_meta($post_id, 'mptbm_terms_price_info', $terms_price_infos);
				$waiting_price = isset($_POST['mptbm_waiting_price']) ? sanitize_text_field($_POST['mptbm_waiting_price']) : '';
				update_post_meta($post_id, 'mptbm_waiting_price', $waiting_price);
			}
		}
	}
	new MPTBM_Price_Settings();
}
