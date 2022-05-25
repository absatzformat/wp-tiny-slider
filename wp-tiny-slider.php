<?php

/*
	Plugin Name: Tiny Slider
	Description: Content slider with Tiny Slider
	Author: Absatzformat GmbH
	Version: 1.0.1
	Author URI: https://absatzformat.de
	Plugin URI: https://github.com/absatzformat/wp-tiny-slider
*/

use Absatzformat\Wordpress\TinySlider\TinySlider;

defined('WPINC') || die();

require __DIR__ . '/src/TinySlider.php';

TinySlider::getInstance();
