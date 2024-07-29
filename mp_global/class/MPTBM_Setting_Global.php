<?php
	/*
* @Author 		engr.sumonazma@gmail.com
* Copyright: 	mage-people.com
*/
	if (!defined('ABSPATH')) {
		die;
	} // Cannot access pages directly.
	if (!class_exists('MP_Settings_Global')) {
		class MP_Settings_Global {
			public function __construct() {
				add_filter('mp_settings_sec_reg', array($this, 'settings_sec_reg'),20);
				add_filter('mp_settings_sec_fields', array($this, 'settings_sec_fields'),20);
			}
			public function settings_sec_reg($default_sec): array {
				$sections = array(
					array(
						'id' => 'mp_global_settings',
						'icon' => 'fas fa-globe',
						'title' => esc_html__('Global Settings', 'ecab-taxi-booking-manager')
					),
					array(
						'id' => 'mp_slider_settings',
						'icon' => 'fas fa-photo-video',
						'title' => esc_html__('Slider Settings', 'ecab-taxi-booking-manager')
					),
					array(
						'id' => 'mp_style_settings',
						'icon' => 'fas fa-drafting-compass',
						'title' => esc_html__('Style Settings', 'ecab-taxi-booking-manager')
					),
					array(
						'id' => 'mp_add_custom_css',
						'icon' => 'far fa-file-code',
						'title' => esc_html__('Custom CSS', 'ecab-taxi-booking-manager')
					)
				);
				return array_merge($default_sec, $sections);
			}
			public function settings_sec_fields($default_fields): array {
				$current_date = current_time('Y-m-d');
				$settings_fields = array(
					'mp_global_settings' => apply_filters('filter_mp_global_settings', array(
						array(
							'name' => 'disable_block_editor',
							'label' => esc_html__('Disable Block/Gutenberg Editor', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('If you want to disable WordPress\'s new Block/Gutenberg editor, please select Yes.', 'ecab-taxi-booking-manager'),
							'type' => 'select',
							'default' => 'yes',
							'options' => array(
								'yes' => esc_html__('Yes', 'ecab-taxi-booking-manager'),
								'no' => esc_html__('No', 'ecab-taxi-booking-manager')
							)
						),
						array(
							'name' => 'set_book_status',
							'label' => esc_html__('Seat Booked Status', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Please Select when and which order status Seat Will be Booked/Reduced.', 'ecab-taxi-booking-manager'),
							'type' => 'multicheck',
							'default' => array(
								'processing' => 'processing',
								'completed' => 'completed'
							),
							'options' => array(
								'on-hold' => esc_html__('On Hold', 'ecab-taxi-booking-manager'),
								'pending' => esc_html__('Pending', 'ecab-taxi-booking-manager'),
								'processing' => esc_html__('Processing', 'ecab-taxi-booking-manager'),
								'completed' => esc_html__('Completed', 'ecab-taxi-booking-manager'),
							)
						),
						array(
							'name' => 'date_format',
							'label' => esc_html__('Date Picker Format', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('If you want to change Date Picker Format, please select format. Default  is D d M , yy.', 'ecab-taxi-booking-manager'),
							'type' => 'select',
							'default' => 'D d M , yy',
							'options' => array(
								'yy-mm-dd' => $current_date,
								'yy/mm/dd' => date_i18n('Y/m/d', strtotime($current_date)),
								'yy-dd-mm' => date_i18n('Y-d-m', strtotime($current_date)),
								'yy/dd/mm' => date_i18n('Y/d/m', strtotime($current_date)),
								'dd-mm-yy' => date_i18n('d-m-Y', strtotime($current_date)),
								'dd/mm/yy' => date_i18n('d/m/Y', strtotime($current_date)),
								'mm-dd-yy' => date_i18n('m-d-Y', strtotime($current_date)),
								'mm/dd/yy' => date_i18n('m/d/Y', strtotime($current_date)),
								'd M , yy' => date_i18n('j M , Y', strtotime($current_date)),
								'D d M , yy' => date_i18n('D j M , Y', strtotime($current_date)),
								'M d , yy' => date_i18n('M  j, Y', strtotime($current_date)),
								'D M d , yy' => date_i18n('D M  j, Y', strtotime($current_date)),
							)
						),
						array(
							'name' => 'date_format_short',
							'label' => esc_html__('Short Date  Format', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('If you want to change Short Date  Format, please select format. Default  is M , Y.', 'ecab-taxi-booking-manager'),
							'type' => 'select',
							'default' => 'M , Y',
							'options' => array(
								'D , M d' => date_i18n('D , M d', strtotime($current_date)),
								'M , Y' => date_i18n('M , Y', strtotime($current_date)),
								'M , y' => date_i18n('M , y', strtotime($current_date)),
								'M - Y' => date_i18n('M - Y', strtotime($current_date)),
								'M - y' => date_i18n('M - y', strtotime($current_date)),
								'F , Y' => date_i18n('F , Y', strtotime($current_date)),
								'F , y' => date_i18n('F , y', strtotime($current_date)),
								'F - Y' => date_i18n('F - y', strtotime($current_date)),
								'F - y' => date_i18n('F - y', strtotime($current_date)),
								'm - Y' => date_i18n('m - Y', strtotime($current_date)),
								'm - y' => date_i18n('m - y', strtotime($current_date)),
								'm , Y' => date_i18n('m , Y', strtotime($current_date)),
								'm , y' => date_i18n('m , y', strtotime($current_date)),
								'F' => date_i18n('F', strtotime($current_date)),
								'm' => date_i18n('m', strtotime($current_date)),
								'M' => date_i18n('M', strtotime($current_date)),
							)
						),
					)),
					'mp_slider_settings' => array(
						array(
							'name' => 'slider_type',
							'label' => esc_html__('Slider Type', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Please Select Slider Type Default Slider', 'ecab-taxi-booking-manager'),
							'type' => 'select',
							'default' => 'slider',
							'options' => array(
								'slider' => esc_html__('Slider', 'ecab-taxi-booking-manager'),
								'single_image' => esc_html__('Post Thumbnail', 'ecab-taxi-booking-manager')
							)
						),
						array(
							'name' => 'slider_style',
							'label' => esc_html__('Slider Style', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Please Select Slider Style Default Style One', 'ecab-taxi-booking-manager'),
							'type' => 'select',
							'default' => 'style_1',
							'options' => array(
								'style_1' => esc_html__('Style One', 'ecab-taxi-booking-manager'),
								'style_2' => esc_html__('Style Two', 'ecab-taxi-booking-manager'),
							)
						),
						array(
							'name' => 'indicator_visible',
							'label' => esc_html__('Slider Indicator Visible?', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Please Select Slider Indicator Visible or Not? Default ON', 'ecab-taxi-booking-manager'),
							'type' => 'select',
							'default' => 'on',
							'options' => array(
								'on' => esc_html__('ON', 'ecab-taxi-booking-manager'),
								'off' => esc_html__('Off', 'ecab-taxi-booking-manager')
							)
						),
						array(
							'name' => 'indicator_type',
							'label' => esc_html__('Slider Indicator Type', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Please Select Slider Indicator Type Default Icon', 'ecab-taxi-booking-manager'),
							'type' => 'select',
							'default' => 'icon',
							'options' => array(
								'icon' => esc_html__('Icon Indicator', 'ecab-taxi-booking-manager'),
								'image' => esc_html__('image Indicator', 'ecab-taxi-booking-manager')
							)
						),
						array(
							'name' => 'showcase_visible',
							'label' => esc_html__('Slider Showcase Visible?', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Please Select Slider Showcase Visible or Not? Default ON', 'ecab-taxi-booking-manager'),
							'type' => 'select',
							'default' => 'on',
							'options' => array(
								'on' => esc_html__('ON', 'ecab-taxi-booking-manager'),
								'off' => esc_html__('Off', 'ecab-taxi-booking-manager')
							)
						),
						array(
							'name' => 'showcase_position',
							'label' => esc_html__('Slider Showcase Position', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Please Select Slider Showcase Position Default Right', 'ecab-taxi-booking-manager'),
							'type' => 'select',
							'default' => 'right',
							'options' => array(
								'top' => esc_html__('At Top Position', 'ecab-taxi-booking-manager'),
								'right' => esc_html__('At Right Position', 'ecab-taxi-booking-manager'),
								'bottom' => esc_html__('At Bottom Position', 'ecab-taxi-booking-manager'),
								'left' => esc_html__('At Left Position', 'ecab-taxi-booking-manager')
							)
						),
						array(
							'name' => 'popup_image_indicator',
							'label' => esc_html__('Slider Popup Image Indicator', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Please Select Slider Popup Indicator Image ON or Off? Default ON', 'ecab-taxi-booking-manager'),
							'type' => 'select',
							'default' => 'on',
							'options' => array(
								'on' => esc_html__('ON', 'ecab-taxi-booking-manager'),
								'off' => esc_html__('Off', 'ecab-taxi-booking-manager')
							)
						),
						array(
							'name' => 'popup_icon_indicator',
							'label' => esc_html__('Slider Popup Icon Indicator', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Please Select Slider Popup Indicator Icon ON or Off? Default ON', 'ecab-taxi-booking-manager'),
							'type' => 'select',
							'default' => 'on',
							'options' => array(
								'on' => esc_html__('ON', 'ecab-taxi-booking-manager'),
								'off' => esc_html__('Off', 'ecab-taxi-booking-manager')
							)
						)
					),
					'mp_style_settings' => apply_filters('filter_mp_style_settings', array(
						array(
							'name' => 'theme_color',
							'label' => esc_html__('Theme Color', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Select Default Theme Color', 'ecab-taxi-booking-manager'),
							'type' => 'color',
							'default' => '#0793C9'
						),
						array(
							'name' => 'theme_alternate_color',
							'label' => esc_html__('Theme Alternate Color', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Select Default Theme Alternate  Color that means, if background theme color then it will be text color.', 'ecab-taxi-booking-manager'),
							'type' => 'color',
							'default' => '#fff'
						),
						array(
							'name' => 'default_text_color',
							'label' => esc_html__('Default Text Color', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Select Default Text  Color.', 'ecab-taxi-booking-manager'),
							'type' => 'color',
							'default' => '#000'
						),
						array(
							'name' => 'default_font_size',
							'label' => esc_html__('Default Font Size', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Type Default Font Size(in PX Unit).', 'ecab-taxi-booking-manager'),
							'type' => 'number',
							'default' => '15'
						),
						array(
							'name' => 'font_size_h1',
							'label' => esc_html__('Font Size h1 Title', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Type Font Size Main Title(in PX Unit).', 'ecab-taxi-booking-manager'),
							'type' => 'number',
							'default' => '35'
						),
						array(
							'name' => 'font_size_h2',
							'label' => esc_html__('Font Size h2 Title', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Type Font Size h2 Title(in PX Unit).', 'ecab-taxi-booking-manager'),
							'type' => 'number',
							'default' => '25'
						),
						array(
							'name' => 'font_size_h3',
							'label' => esc_html__('Font Size h3 Title', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Type Font Size h3 Title(in PX Unit).', 'ecab-taxi-booking-manager'),
							'type' => 'number',
							'default' => '22'
						),
						array(
							'name' => 'font_size_h4',
							'label' => esc_html__('Font Size h4 Title', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Type Font Size h4 Title(in PX Unit).', 'ecab-taxi-booking-manager'),
							'type' => 'number',
							'default' => '20'
						),
						array(
							'name' => 'font_size_h5',
							'label' => esc_html__('Font Size h5 Title', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Type Font Size h5 Title(in PX Unit).', 'ecab-taxi-booking-manager'),
							'type' => 'number',
							'default' => '18'
						),
						array(
							'name' => 'font_size_h6',
							'label' => esc_html__('Font Size h6 Title', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Type Font Size h6 Title(in PX Unit).', 'ecab-taxi-booking-manager'),
							'type' => 'number',
							'default' => '16'
						),
						array(
							'name' => 'button_font_size',
							'label' => esc_html__('Button Font Size ', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Type Font Size Button(in PX Unit).', 'ecab-taxi-booking-manager'),
							'type' => 'number',
							'default' => '18'
						),
						array(
							'name' => 'button_color',
							'label' => esc_html__('Button Text Color', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Select Button Text  Color.', 'ecab-taxi-booking-manager'),
							'type' => 'color',
							'default' => '#FFF'
						),
						array(
							'name' => 'button_bg',
							'label' => esc_html__('Button Background Color', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Select Button Background  Color.', 'ecab-taxi-booking-manager'),
							'type' => 'color',
							'default' => '#222'
						),
						array(
							'name' => 'font_size_label',
							'label' => esc_html__('Label Font Size ', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Type Font Size Label(in PX Unit).', 'ecab-taxi-booking-manager'),
							'type' => 'number',
							'default' => '18'
						),
						array(
							'name' => 'warning_color',
							'label' => esc_html__('Warning Color', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Select Warning  Color.', 'ecab-taxi-booking-manager'),
							'type' => 'color',
							'default' => '#E67C30'
						),
						array(
							'name' => 'section_bg',
							'label' => esc_html__('Section Background color', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Select Background  Color.', 'ecab-taxi-booking-manager'),
							'type' => 'color',
							'default' => '#FAFCFE'
						),
					)),
					'mp_add_custom_css' => apply_filters('filter_mp_add_custom_css', array(
						array(
							'name' => 'custom_css',
							'label' => esc_html__('Custom CSS', 'ecab-taxi-booking-manager'),
							'desc' => esc_html__('Write Your Custom CSS Code Here', 'ecab-taxi-booking-manager'),
							'type' => 'textarea',
						)
					))
				);
				return array_merge($default_fields, $settings_fields);
			}
		}
		new MP_Settings_Global();
	}