<?php

	$GLOBALS['ASSESSMENT_FORM_NAME'] = 'Stream Habitat Assessment Form';
	$GLOBALS['NEW_SITE_FORM_NAME'] = 'Add new site';

	function massageData ($data, $fields) {
		$massageData = array();
		$items = array();

		if (!is_array($fields)) {
			$fields = array();
		}

		foreach ($data as $datum) {
			if (!array_key_exists($datum['submit_time'], $items)) {
				$items[$datum['submit_time']] = array();
			}

			if (in_array($datum['field_name'], $fields)) {
				$items[$datum['submit_time']][$datum['field_name']] = $datum['field_value'];
			}
		}

		foreach ($items as $item) {
			$massagedData[] = $item;
		}

		return $massagedData;
	}