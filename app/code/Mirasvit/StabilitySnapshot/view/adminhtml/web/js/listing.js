define([
    'uiComponent',
    'underscore',
    'moment'
], function (Component, _, moment) {
    'use strict';
    
    return Component.extend({
        moment: function (date) {
            return moment(date);
        },
        
        renderHealthMetric: function (metric) {
            var value = metric.value;
            var unit = metric.unit;
            
            if (value === null || value === false) {
                return '-';
            }
            
            value = parseFloat(value);
            
            if (unit === 'millisecond') {
                return (value / 1000).toFixed(1) + " <em>sec</em>" + this.renderHealthMetricDiff(metric)
            } else if (unit === 'percent') {
                return (value / 1).toFixed(1) + " <em>%</em>" + this.renderHealthMetricDiff(metric)
            } else if (unit === 'per_1000') {
                return (value / 1).toFixed(0) + " <em>%%</em>" + this.renderHealthMetricDiff(metric)
            }
        },
        
        renderHealthMetricDiff: function (metric) {
            var diff = metric.diff;
            
            if (diff === 0 || diff === null || diff === undefined) {
                return '';
            }
            diff = (diff / 1).toFixed(0);
            
            var css = "";
            
            if (metric.direction === "down") {
                css = diff > 0 ? "grow" : (diff < 0) ? "fall" : "";
            } else {
                css = diff > 0 ? "fall" : (diff < 0) ? "grow" : "";
            }
            
            css = css + " " + metric.direction;
            
            if (Math.abs(diff) > 1000) {
                diff = ">1000";
            } else {
                diff = Math.abs(diff);
            }
            
            return "<div class='diff " + css + "'>" + diff + "<em>%</em></div>";
        },
        
        renderEnvironmentData: function (obj) {
            return _.map(_.keys(obj), function (key) {
                var items = _.map(obj[key], function (item) {
                    return '<p>' + item + '</p>';
                }.bind(this)).join(' ');
                
                return '<div><b>' + key + '</b>' + items + '</div>';
            }.bind(this)).join(' ');
        }
    });
});