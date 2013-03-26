var checkForUploadedSilvercartFiles = function() {
    var $ = jQuery;
    var reloadFileGridField = function(classname) {
        var fileUploadField = $('ul.ss-uploadfield-files.files li').closest('.field.' + classname),
            fileGridField = $('.ss-gridfield[data-name="' + fileUploadField.attr('id').replace(/Upload/gi, '') + '"]');
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
    };
    if (checkForUploadedSilvercartFilesLength < $('.field.silvercartfileupload ul.ss-uploadfield-files.files li').length) {
        checkForUploadedSilvercartFilesLength = $('.field.silvercartfileupload ul.ss-uploadfield-files.files li').length;
        reloadFileGridField('silvercartfileupload');
    }
    if (checkForUploadedSilvercartImagesLength < $('.field.silvercartimageupload ul.ss-uploadfield-files.files li').length) {
        checkForUploadedSilvercartImagesLength = $('.field.silvercartimageupload ul.ss-uploadfield-files.files li').length;
        reloadFileGridField('silvercartimageupload');
    }
};
if (typeof checkForUploadedSilvercartFilesInterval == 'undefined') {
    var checkForUploadedSilvercartFilesInterval    = setInterval('checkForUploadedSilvercartFiles();', 5000);
    var checkForUploadedSilvercartFilesLength      = 0;
    var checkForUploadedSilvercartImagesLength     = 0;
}