/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Reports
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        gridstack: 'Mageplaza_ReportCustomize/js/lib/gridstack',
        gridstackJqueryUi: 'Mageplaza_ReportCustomize/js/lib/gridstack.jQueryUI',
        daterangepicker: 'Mageplaza_ReportCustomize/js/lib/daterangepicker.min',
        chartBundle: 'Mageplaza_ReportCustomize/js/lib/Chart.bundle.min',
        initDateRange: 'Mageplaza_ReportCustomize/js/dashboard/initDateRange',
        initGridStack: 'Mageplaza_ReportCustomize/js/dashboard/initGridStack',
        initChart: 'Mageplaza_ReportCustomize/js/dashboard/initChart',
    },
    map: {
        gridstack: {
            'lodash': 'underscore'
        },
        gridstackJqueryUi: {
            'lodash': 'underscore'
        },
        '*': {
            moment: 'Mageplaza_ReportCustomize/js/lib/moment.min'
        }
    }
};
