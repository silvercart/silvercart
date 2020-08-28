var checkForUploadedFiles = function() {
    var $ = jQuery;
    var reloadFileGridField = function(classname, uploadListSelector) {
        var fileUploadField = $(uploadListSelector).closest('.field.' + classname),
            gridFieldName = fileUploadField.attr('id').replace($(uploadListSelector).closest('form').attr('id') + '_', '').replace(/Upload/gi, '').replace(/_Holder/gi, ''),
            fileGridField = $('.ss-gridfield[data-name="' + gridFieldName + '"]');
        if (fileGridField.length > 0) {

            $.entwine('ss', function($) {
                fileGridField.entwine({
                    /**
                     * @param {Object} Additional options for jQuery.ajax() call
                     * @param {successCallback} callback to call after reloading succeeded.
                     */

                    reload: function(ajaxOpts, successCallback) {
                        var self = this, form = this.closest('form'), 
                            focusedElName = this.find(':input:focus').attr('name'), // Save focused element for restoring after refresh
                            data = form.find(':input').serializeArray();

                        if(!ajaxOpts) ajaxOpts = {};
                        if(!ajaxOpts.data) ajaxOpts.data = [];
                        ajaxOpts.data = ajaxOpts.data.concat(data);


                        // Include any GET parameters from the current URL, as the view state might depend on it.
                        // For example, a list prefiltered through external search criteria might be passed to GridField.
                        if(window.location.search) {
                            ajaxOpts.data = window.location.search.replace(/^\?/, '') + '&' + $.param(ajaxOpts.data);
                        }

                        form.addClass('loading');

                        $.ajax($.extend({}, {
                            headers: {"X-Pjax" : 'CurrentField'},
                            type: "POST",
                            url: this.data('url'),
                            dataType: 'html',
                            success: function(data) {
                                // Replace the grid field with response, not the form.
                                // TODO Only replaces all its children, to avoid replacing the current scope
                                // of the executing method. Means that it doesn't retrigger the onmatch() on the main container.
                                self.empty().append($(data).children());

                                // Refocus previously focused element. Useful e.g. for finding+adding
                                // multiple relationships via keyboard.
                                if(focusedElName) self.find(':input[name="' + focusedElName + '"]').focus();

                                var content;
                                if(ajaxOpts.data[0].filter=="show"){	
                                    content = '<span class="non-sortable"></span>';
                                    self.addClass('show-filter').find('.filter-header').show();														
                                }else{
                                    content = '<button name="showFilter" class="ss-gridfield-button-filter trigger"></button>';
                                    self.removeClass('show-filter').find('.filter-header').hide();	
                                }

                                self.find('.sortable-header th:last').html(content);

                                form.removeClass('loading');
                                if(successCallback) successCallback.apply(this, arguments);
                                self.trigger('reload', self);
                            },
                            error: function(e) {
                                alert(ss.i18n._t('GRIDFIELD.ERRORINTRANSACTION'));
                                form.removeClass('loading');
                            }
                        }, ajaxOpts));
                    },
                    showDetailView: function(url) {
                        window.location.href = url;
                    },
                    getItems: function() {
                        return this.find('.ss-gridfield-item');
                    },
                    /**
                     * @param {String}
                     * @param {Mixed}
                     */
                    setState: function(k, v) {
                        var state = this.getState();
                        state[k] = v;
                        this.find(':input[name="' + this.data('name') + '[GridState]"]').val(JSON.stringify(state));
                    },
                    /**
                     * @return {Object}
                     */
                    getState: function() {
                        return JSON.parse(this.find(':input[name="' + this.data('name') + '[GridState]"]').val());
                    }
                });
            });

            fileGridField.reload();
        }
    },
    attachFiles = function(fieldSelector) {
        var addRelations = [], scImageUploadField = $('input.' + fieldSelector, $('.field.' + fieldSelector + ' .uploadfield-item').closest('.uploadfield-holder').closest('.form__field-holder'));
        if (scImageUploadField.length > 0) {
            var schema   = scImageUploadField.data('schema'),
                endpoint = schema.data.attachFileEndpoint.url;
            $('.field.' + fieldSelector + ' .uploadfield-item').each(function() {
                if ($('.uploadfield-item__complete-icon', this).length === 0
                 && $('.uploadfield-item__upload-progress', this).length === 0
                ) {
                    addRelations.push($('input', this).val());
                    $('.uploadfield-item__details', this).after('<div class="uploadfield-item__upload-progress"><div class="progress h-100"><div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div></div></div>');
                }
            });
        }
        if (addRelations.length > 0) {
            var queryString = '';
            addRelations.forEach(function(id) {
                queryString += 'ids[]=' + id + '&';
            });
            $.ajax({
                type: "POST",
                url:  endpoint,
                data: queryString,
                success: function(data) {
                    addRelations.forEach(function(id) {
                        var item = $('.uploadfield-item input[value="' + id + '"]').closest('.uploadfield-item');
                        $('.uploadfield-item__upload-progress', item).remove();
                        $('.uploadfield-item__details', item).after('<div class="uploadfield-item__complete-icon"></div>');
                    });
                    reloadFileGridField(fieldSelector, '.field.' + fieldSelector + ' .uploadfield-item');
                },
            });
        } else {
            reloadFileGridField(fieldSelector, '.field.' + fieldSelector + ' .uploadfield-item');
        }
    };
    
    if (checkForUploadedFilesLength < $('.field.sc-fileuploadfield .uploadfield-item').length) {
        checkForUploadedFilesLength = $('.field.sc-fileuploadfield .uploadfield-item').length;
        attachFiles('sc-fileuploadfield');
    } else if (checkForUploadedFilesLength > $('.field.sc-fileuploadfield .uploadfield-item').length) {
        checkForUploadedFilesLength = $('.field.sc-fileuploadfield .uploadfield-item').length;
    }
    if (checkForUploadedImagesLength < $('.field.sc-imageuploadfield .uploadfield-item').length) {
        checkForUploadedImagesLength = $('.field.sc-imageuploadfield .uploadfield-item').length;
        attachFiles('sc-imageuploadfield');
    } else if (checkForUploadedImagesLength > $('.field.sc-imageuploadfield .uploadfield-item').length) {
        checkForUploadedImagesLength = $('.field.sc-imageuploadfield .uploadfield-item').length;
    }
};
if (typeof checkForUploadedFilesInterval == 'undefined') {
    var checkForUploadedFilesInterval    = setInterval('checkForUploadedFiles();', 5000);
    var checkForUploadedFilesLength      = 0;
    var checkForUploadedImagesLength     = 0;
}