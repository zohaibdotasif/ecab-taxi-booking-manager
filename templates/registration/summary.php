<?php
	/*
 * @Author 		engr.sumonazma@gmail.com
 * Copyright: 	mage-people.com
 */
	if (!defined('ABSPATH')) {
		die;
	} // Cannot access pages directly
	$distance = $distance ?? (isset($_COOKIE['mptbm_distance']) ?absint($_COOKIE['mptbm_distance']): '');
	$duration = $duration ?? (isset($_COOKIE['mptbm_duration']) ?absint($_COOKIE['mptbm_duration']): '');
	$label = $label ?? MPTBM_Function::get_name();
	$date = $date ?? '';
	$start_place = $start_place ?? '';
	$end_place = $end_place ?? '';
	$two_way = $two_way ?? 1;
	$waiting_time = $waiting_time ?? 0;
	$fixed_time = $fixed_time ?? '';
	$return_date_time = $return_date_time ?? '';
	$price_based = $price_based ?? '';
	
?>
	<div class="leftSidebar">
		<div class="mp_sticky_area">
			<div class="mp_sticky_on_scroll">
				<div class="_dLayout_dFlex_fdColumn_btLight_2">
					<h3><?php esc_html_e('SUMMARY', 'ecab-taxi-booking-manager'); ?></h3>
					<div class="dividerL"></div>

					<h6 class="_mB_xs"><?php esc_html_e('Pickup Date', 'ecab-taxi-booking-manager'); ?></h6>
					<p class="_textLight_1"><?php echo esc_html(MP_Global_Function::date_format($date)); ?></p>
					<div class="dividerL"></div>
					<h6 class="_mB_xs"><?php esc_html_e('Pickup Time', 'ecab-taxi-booking-manager'); ?></h6>
					<p class="_textLight_1"><?php echo esc_html(MP_Global_Function::date_format($date, 'time')); ?></p>
					<div class="dividerL"></div>
					<h6 class="_mB_xs"><?php esc_html_e('Pickup Location', 'ecab-taxi-booking-manager'); ?></h6>
					<?php if($price_based == 'manual'){ ?>
						<p class="_textLight_1 mptbm_manual_start_place"><?php echo esc_html(MPTBM_Function::get_taxonomy_name_by_slug( $start_place,'locations' )); ?></p>
					<?php }else{ ?>
						<p class="_textLight_1 mptbm_manual_start_place"><?php echo esc_html($start_place); ?></p>
					<?php } ?>
					<div class="dividerL"></div>
					<h6 class="_mB_xs"><?php esc_html_e('Drop-Off Location', 'ecab-taxi-booking-manager'); ?></h6>
					<?php if($price_based == 'manual'){ ?>
						<p class="_textLight_1 mptbm_map_end_place"><?php echo esc_html(MPTBM_Function::get_taxonomy_name_by_slug( $end_place,'locations' )); ?></p>
					<?php }else{ ?>
						<p class="_textLight_1 mptbm_map_end_place"><?php echo esc_html($end_place); ?></p>
					<?php } ?>
					
					<?php if($two_way>1){ 
						?>
						<div class="dividerL"></div>
						<h6 class="_mB_xs"><?php esc_html_e('Transfer Type', 'ecab-taxi-booking-manager'); ?></h6>
						<p class="_textLight_1"><?php esc_html_e('Return', 'ecab-taxi-booking-manager'); ?></p>
						<div class="dividerL"></div>
						<h6 class="_mB_xs"><?php esc_html_e('Return Date', 'ecab-taxi-booking-manager'); ?></h6>
						<p class="_textLight_1"><?php echo esc_html(MP_Global_Function::date_format($return_date_time)); ?></p>
						<div class="dividerL"></div>
						<h6 class="_mB_xs"><?php esc_html_e('Return Time', 'ecab-taxi-booking-manager'); ?></h6>
						<p class="_textLight_1"><?php echo esc_html(MP_Global_Function::date_format($return_date_time,'time')); ?></p>
					<?php } ?>
					<?php if($waiting_time>0){ ?>
						<div class="dividerL"></div>
						<h6 class="_mB_xs"><?php esc_html_e('Extra Waiting Hours', 'ecab-taxi-booking-manager'); ?></h6>
						<p class="_textLight_1"><?php echo esc_html($waiting_time); ?>&nbsp;<?php esc_html_e('Hours', 'ecab-taxi-booking-manager'); ?></p>
					<?php } ?>
					<?php if($fixed_time && $fixed_time>0){ ?>
						<div class="dividerL"></div>
						<h6 class="_mB_xs"><?php esc_html_e('Service Times', 'ecab-taxi-booking-manager'); ?></h6>
						<p class="_textLight_1"><?php echo esc_html($fixed_time); ?> &nbsp;<?php esc_html_e('Hours', 'ecab-taxi-booking-manager'); ?></p>
					<?php } ?>
					<div class="mptbm_transport_summary">
						<div class="dividerL"></div>
						<h6 class="_mB_xs"><?php echo esc_html($label) . ' ' . esc_html__(' Details', 'ecab-taxi-booking-manager') ?></h6>
						<div class="_textColor_4 justifyBetween">
							<div class="_dFlex_alignCenter">
								<span class="fas fa-check-square _textTheme_mR_xs"></span>
								<span class="mptbm_product_name"></span>
							</div>
							<span class="mptbm_product_price _textTheme"></span>
						</div>
						<div class="mptbm_extra_service_summary"></div>
						<div class="dividerL"></div>
						<div class="justifyBetween">
							<h4><?php esc_html_e('Total : ', 'ecab-taxi-booking-manager'); ?></h4>
							<h6 class="mptbm_product_total_price"></h6>
						</div>
					</div>
				</div>
				<div class="divider"></div>
				<button type="button" class="_mpBtn_fullWidth mptbm_get_vehicle_prev">
					<span>&longleftarrow; &nbsp;<?php esc_html_e('Previous', 'ecab-taxi-booking-manager'); ?></span>
				</button>
			</div>
		</div>
	</div>
<?php
