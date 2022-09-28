/**
 * Compatibility with ajax popup for JetTheme
 */

define([
    'jquery',
    'ko',
    'uiRegistry'
], function ($, ko, uiRegistry) {

    /**
     * Params validation
     *
     * @param {object} context
     * @param {string} selector
     *
     * @return {boolean}
     */
    function isParamsValid(context, selector)
    {
        return typeof context === 'object' && $(selector).length;
    }

    /**
     * Refresh ko binding on the element
     *
     * @param {object} context
     * @param {string} selector
     *
     * @return {void}
     */
    function refreshBinding(context, selector)
    {
        ko.cleanNode($(selector)[0]);
        ko.applyBindings(context, $(selector)[0]);
    }

    /**
     * Clear node and apply actual ko bindings for opened popup
     *
     * @param {object} context
     * @param {string} selector
     *
     * @return {void}
     */
    function setOnContentUpdatedEvent(context, selector)
    {
        $('body').on('popup.amContentUpdated', function () {
            refreshBinding(context, selector);
        });
    }

    /**
     * Update knockout content
     *
     * @param {object} context
     * @param {string} selector
     * @param {string} componentName
     *
     * @return {void}
     */
    function initCompatibility(context, selector, componentName)
    {
        if (isParamsValid(context, selector)) {
            refreshBinding(context, selector);

            $('body').on('popup.amPopupDestroy', function () {
                uiRegistry.remove(componentName);
            });
        } else {
            setOnContentUpdatedEvent(context, selector);
        }
    }

    /**
    * @return {object}
    */
    return {
        initCompatibility: initCompatibility
    }
})
