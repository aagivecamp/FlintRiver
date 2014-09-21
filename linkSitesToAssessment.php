<?php

	/**
	 * Queries in list of submitted sites and inserts them as <option> elements in the Assessment form "Stream name" field
	 * Normally, this function would be placed in "functions.php" in the root folder of the active theme
	 * 
	 * @return String HTML form
	 */
	function linkSitesToAssessment() {
		global $wpdb;

		$siteOptions = '';
		$html = '';
		$options = array();

		$sites = $wpdb->get_results('SELECT DISTINCT field_value FROM wp_cf7dbplugin_submits WHERE field_name="site_name"', ARRAY_N);

		// Flatten array of results
		foreach ($sites as $site) {
			$options[] = $site[0];
		}

		// Render the HTML option elements
		foreach ($options as $option) {
			$siteOptions .= '<option class="added" value="'.$option.'">'.$option.'</option>';
		}

		// Replace the select with dynamic list
		$html = preg_replace(
			'/<select(.*)name="site_name"(.*)>.*<\/select>/',
			'<select$1name="site_name"$2>'.$siteOptions.'</select>',
			do_shortcode('[contact-form-7 id="1693"]')
		);

		return $html;
	}

	add_shortcode('frwc_linked_form', 'linkSitesToAssessment');

?>