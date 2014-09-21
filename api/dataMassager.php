<?php

	// THESE ARE EXTREMELY IMPORTANT!
	// These form names must match exactly what they are called in Contact Form 7.
	$GLOBALS['ASSESSMENT_FORM_NAME'] = 'Stream Habitat Assessment Form';
	$GLOBALS['NEW_SITE_FORM_NAME'] = 'Add new watershed site';

	function massageData ($data, $whitelist) {
		$massageData = array();
		$items = array();
		$blacklist = array('Submitted From', 'Submitted Login');

		if (!is_array($whitelist)) {
			$whitelist = array();
		}

		$isWhitelisting = count($whitelist) > 0;

		foreach ($data as $datum) {
			if (!array_key_exists($datum['submit_time'], $items)) {
				$items[$datum['submit_time']] = array();
			}

			if (!in_array($datum['field_name'], $blacklist)) {
				if ($isWhitelisting) {
					if (in_array($datum['field_name'], $whitelist)) {
						$items[$datum['submit_time']][$datum['field_name']] = $datum['field_value'];
					}
				} else {
					$items[$datum['submit_time']][$datum['field_name']] = $datum['field_value'];
				}
			}
		}

		foreach ($items as $item) {
			$massagedData[] = $item;
		}

		return $massagedData;
	}