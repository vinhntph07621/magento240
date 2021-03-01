define([
    'jquery',
    'jquery/ui',
    'uiLayout',
    'uiComponent',
    'uiRegistry'
], function ($, ui, layout, Component, registry) {
    'use strict';
    
    return Component.extend({
        defaults: {
            status:      'new',
            refreshUrl:  null,
            id:          null,
            size:        0,
            limit:       200,
            step:        null,
            stepStatus:  null,
            steps:       {},
            debug:       false,
            isStopped:   false,
            forceLimit:  false,
            lastOrderId: null
        },
        
        /**
         * Initializes component.
         *
         * @returns {Object} Chainable.
         */
        initialize: function () {
            this._super();
            _.bindAll(this,
                'sendRequest',
                'handleResponse',
                'stop'
            );
            
            return this;
        },
        
        /**
         * Run segment data refresh process.
         */
        refresh: function () {
            this.log('refresh started');
            
            var self = this;
            this.isStopped = false;
            
            this.showProgress();
            
            this.sendRequest()
                .done(this.handleResponse)
                .fail(function (response) {
                    self.progress.setProgress({error: response.responseText});
                    self.reset();
                });
        },
        
        /**
         * Show refresh progress popup.
         */
        showProgress: function () {
            if (!this.progress) {
                this.progress = registry.get(this.name + '.progress');
            }
            
            this.progress.setRefresher(this);
            this.progress.show('show');
        },
        
        /**
         * Send request to refresh segment data.
         */
        sendRequest: function () {
            // remove duplicated parameters from URL related to other functionality
            var refreshUrl = this.refreshUrl.replace('limit/', '');
            // merge params with step statuses
            var data = $.extend({
                id:            this.id,
                status:        this.status,
                size:          this.size,
                limit:         this.limit,
                step:          this.step,
                step_status:   this.stepStatus,
                last_order_id: this.lastOrderId
            }, this.steps);
            
            return $.ajax(refreshUrl, {
                method:   'GET',
                dataType: 'json',
                data:     data
            });
        },
        
        /**
         * Handle refresh segment data response from server.
         */
        handleResponse: function (response) {
            var self = this;
            this.log(response);
            
            try {
                if (response.success) {
                    if (response.status == 'completed') {
                        this.progress.setProgress(response.progress);
                        this.reset();
                    } else {
                        this.progress.setProgress(response.progress);
                        this.status = response.status;
                        this.size = response.size;
                        this.step = response.step;
                        this.stepStatus = response.step_status;
                        this.lastOrderId = response.last_order_id;
                        
                        // change limit
                        if (response.limit && !this.forceLimit) {
                            this.limit = response.limit;
                        }
                        
                        // save step statuses
                        if (response.progress && response.progress.steps) {
                            $.each(response.progress.steps, function (index, step) {
                                self.steps[step.code] = step.status;
                            });
                        }
                        
                        if (!this.isStopped) {
                            this.refresh();
                        }
                    }
                } else {
                    this.progress.setProgress({error: response.error});
                }
            } catch (error) {
                this.log(error);
                this.progress.setProgress({error: response.responseJSON});
                this.reset();
            }
        },
        
        /**
         * Reset refresh parameters.
         */
        reset: function () {
            this.size = 0;
            this.status = 'new';
            this.step = null;
            this.stepStatus = null;
            this.steps = {};
            this.lastOrderId = null;
        },
        
        /**
         * Print message if debugging mode enabled.
         */
        log: function (msg) {
            if (this.debug) {
                console.log(msg);
            }
        },
        
        /**
         * Stop sending requests and reset execution.
         */
        stop: function () {
            this.isStopped = true;
            this.progress.setProgress({});
        }
    });
});
