/*global define*/
define([
    'jquery',
    'uiComponent'
], function ($, Component) {
    return Component.extend({
        defaults: {
            voteUrl: '/faq/index/vote',
            template: 'Amasty_Faq/rating/yesno',
            id: 0,
            positiveRating: 0,
            negativeRating: 0,
            isVoted: false,
            isPositiveVoted: false
        },
        initialize: function (config) {
            this._super();
            return this;
        },
        initObservable: function () {
            this._super()
                .observe({
                    isVoted: this.isVoted,
                    positiveRating: this.positiveRating,
                    negativeRating: this.negativeRating
                });

            return this;
        },
        vote: function (isPositive) {
            if (this.isVoted()) {
                return true;
            }

            var self = this;
            $.ajax({
                url: this.voteUrl,
                data: {id: this.id, positive: isPositive, isAjax: true},
                method: 'post',
                dataType: 'json',
                success: function (responce) {
                    if (responce && responce.result.code == 'success') {
                        if (isPositive) {
                            self.isPositiveVoted = true;
                            self.positiveRating(self.positiveRating() + 1);
                        } else {
                            self.isPositiveVoted = false;
                            self.negativeRating(self.negativeRating() + 1);
                        }
                        self.isVoted(true);
                    }
                }
            });
        },
        votePositive: function () {
            this.vote(1);
        },
        voteNegative: function () {
            this.vote(0);
        },
        isNegativeVotedQuestion: function () {
            if (this.isVoted()) {
                if (!this.isPositiveVoted) {
                    return true;
                }
            }
            return false;
        },
        isPositiveVotedQuestion: function () {
            if (this.isVoted()) {
                if (this.isPositiveVoted) {
                    return true;
                }
            }
            return false;
        },
        getPositiveRating: function () {
            return this.positiveRating();
        },
        getTotalRating: function () {
            return this.positiveRating() + Math.abs(this.negativeRating());
        }
    });
});
