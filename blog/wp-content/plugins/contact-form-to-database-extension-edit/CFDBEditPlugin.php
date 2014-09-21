<?php
/*
    "Contact Form to Database Extension Edit" Copyright (C) 2011-2014 Simpson Software Studio LLC (email : mike@simpson-software-studio.com)

    This file is part of Contact Form to Database Extension Edit.

    Contact Form to Database Extension Edit is licensed under the terms of an End User License Agreement (EULA).
    You should have received a copy of the license along with Contact Form to Database Extension Edit
    (See the license.txt file).
*/

require_once(dirname(dirname(__FILE__)) . '/contact-form-7-to-database-extension/CF7DBPluginLifeCycle.php');

class CFDBEditPlugin extends CF7DBPluginLifeCycle {

    /**
     * @return string name of the main plugin file that has the header section with
     * "Plugin Name", "Version", "Description", "Text Domain", etc.
     */
    protected function getMainPluginFileName() {
        return 'contact-form-to-database-extension-edit.php';
    }

    protected function getPluginDir() {
        return dirname(__FILE__);
    }

    public function addActionsAndFilters() {

        add_action('cfdb_edit_enqueue', array(&$this, 'enqueue'));
        add_action('cfdb_edit_fnDrawCallbackJSON', array(&$this, 'fnDrawCallbackJSON'));
        add_action('cfdb_edit_fnDrawCallbackJsonForSC', array(&$this, 'fnDrawCallbackJsonForSC'));
        add_action('cfdb_edit_setup', array(&$this, 'setup'));

        add_action('wp_ajax_nopriv_cfdb-edit', array(&$this, 'ajaxEditEntry'));
        add_action('wp_ajax_cfdb-edit', array(&$this, 'ajaxEditEntry'));

        add_action('wp_ajax_nopriv_cfdb-getvalue', array(&$this, 'ajaxGetRawEntry'));
        add_action('wp_ajax_cfdb-getvalue', array(&$this, 'ajaxGetRawEntry'));

        add_action('wp_ajax_nopriv_cfdb-coledit', array(&$this, 'ajaxEditColumn'));
        add_action('wp_ajax_cfdb-coledit', array(&$this, 'ajaxEditColumn'));

        add_action('wp_ajax_nopriv_cfdb-addColumn', array(&$this, 'ajaxAddColumn'));
        add_action('wp_ajax_cfdb-addColumn', array(&$this, 'ajaxAddColumn'));

        add_action('wp_ajax_nopriv_cfdb-deleteColumn', array(&$this, 'ajaxDeleteColumn'));
        add_action('wp_ajax_cfdb-deleteColumn', array(&$this, 'ajaxDeleteColumn'));

        add_action('wp_ajax_nopriv_cfdb-importcsv', array(&$this, 'ajaxImportCsv'));
        add_action('wp_ajax_cfdb-importcsv', array(&$this, 'ajaxImportCsv'));

        add_action('wp_ajax_nopriv_cfdb-renameform', array(&$this, 'ajaxRenameForm'));
        add_action('wp_ajax_cfdb-renameform', array(&$this, 'ajaxRenameForm'));

    }

    public function enqueue() {
        wp_enqueue_script('jeditable', plugins_url('js/jquery.jeditable.mini.js', __FILE__), array('jquery'));
        wp_enqueue_script('cfdb.edit', plugins_url('js/cfdb.edit.js?e=1.3', __FILE__), array('jquery', 'jeditable'));
    }

    /**
     * @param CF7DBPlugin $plugin
     * @return void
     */
    public function setup($plugin) {
        ?>
    <div id="AddColumnDialog" style="display:none; background-color:#EEEEEE;">
        <input id="addColumnName" type="text" size="25" value=""/><br/>
        <input type="button" value="<?php _e('Cancel', 'contact-form-7-to-database-extension') ?>"
               onclick="jQuery('#AddColumnDialog').dialog('close');"/>
        <input id="addColumnOkButton" type="button" value=""
               onclick="addColumn();"/>
    </div>
    <div id="DeleteColumnDialog" style="display:none; background-color:#EEEEEE;">
        <select id="deleteColumnSelect"></select><br/>
        <input type="button" value="<?php _e('Cancel', 'contact-form-7-to-database-extension') ?>"
               onclick="jQuery('#DeleteColumnDialog').dialog('close');"/>
        <input id="deleteColumnOkButton" type="button" value=""
               onclick="deleteColumn();"/>
    </div>
    <script type="text/javascript" language="Javascript">
        jQuery('#edit_controls').html(
                '<input id="edit_cb" type="checkbox" onclick="oTable.fnDraw();"/>&nbsp;<label for="edit_cb">' +
                        jQuery('#edit_controls > a').text() + '</label>' +
                        '&nbsp;&nbsp;' +
                        '<input id="addColumnButton" type="button" value="">' +
                        '&nbsp;&nbsp;' +
                        '<input id="deleteColumnButton" type="button" value="">'
        );
        jQuery('#addColumnOkButton').val(addColumnLabelText);
        jQuery('#addColumnButton').val(addColumnLabelText).click(
                function() {
                    jQuery("#AddColumnDialog").dialog({ autoOpen: false, title: addColumnLabelText });
                    jQuery("#AddColumnDialog").dialog('open');
                    jQuery("#addColumnName").focus();
                });

        jQuery('#deleteColumnOkButton').val(deleteColumnLabelText);
        jQuery('#deleteColumnButton').val(deleteColumnLabelText).click(
                function() {
                    jQuery("#DeleteColumnDialog").dialog({ autoOpen: false, title: deleteColumnLabelText });
                    jQuery("#DeleteColumnDialog").dialog('open');
                    var url = '<?php echo $plugin->getFormFieldsAjaxUrlBase() ?>' + encodeURIComponent('<?php echo $_REQUEST['form_name']; ?>');
                    jQuery.getJSON(url, function(json) {
                        var optionsHtml = '';
                        jQuery(json).each(function() {
                            if (this != 'Submitted' && this != 'submit_time') { // can't delete submit_time
                                optionsHtml += '<option value="' + this + '">' + this + '</option>';
                            }
                        });
                        jQuery('#deleteColumnSelect').html(optionsHtml).focus();
                    });
                } );

        function addColumn() {
            jQuery('#addColumnOkButton').attr('disabled', 'disabled');
            var formName = '<?php echo $_REQUEST['form_name']; ?>';
            var colName = jQuery('#addColumnName').val();
            jQuery.ajax({
                cache: false,
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php') . '?action=cfdb-addColumn' ?>',
                data: { form_name : formName, column_name : colName},
                success: function(data, textStatus, jqXHR) {
                    jQuery('#AddColumnDialog').dialog('close');
                    location.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error ' + textStatus + ': ' + textStatus);
                    jQuery('#addColumnOkButton').removeAttr('disabled');
                }
            });
        }
        function deleteColumn() {
            jQuery('#deleteColumnOkButton').attr('disabled', 'disabled');
            var formName = '<?php echo $_REQUEST['form_name']; ?>';
            var colName = jQuery('#deleteColumnSelect').val();
            jQuery.ajax({
                cache: false,
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php') . '?action=cfdb-deleteColumn' ?>',
                data: { form_name : formName, column_name : colName},
                success: function(data, textStatus, jqXHR) {
                    jQuery('#DeleteColumnDialog').dialog('close');
                    location.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error ' + textStatus + ': ' + textStatus);
                    jQuery('#deleteColumnOkButton').removeAttr('disabled')
                }
            });
        }
    </script>

    <?php

    }

    public function ajaxEditEntry() {
        header('Content-Type: text/plain; charset=UTF-8');
        header("Pragma: no-cache");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        $value = stripslashes($_REQUEST['value']);
        try {
            require_once(dirname(dirname(__FILE__)) . '/contact-form-7-to-database-extension/ExportToHtmlTable.php');
            $export = new ExportToHtmlTable;

            if ($export->plugin->canUserDoRoleOption('CanChangeSubmitData')) {
                $key = explode(',', $_REQUEST['id']); // submit_time = $key[0], field_name = $key[1]
                $tableName = $export->plugin->getSubmitsTableName();

                global $wpdb;
                // Use "like" below to address: http://bugs.mysql.com/bug.php?id=30485
                $sql = "update `$tableName` set `field_value` = %s where `submit_time` like %s and `field_name` = %s";
                $rowsUpdated = $wpdb->query($wpdb->prepare($sql, $value, $key[0], $key[1]));
                if ($rowsUpdated === false) {
                    error_log(sprintf('CFDB Error: %s', $wpdb->last_error));
                }
                else if ($rowsUpdated === 0) {
                    $sql = "select distinct `form_name` as 'form_name' from `$tableName` where `submit_time` = %s limit 1";
                    $row = $wpdb->get_row($wpdb->prepare($sql, $key[0]));

                    $sql = "insert into `$tableName` (`submit_time`, `form_name`, `field_name`, `field_value`) values (%s, %s, %s, %s)";
                    $wpdb->query($wpdb->prepare($sql, $key[0], $row->form_name, $key[1], $value));
                }

                $sql = "select `field_value`, `form_name`, `file` is not null and length(`file`) > 0 as 'is_file' from `$tableName` where `submit_time` = %s and `field_name` = %s";
                $row = $wpdb->get_row($wpdb->prepare($sql, $key[0], $key[1]));

                $value = $row->form_name;
                $value = $export->rawValueToPresentationValue(
                    $row->field_value,
                    ($export->plugin->getOption('ShowLineBreaksInDataTable') != 'false'),
                    ($row->is_file != 0),
                    $key[0],
                    $row->form_name,
                    $key[1]);
            }
            else {
                die(1);
            }
        }
        catch (Exception $ex) {
            error_log(sprintf('CFDB Error: %s:%s %s  %s', $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getTraceAsString()));
        }

        echo $value;
        die();
    }

    public function ajaxGetRawEntry() {
        header('Content-Type: text/plain; charset=UTF-8');
        header("Pragma: no-cache");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        $value = '';
        try {
            require_once(dirname(dirname(__FILE__)) . '/contact-form-7-to-database-extension/CF7DBPlugin.php');
            $cfdb = new CF7DBPlugin;
            if ($cfdb->canUserDoRoleOption('CanChangeSubmitData')) {
                global $wpdb;
                $delim = (strpos($_REQUEST['id'], ',') !== false) ? ',' : '%2C';
                $key = explode($delim, $_REQUEST['id']);

                $tableName = $cfdb->getSubmitsTableName();
                $sql = "select `field_value` from `$tableName` where `submit_time` = %F and `field_name` = '%s'";

                //$value = $wpdb->get_var($wpdb->prepare($sql, $key[0], $key[1]));
                $rows = $wpdb->get_results($wpdb->prepare($sql, $key[0], $key[1]));
                foreach ($rows as $aRow) {
                    if ($aRow->field_value != '') {
                        $value = $aRow->field_value;
                        break;
                    }
                }
            }
            else {
                die(1);
            }
        }
        catch (Exception $ex) {
            error_log(sprintf('CFDB Error: %s:%s %s  %s', $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getTraceAsString()));
        }

        echo $value;
        die();
    }

    public function ajaxEditColumn() {
        header('Content-Type: text/plain; charset=UTF-8');
        header("Pragma: no-cache");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
        $delim = (strpos($_REQUEST['id'], ',') !== false) ? ',' : '%2C';
        $key = explode($delim, $_REQUEST['id']); // form_name = $key[0], field_name = $key[1]
        $returnName = $key[1];
        try {
            require_once(dirname(dirname(__FILE__)) . '/contact-form-7-to-database-extension/CF7DBPlugin.php');
            $cfdb = new CF7DBPlugin;
            if ($cfdb->canUserDoRoleOption('CanChangeSubmitData')) {
                global $wpdb;

                $tableName = $cfdb->getSubmitsTableName();
                $sql = "update `$tableName` set `field_name` = %s where `form_name` = %s and `field_name` = %s";
                if ($wpdb->query($wpdb->prepare($sql, $_REQUEST['value'], $key[0], $key[1]))) {
                    $returnName = $_REQUEST['value'];
                }
            }
            else {
                die(1);
            }
        }
        catch (Exception $ex) {
            error_log(sprintf('CFDB Error: %s:%s %s  %s', $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getTraceAsString()));
        }

        echo $returnName;
        die();

    }

    public function ajaxAddColumn() {
        header("Pragma: no-cache");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

        $formName = $_REQUEST['form_name'];
        $columnName = $_REQUEST['column_name'];
        if (!$formName || !$columnName) {
            die(1);
        }
        try {
            require_once(dirname(dirname(__FILE__)) . '/contact-form-7-to-database-extension/CF7DBPlugin.php');
            $cfdb = new CF7DBPlugin;
            if ($cfdb->canUserDoRoleOption('CanChangeSubmitData')) {
                global $wpdb;
                $tableName = $cfdb->getSubmitsTableName();
                $sql = "insert into `$tableName` (`submit_time`, `form_name`, `field_name`, `field_value` ) select distinct `submit_time`, `form_name`, '$columnName', '' from `$tableName` where `form_name` = %s";
                $wpdb->query($wpdb->prepare($sql, $formName, $columnName));
            }
            else {
                die(1);
            }
        }
        catch (Exception $ex) {
            error_log(sprintf('CFDB Error: %s:%s %s  %s', $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getTraceAsString()));
            die(1);
        }
        die();
    }

    public function ajaxDeleteColumn() {
        header("Pragma: no-cache");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

        $formName = $_REQUEST['form_name'];
        $columnName = $_REQUEST['column_name'];
        if (!$formName || !$columnName) {
            die(1);
        }
        try {
            require_once(dirname(dirname(__FILE__)) . '/contact-form-7-to-database-extension/CF7DBPlugin.php');
            $cfdb = new CF7DBPlugin;
            if ($cfdb->canUserDoRoleOption('CanChangeSubmitData')) {
                global $wpdb;
                $tableName = $cfdb->getSubmitsTableName();
                $sql = "delete from `$tableName` where `form_name` = %s and `field_name` = %s";
                $wpdb->query($wpdb->prepare($sql, $formName, $columnName));
            }
            else {
                die(1);
            }
        }
        catch (Exception $ex) {
            error_log(sprintf('CFDB Error: %s:%s %s  %s', $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getTraceAsString()));
            die(1);
        }
        die();
    }

    public function fnDrawCallbackJSON($tableHtmlId) {
        $cfdbEditUrl = admin_url('admin-ajax.php') . '?action=cfdb-edit';
        $cfdbGetValueUrl = admin_url('admin-ajax.php') . '?action=cfdb-getvalue';
        $cfdbColEditUrl = admin_url('admin-ajax.php') . '?action=cfdb-coledit';
        $loadImg = plugins_url('img/load.gif', __FILE__);
        ?>
         ,  "fnDrawCallback" : function() {
                if (jQuery('#edit_cb').is(':checked')) {
                    cfdbEditable(
                        <?php echo "'$tableHtmlId'" ?>,
                        <?php echo "'$cfdbEditUrl'" ?>,
                        <?php echo "'$cfdbGetValueUrl'" ?>,
                        <?php echo "'$cfdbColEditUrl'" ?>,
                        <?php echo "'$loadImg'" ?>
                    );
                }
                else {
                    jQuery('#<?php echo $tableHtmlId ?> td:not([title="Submitted"]) > div').addClass('non_edit').removeClass('edit').unbind('click.editable');
                    jQuery('#<?php echo $tableHtmlId ?> th:not([title="Submitted"]) > div > div').addClass('non_edit').removeClass('edit').unbind('click.editable');
                }
            }
        <?php
    }

    public function fnDrawCallbackJsonForSC($tableHtmlId) {
        $cfdbEditUrl = admin_url('admin-ajax.php') . '?action=cfdb-edit';
        $cfdbGetValueUrl = admin_url('admin-ajax.php') . '?action=cfdb-getvalue';
        $cfdbColEditUrl = admin_url('admin-ajax.php') . '?action=cfdb-coledit';
        $loadImg = plugins_url('img/load.gif', __FILE__);
        echo ",  \"fnDrawCallback\" : function() { cfdbEditable('$tableHtmlId','$cfdbEditUrl','$cfdbGetValueUrl','$cfdbColEditUrl','$loadImg'); }";
    }

    public function ajaxImportCsv() {
        header("Pragma: no-cache");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

        $cfdb = new CF7DBPlugin;
        if (!$cfdb->canUserDoRoleOption('CanChangeSubmitData')) {
            echo "Permission denied";
            die(1);
        }

        // Array ( [action] => cfdb-importcsv [file] => alanine.csv [into] => into [newformname] =>       [form] => File Upload [Import] => import )
        // Array ( [action] => cfdb-importcsv [file] => alanine.csv [into] => new  [newformname] => mike  [form] =>             [Import] => import )

        //print_r($_REQUEST);
        //print_r($_FILES);

        if (!isset($_FILES['file']) || !$_FILES['file']) {
            echo 'No file uploaded';
            die(1);
        }

        if (!isset($_REQUEST['into']) || !$_REQUEST['into']) {
            echo 'Missing input to indicate new or existing form';
            die(1);
        }

        $formName = null;
        if ($_REQUEST['into'] == 'new') {
            if (!isset($_REQUEST['newformname']) || !$_REQUEST['newformname']) {
                echo 'No new form name set';
                die(1);
            }
            $formName = $_REQUEST['newformname'];
        }

        if ($_REQUEST['into'] == 'into') {
            if (!isset($_REQUEST['form']) || !$_REQUEST['form']) {
                echo 'No form chosen';
                die(1);
            }
            $formName = $_REQUEST['form'];
        }

        if (!$formName) {
            echo 'No form name';
            die(1);
        }

        // Array ( [file] => Array ( [name] => alanine.csv [type] => application/octet-stream [tmp_name] => /tmp/phpJW3RIm [error] => 0 [size] => 1214 ) )
        ini_set('auto_detect_line_endings', true); // recognize Mac \r as a valid line ending
        $handle = fopen($_FILES['file']['tmp_name'], 'r');
        if (!$handle) {
            echo 'Cannot read file: ' . $_FILES['file']['tmp_name'];
            die(1);
        }

        global $wpdb;
        require_once(dirname(dirname(__FILE__)) . '/contact-form-7-to-database-extension/CF7DBPlugin.php');
        $cfdb = new CF7DBPlugin();
        $tableName = $cfdb->getSubmitsTableName();
        $parametrizedQuery = "INSERT INTO `$tableName` (`submit_time`, `form_name`, `field_name`, `field_value`, `field_order`) VALUES (%s, %s, %s, %s, %s)";

        $row = 1;
        $headerRow = null;
        $headerCount = 0;
        $generatedSubmitTime = function_exists('microtime') ? microtime(true) : time();
        while (($data = fgetcsv($handle)) !== false) {
            $rowSize = count($data);
            if (!$data[$rowSize - 1]) {
                // bogus blank at end of row due to trailing comma
                array_pop($data);
                $rowSize--;
            }

            if ($row == 1) {
                $headerRow = $data;
                $headerCount = $rowSize;
                //echo "header: " ; print_r($headerRow); echo "<br/>";
            }
            else {
                $importData = array('submit_time' => '');
                for ($idx = 0; $idx < $rowSize; $idx++) {
                    $fieldName = ($idx < $headerCount) ? $headerRow[$idx] : 'field' . $idx;
                    $importData[$fieldName] = $data[$idx];
                }
                if ($importData['submit_time'] == '') {
                    $generatedSubmitTime += 0.01;
                    $importData['submit_time'] = $generatedSubmitTime;
                }

                //print_r($importData); echo "\n<br/>";
                $order = -1;
                foreach ($importData as $fieldName => $value) {
                    if ($fieldName == 'submit_time') {
                        continue;
                    }

                    ++$order;
                    $fieldOrder = $order;
                    if ($fieldName == 'Submitted Login') {
                        $fieldOrder = ($order < 9999) ? 9999 : $order; // large order num to try to make it always next-to-last

                    }
                    else if ($fieldName == 'Submitted From') {
                        $fieldOrder = ($order < 10000) ? 10000 : $order; // large order num to try to make it always last
                    }

                    //echo $importData['submit_time'] . " $formName:$fieldOrder : $fieldName = $value<br/>";
                    $wpdb->query($wpdb->prepare($parametrizedQuery,
                        $importData['submit_time'],
                        $formName,
                        $fieldName,
                        $value,
                        $fieldOrder));
                }
            }
            $row++;
        }
        fclose($handle);

        $url = admin_url('admin.php') . '?page=CF7DBPluginSubmissions&form_name=' . urlencode($formName);
        echo ($row - 2) . " rows processed into form <a href=\"$url\">$formName</a>";
        $backUrl = admin_url('admin.php') . '?page=CF7DBPluginImport';
        printf('<br/><a href="%s">%s</a>', $backUrl, 'Back');

        die();
    }

    public function ajaxRenameForm() {
        header("Pragma: no-cache");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

        $cfdb = new CF7DBPlugin;
        if (!$cfdb->canUserDoRoleOption('CanChangeSubmitData')) {
            die(1);
        }

        if (!isset($_REQUEST['form']) || !$_REQUEST['form']) {
            echo 'No form name set';
            die(1);
        }
        if (!isset($_REQUEST['newformname']) || !$_REQUEST['newformname']) {
            echo 'No new form name set';
            die(1);
        }

        global $wpdb;
        $tableName = $cfdb->getSubmitsTableName();
        $parametrizedQuery = "UPDATE `$tableName` SET `form_name` = %s WHERE `form_name` = %s";
        $result =  $wpdb->query($wpdb->prepare($parametrizedQuery, $_REQUEST['newformname'], $_REQUEST['form']));
        if ($result == false) {
            echo 'Failed to update';
        } else {
            $url = admin_url('admin.php') . '?page=CF7DBPluginSubmissions&form_name=' . $_REQUEST['newformname'];
            printf('Form "%s" renamed to <a href="%s">"%s"</a>.', $_REQUEST['form'], $url, $_REQUEST['newformname']);
            $backUrl = admin_url('admin.php') . '?page=CF7DBPluginImport';
            printf('<br/><a href="%s">%s</a>', $backUrl, 'Back');
        }

        die();
    }

}