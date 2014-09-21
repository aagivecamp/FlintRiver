/*
 "Contact Form to Database Extension Edit" Copyright (C) 2011-2014 Simpson Software Studio LLC (email : info@simpson-software-studio.com)

 This file is part of Contact Form to Database Extension Edit.

 Contact Form to Database Extension Edit is licensed under the terms of an End User License Agreement (EULA).
 You should have received a copy of the license along with Contact Form to Database Extension Edit
 (See the license.txt file).
 */

function cfdbEditable(tableHtmlId, cfdbEditUrl, cfdbGetValueUrl, cfdbColEditUrl, loadImg) {

    jQuery('#' + tableHtmlId).find('td:not([title="Submitted"]) > div').editable(
            cfdbEditUrl,
            {
                type: 'textarea',
                submit: 'OK',
                indicator: '<img alt="Saving..." src="' + loadImg + '"/>',
                height: '50px',
                placeholder: '&nbsp;',
                select: 'true',
                ajaxoptions: {
                    cache: false
                },
                loadurl: cfdbGetValueUrl
            }
    );
    jQuery('#' + tableHtmlId + '_wrapper').find('th:not([title="Submitted"]) > div > div').editable(
            cfdbColEditUrl,
            {
                type: 'textarea',
                submit: 'OK',
                indicator: '<img alt="Saving..." src="' + loadImg + '"/>',
                height: '50px',
                placeholder: '&nbsp;',
                select: 'true',
                ajaxoptions: {
                    cache: false
                },
                callback: function (newColumnName, settings) {
                    var origColumnName = this.id.match(/,.*$/)[0].substring(1);
                    this.id = this.id.replace("," + origColumnName, "," + newColumnName);
                    jQuery('#' + tableHtmlId + ' td[title="' + origColumnName + '"] > div').each(
                            function (index, element) {
                                this.id = this.id.replace("," + origColumnName, "," + newColumnName);
                            }
                    );
                    jQuery('#' + tableHtmlId + '' +
                            ' td[title="' + origColumnName + '"]').attr("title", newColumnName);
                }
            });
}

function cfdbEntryEditable(tableHtmlId, cfdbEditUrl, cfdbGetValueUrl, loadImg) {
    (function ($) {
        $("#" + tableHtmlId).find("tr:not(:first-child) td:nth-child(2) div").editable(
                cfdbEditUrl,
                {
                    type: 'textarea',
                    submit: 'OK',
                    indicator: '<img alt="Saving..." src="' + loadImg + '"/>',
                    height: '50px',
                    placeholder: '&nbsp;',
                    select: 'true',
                    ajaxoptions: {
                        cache: false
                    },
                    loadurl: cfdbGetValueUrl
                })
    })(jQuery);
}
