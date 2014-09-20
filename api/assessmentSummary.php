<?php
	define('WP_USE_THEMES', false);
	require_once('../wp-blog-header.php');
	require_once('./dataMassager.php');

	$verb = $_SERVER['REQUEST_METHOD'];

	$response = array(
		'result' => 404,
		'data' => ''
	);

	function getAssessmentSummaries ($params) {
		global $wpdb;

		$assessment_form_fields = array('site_name', 'assessment_date', 'stream_quality_score', 'stream_quality_grade');
		$site_form_fields = array('site_name','site_id','lat_degrees','lat_minutes','lat_seconds','lng_degrees','lng_minutes','lng_seconds','site_directions');

		$assessments = $wpdb->get_results('SELECT submit_time, field_name, field_value FROM wp_cf7dbplugin_submits WHERE form_name = "'.$GLOBALS['ASSESSMENT_FORM_NAME'].'"', ARRAY_A);

		$sites = $wpdb->get_results('SELECT submit_time, field_name, field_value FROM wp_cf7dbplugin_submits WHERE form_name = "'.$GLOBALS['NEW_SITE_FORM_NAME'].'"', ARRAY_A);

		$assessments = massageData($assessments, $assessment_form_fields);
		$sites = massageData($sites, $site_form_fields);

		foreach ($sites as &$site) {
			foreach ($assessments as $assessment) {
				if ($site['site_name'] == $assessment['site_name']) {

					if (!array_key_exists('assessments', $site)) {
						$site['assessments'] = array();
					}

					$site['assessments'][] = $assessment;
				}
			}
		}

		return $sites;
	}

	if ($verb == 'GET') {
		try {
			$response['data'] = getAssessmentSummaries($_GET);
			$response['result'] = 200;
		} catch (Exception $e) {
			$response['data'] = $e->getMessage();
		}
	} else {
		$response['data'] = 'This API does not support that method.';
	}

	echo(json_encode($response));
