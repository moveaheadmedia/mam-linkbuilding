<?php

get_header();

/**
 * @var $resources WP_Query
 * */
$resources = apply_filters('mam-resources-filtered-posts', []);
if ($resources->have_posts()) {
	while ($resources->have_posts()) {
		$resources->the_post();
		$currency = get_field('currency');
		$original_price = get_field('original_price');
		$casino_price = get_field('casino_price');
		$cbd_price = get_field('cbd_price');
		$adult_price = get_field('adult_price');
		$link_placement_price = get_field('link_placement_price');
		$finale_price = get_field('price');
		if(!$currency){
			$currency = 'USD';
		}
		if(!$finale_price){
			$finale_price = $original_price;
		}
		if(!$finale_price){
			$finale_price = $casino_price;
		}
		if(!$finale_price){
			$finale_price = $cbd_price;
		}
		if(!$finale_price){
			$finale_price = $adult_price;
		}
		if(!$finale_price){
			$finale_price = $link_placement_price;
		}
		if(!$finale_price || !is_numeric($finale_price)){
			continue;
		}

		$usd_price = '';
		if($currency == 'USD'){
			$usd_price = $finale_price;
		}else{
			$usd_price = ceil($finale_price / mam_get_currency_rate($currency));
		}
		if(is_numeric($usd_price)){
			update_field('price_in_usd', $usd_price, get_the_ID());
		}
	}
}

function mam_get_currency_rate($currency){
	$file = file_get_contents(\MAM\Plugin\Config::getInstance()->plugin_path . 'currencies.json');
	$rates = json_decode($file, true);
	return ($rates['data'][$currency]);
}

echo '<p>Done</p>';
get_footer();