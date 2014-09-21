<?php
/*
	Template Name: Stream Assessment Report
 */
	get_header();

	$date = $_GET['assessment_date'];
	$site = $_GET['site_name'];

	$template = get_post_field('post_content', 1722);

	echo(do_shortcode('[cfdb-html form="Stream Habitat Assessment Form" filter="site_name='.$site.'&&assessment_date='.$date.'"]'.$template.'[/cfdb-html]'));

	get_footer();
?>