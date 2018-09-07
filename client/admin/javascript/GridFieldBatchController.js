
(function($) {
    $('.col-batch-action-selector.action').live('click', function(event) {
        event.preventDefault();
        if ($(event.target).is('input[type="checkbox"]')) {
            scBatchActionEventTarget = $(event.target);
            setTimeout("handleScBatchActionEventTarget()", 100);
        } else {
            var checkbox = $('input[type="checkbox"]', $(event.target));
            if (checkbox.is(':checked')) {
                checkbox.attr('checked', false);
            } else {
                checkbox.attr('checked', true);
            }
        }
        return false;
    });
    $('.col-batch-action-header.check-all').live('click', function(event) {
        $('.col-batch-action-selector.action').each(function() {
            var checkbox = $('input[type="checkbox"]', $(this));
            checkbox.attr('checked', true);
        });
    });
    $('.col-batch-action-header.uncheck-all').live('click', function(event) {
        $('.col-batch-action-selector.action').each(function() {
            var checkbox = $('input[type="checkbox"]', $(this));
            checkbox.attr('checked', false);
        });
    });
    $('#action_execute_batch_action').live('click', function(event) {
        var valid       = true,
            button      = $(this).closest('.grid-batch-action-button'),
            select      = $('select', button),
            checkboxes  = $('.col-batch-action-selector.action input[type="checkbox"]', $(event.target).closest('fieldset.ss-gridfield'));
        if (select.val() === '') {
            alert(ss.i18n._t('SilvercartGridFieldBatchController.NO_ACTION_SELECTED'));
            event.preventDefault();
            valid = false;
        } else {
            var checkedAtLeastOne = false;
            $(checkboxes).each(function() {
                if ($(this).attr('checked')) {
                    checkedAtLeastOne = true;
                }
            });
            if (!checkedAtLeastOne) {
                alert(ss.i18n._t('SilvercartGridFieldBatchController.NO_ENTRY_SELECTED'));
                event.preventDefault();
                valid = false;
            }
        }
        return valid;
    });
    $('.grid-batch-action-button select.grid-batch-action-selector').live('change', function(event) {
        scBatchActionRecordName = $(event.target).closest('fieldset.ss-gridfield').attr('data-name');
        showScBatchActionLoadingBar(function() {
            $('.grid-batch-action-callback-target').html('');
            scBatchActionName   = $('.grid-batch-action-button select option:selected').val();
            var callBack        = 'silvercartBatch_' + scBatchActionName;
            if(eval('typeof ' + callBack + " == 'function'")) {
                eval(callBack + '();')
            }
            hideScBatchActionLoadingBar();
        });
    });
	$.entwine('ss', function($) {
        $('.ss-gridfield .action.gridfield-button-batch').entwine({
            onclick: function(e){
                var filterState='show'; //filterstate should equal current state.

                if(this.hasClass('ss-gridfield-button-close') || !(this.closest('.ss-gridfield').hasClass('show-filter'))){
                    filterState='hidden';
                }

                this.getGridField().reload({data: [{name: this.attr('name'), value: this.val(), filter: filterState}]}, function() {
                    var callBack    = 'silvercartBatch_' + scBatchActionName + '_successCallback';
                    if(eval('typeof ' + callBack + " == 'function'")) {
                        eval(callBack + '();')
                    }
                });
                e.preventDefault();
            }
        });
    });
    
    var showScBatchActionLoadingBar = function(callback) {
            if ($('#grid-batch-action-loading-bar').length === 0) {
                $('.grid-batch-action-button').append('<div id="grid-batch-action-loading-bar"></div>');
            }
            $('#grid-batch-action-loading-bar').fadeTo(300, 0.8, callback);
        },
        hideScBatchActionLoadingBar = function() {
            $('#grid-batch-action-loading-bar').fadeOut();
        };
})(jQuery);

var scBatchActionName               = '',
    scBatchActionRecordName         = '',
    scBatchActionEventTarget        = false,
    handleScBatchActionEventTarget  = function() {
        if (scBatchActionEventTarget !== false) {
            scBatchActionEventTarget.closest('td').trigger('click');
        }
    }
    loadScCallbackFormFromServer    = function(batchActionName) {
        var urlToGetCallbackFormFrom            = document.location.pathname,
            reversedUrlToGetCallbackFormFrom    = urlToGetCallbackFormFrom.split("").reverse().join(""),
            reversedRecordName                  = scBatchActionRecordName.split("").reverse().join("");
        if (reversedUrlToGetCallbackFormFrom.indexOf('/') > 0) {
            urlToGetCallbackFormFrom        += '/';
            reversedUrlToGetCallbackFormFrom = '/' + reversedUrlToGetCallbackFormFrom;
        }
        if (reversedUrlToGetCallbackFormFrom.indexOf('/' + reversedRecordName) !== 0) {
            urlToGetCallbackFormFrom        += scBatchActionRecordName + '/';
        }
        urlToGetCallbackFormFrom += 'handleBatchCallback';
        (function($) {
            $.ajax({
                type    : "POST",
                url     : urlToGetCallbackFormFrom,
                data    : {
                    scBatchAction : batchActionName
                },
                async   : false,
                success : function(data) {
                    $('.grid-batch-action-callback-target').html(data);
                }
            });
        })(jQuery);
    };