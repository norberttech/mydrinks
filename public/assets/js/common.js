requirejs.config({
    deps: ['require'],
    paths: {
        jquery: '../vendor/jquery/dist/jquery',
        bootstrap: '../vendor/bootstrap/dist/js/bootstrap',
        bootstrapTypehead: '../vendor/bootstrap3-typeahead/bootstrap3-typeahead'
    },
    shim: {
        bootstrap: ['jquery']
    },
    callback: function (require) {
        'use strict';

        // Load application module.
        require(['app']);
    }
});