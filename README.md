# Wordpress Tiny Slider

Wordpress Content Slider based on Tiny Slider.

## Usage

```html
[wp-tiny-slider]
	<div>Slide 1</div>
	<div>Slide 2</div>
	<div>Slide 3</div>
[/wp-tiny-slider]
```

## Configuration

You can define all available JavaScript options in the shortcode.
Take a look at [Tiny Slider Options](https://github.com/ganlanyuan/tiny-slider#options) for reference.

```
[wp-tiny-slider autoplay="true" autoplay_button_output="false"] ... [/wp-tiny-slider]
```

> Note that you need to convert the CamelCase option strings to snake_case: ```AutoplayButtonOutput``` becomes ```autoplay_button_output```.
