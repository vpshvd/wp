<?php
/*
Plugin Name: Time to Read
Description: Adds an estimated reading time to posts based on the number of symbols.
Author: Volodymyr Shved
Version: 1.0
*/

function add_reading_time_to_content( $content ): string {
	$time_to_read = round( strlen( strip_tags( trim( $content ) ) ) / 1000 );
	$time_to_read = ($time_to_read == 0) ? 1 : $time_to_read;
	$read_time_text = "<p>Estimated reading time: $time_to_read minutes</p>";

	return $content . $read_time_text;
}

add_filter( 'the_content', 'add_reading_time_to_content' );
