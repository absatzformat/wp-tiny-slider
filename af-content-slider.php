<?php

/*
	Plugin Name: Content Slider
	Description: Content slider with Tiny Slider
	Author: Absatzformat GmbH
	Version: 1.0.0
	Author URI: https://absatzformat.de
*/

namespace Absatzformat\Wordpress\ContentSlider;

defined('WPINC') or die();

define(__NAMESPACE__.'\PLUGIN_VERSION', '1.0.0');
define(__NAMESPACE__.'\PLUGIN_PATH', plugin_dir_path(__FILE__));
define(__NAMESPACE__.'\PLUGIN_URL', plugin_dir_url(__FILE__));
define(__NAMESPACE__.'\PLUGIN_SLUG', pathinfo(__FILE__, PATHINFO_FILENAME));
define(__NAMESPACE__.'\MENU_SLUG', PLUGIN_SLUG);


final class ContentSlider{

	private static $instance = null;

	private const DEFAULT_OPTIONS = [
		'mode' =>							'carousel',
		'axis' => 							'horizontal',
		'items' =>							1,
		'gutter' => 						0,
		'edge_padding' => 					0,
		'fixed_width' => 					false,
		'auto_width' => 					false,
		'viewport_max' => 					false,
		'slide_by' => 						1,
		'center' => 						false,
		'controls' => 						true,
		'controls_position' => 				'top',
		'controls_text' => '				prev|next',
		'controls_container' => 			false,
		'prev_button' => 					false,
		'next_button' => 					false,
		'nav' => 							true,
		'nav_position' => 					'top',
		'nav_container' => 					false,
		'nav_as_thumbnails' => 				false,
		'arrow_keys' => 					false,
		'speed' => 							300,
		'autoplay' => 						false,
		'autoplay_position' => 				'top',
		'autoplay_timeout' => 				5000,
		'autoplay_direction' => 			'forward',
		'autoplay_text' => 					'start|stop',
		'autoplay_hoverPause' =>			false,
		'autoplay_button' => 				false,
		'autoplay_buttonOutput' => 			true,
		'autoplay_reset_on_visibility' =>	 true,
		'animate_in' => 					'tns-fadeIn',
		'animate_out' => 					'tns-fadeOut',
		'animate_normal' =>		 			'tns-normal',
		'animate_delay' =>					false,
		'loop' => 							true,
		'rewind' =>							false,
		'auto_height' => 					false,
		'responsive' => 					false,
		'lazyload' => 						false,
		'lazyload_selector' => 				'.tns-lazy-img',
		'touch' => 							true,
		'mouse_drag' => 					false,
		'swipe_angle' =>		 			15,
		'nested' =>				 			false,
		'prevent_action_when_running' => 	false,
		'prevent_scroll_on_touch' => 		false,
		'freezable' => 						true,
		'use_local_storage' => 				true,
		'nonce' => 							false
	];

	private const OPTIONS_MAPPING = [

		'mode' => 'mode',
		'axis' => 'axis',
		'items' => 'items',
		'gutter' => 'gutter',
		'edge_padding' => 'edgePadding',
		'fixed_width' => 'fixedWidth',
		'auto_width' => 'autoWidth',
		'viewport_max' => 'viewportMax',
		'slide_by' => 'slideBy',
		'center' => 'center',
		'controls' => 'controls',
		'controls_position' => 'controlsPosition',
		'controls_text' => 'controlsText',
		'controls_container' => 'controlsContainer',
		'prev_button' => 'prevButton',
		'next_button' => 'nextButton',
		'nav' => 'nav',
		'nav_position' => 'navPosition',
		'nav_container' => 'navContainer',
		'nav_as_thumbnails' => 'navAsThumbnails',
		'arrow_keys' => 'arrowKeys',
		'speed' => 'speed',
		'autoplay' => 'autoplay',
		'autoplay_position' => 'autoplayPosition',
		'autoplay_timeout' => 'autoplayTimeout',
		'autoplay_direction' => 'autoplayDirection',
		'autoplay_text' => 'autoplayText',
		'autoplay_hover_pause' => 'autoplayHoverPause',
		'autoplay_button' => 'autoplayButton',
		'autoplay_button_output' => 'autoplayButtonOutput',
		'autoplay_reset_on_visibility' => 'autoplayResetOnVisibility',
		'animate_in' => 'animateIn',
		'animate_out' => 'animateOut',
		'animate_normal' => 'animateNormal',
		'animate_delay' => 'animateDelay',
		'loop' => 'loop',
		'rewind' => 'rewind',
		'auto_height' => 'autoHeight',
		'responsive' => 'responsive',
		'lazyload' => 'lazyload',
		'lazyload_selector' => 'lazyloadSelector',
		'touch' => 'touch',
		'mouse_drag' => 'mouseDrag',
		'swipe_angle' => 'swipeAngle',
		'nested' => 'nested',
		'prevent_action_when_running' => 'preventActionWhenRunning',
		'prevent_scroll_on_touch' => 'preventScrollOnTouch',
		'freezable' => 'freezable',
		'use_local_storage' => 'useLocalStorage',
		'nonce' => 'nonce',
	];

	public static function getInstance(){

		if(self::$instance === null){
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function init(){
		return self::getInstance();
	}

	private function __construct(){

		wp_register_style('tiny-slider', PLUGIN_URL.'assets/css/tiny-slider.css');
		wp_register_script('tiny-slider', PLUGIN_URL.'assets/js/tiny-slider.js', [], null, true);

		add_shortcode('af-content-slider', [$this, 'handleShortcode']);
	}

	public function handleShortcode($attrs = [], $content = null){

		// load scripts
		wp_enqueue_style('tiny-slider');
		wp_enqueue_script('tiny-slider');

		$defaultOptions = self::DEFAULT_OPTIONS;
		$containerClass = uniqid('af-');

		$options = shortcode_atts($defaultOptions, $attrs);

		// remap
		$mappedOptions = [];
		foreach($options as $key => $val){
			if(isset(self::OPTIONS_MAPPING[$key])){
				if($val == 'true' || $val == 'false'){
					$val = boolval($val);
				}
				else if(is_numeric($val)){
					$val = floatval($val);
				}
				$mappedOptions[self::OPTIONS_MAPPING[$key]] = $val;
			}
		}

		$mappedOptions['container'] = '.'.$containerClass;
		
		$mappedOptions['controlsText'] = explode('|', trim($options['controls_text']));
		$mappedOptions['autoplayText'] = explode('|', trim($options['autoplay_text']));

		$jsonOptions = json_encode($mappedOptions);

		wp_add_inline_script('tiny-slider', <<<JS
			tns($jsonOptions);
		JS);

		return <<<HTML
			<div class="$containerClass">$content</div>
		HTML;
	}
}

ContentSlider::init();