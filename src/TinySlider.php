<?php

namespace Absatzformat\Wordpress\TinySlider;

final class TinySlider
{
	// shortcode attr => [js key, default value]
	/** @var array */
	protected static $optionsMapping = [
		'mode' =>							['mode',						'carousel'],
		'axis' => 							['axis',						'horizontal'],
		'items' =>							['items',						1],
		'gutter' => 						['gutter',						0],
		'edge_padding' => 					['edgePadding',					0],
		'fixed_width' => 					['fixedWidth',					false],
		'auto_width' => 					['autoWidth',					false],
		'viewport_max' => 					['viewportMax',					false],
		'slide_by' => 						['slideBy',						1], // or 'page'
		'center' => 						['center',						false],
		'controls' => 						['controls',					true],
		'controls_position' => 				['controlsPosition',			'top'],
		'controls_text' =>					['controlsText',				'prev|next'], // js val array
		'controls_container' => 			['controlsContainer',			false],
		'prev_button' => 					['prevButton',					false],
		'next_button' => 					['nextButton',					false],
		'nav' => 							['nav',							true],
		'nav_position' => 					['navPosition',					'top'],
		'nav_container' => 					['navContainer',				false],
		'nav_as_thumbnails' => 				['navAsThumbnails',				false],
		'arrow_keys' => 					['arrowKeys',					false],
		'speed' => 							['speed',						300],
		'autoplay' => 						['autoplay',					false],
		'autoplay_position' => 				['autoplayPosition',			'top'],
		'autoplay_timeout' => 				['autoplayTimeout',				5000],
		'autoplay_direction' => 			['autoplayDirection',			'forward'],
		'autoplay_text' => 					['autoplayText',				'start|stop'], // js val array
		'autoplay_hover_pause' =>			['autoplayHoverPause',			false],
		'autoplay_button' => 				['autoplayButton',				false],
		'autoplay_button_output' => 		['autoplayButtonOutput',		true],
		'autoplay_reset_on_visibility' =>	['autoplayResetOnVisibility',	true],
		'animate_in' => 					['animateIn',					'tns-fadeIn'],
		'animate_out' => 					['animateOut',					'tns-fadeOut'],
		'animate_normal' =>		 			['animateNormal',				'tns-normal'],
		'animate_delay' =>					['animateDelay',				false],
		'loop' => 							['loop',						true],
		'rewind' =>							['rewind',						false],
		'auto_height' => 					['autoHeight',					false],
		'responsive' => 					['responsive',					false],
		'lazyload' => 						['lazyload',					false],
		'lazyload_selector' => 				['lazyloadSelector',			'.tns-lazy-img'],
		'touch' => 							['touch',						true],
		'mouse_drag' => 					['mouseDrag',					false],
		'swipe_angle' =>		 			['swipeAngle',					15],
		'nested' =>				 			['nested',						false],
		'prevent_action_when_running' => 	['preventActionWhenRunning',	false],
		'prevent_scroll_on_touch' => 		['preventScrollOnTouch',		false],
		'freezable' => 						['freezable',					true],
		'use_local_storage' => 				['useLocalLtorage',				true],
		'nonce' => 							['nonce',						false]
	];

	protected static $shortcodeName = 'wp-tiny-slider';

	/** @var null|TinySlider */
	protected static $instance = null;

	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	protected function __construct()
	{
		if (!is_admin()) {

			add_action('wp_enqueue_scripts', [$this, 'registerScripts']);
			add_shortcode(self::$shortcodeName, [$this, 'handleShortcode']);
		}
	}

	public function registerScripts(): void
	{
		$pluginUrl = plugin_dir_url(__DIR__ . '/../..');

		wp_register_style('tiny-slider', $pluginUrl . 'assets/css/tiny-slider.css');
		wp_register_script('tiny-slider', $pluginUrl . 'assets/js/tiny-slider.js', [], null, true);
	}

	public function enqueueScripts(): void
	{
		wp_enqueue_style('tiny-slider');
		wp_enqueue_script('tiny-slider');
	}

	protected static function getDefaultOptions(): array
	{
		return array_map(function ($mapping) {
			return $mapping[1];
		}, self::$optionsMapping);
	}

	protected static function mapJsOptions($options): array
	{

		$mappedOptions = [];
		foreach (self::$optionsMapping as $key => $mapping) {

			$val = $options[$key];

			// lazy casting
			if ($val === 'true' || $val === 'false') {
				$val = $val === 'true';
			} else if (is_numeric($val)) {
				$val = floatval($val);
			}

			$mappedOptions[$mapping[0]] = $val;
		}

		// handle array options
		$mappedOptions['controlsText'] = explode('|', trim($mappedOptions['controlsText']));
		$mappedOptions['autoplayText'] = explode('|', trim($mappedOptions['autoplayText']));

		return $mappedOptions;
	}

	public function handleShortcode($attrs = [], $content = null): string
	{
		// load scripts
		$this->enqueueScripts();

		// get default options array
		$defaultOptions = self::getDefaultOptions();

		// extend options
		$options = shortcode_atts($defaultOptions, $attrs, self::$shortcodeName);

		// map options
		$mappedOptions = self::mapJsOptions($options);

		// add container class
		$containerClass = uniqid('wp-tiny-slider-');
		$mappedOptions['container'] = '.' . $containerClass;

		$jsonOptions = json_encode($mappedOptions);

		wp_add_inline_script('tiny-slider', "tns($jsonOptions);");

		return '<div class="' . $containerClass . '">' . $content . '</div>';
	}
}
