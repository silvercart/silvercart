(function($) {
    $('.col-add-sub-object.action').live('click', function(event) {
        if (($(event.target).hasClass('action') &&
             $(event.target).hasClass('add-sub-object-button')) ||
            $('button.action.add-sub-object-button').has(event.target).length > 0) {
            var button;
            if ($(event.target).hasClass('action') &&
                $(event.target).hasClass('add-sub-object-button')) {
                button = event.target;
            } else {
                button = $('button.action.add-sub-object-button').has(event.target);
            }
            var recordID = $(button).attr('data-select-target');
            $('input[name="SubObjectParentID"]').val(recordID);
        } else {
            event.preventDefault();
            return false;
        }
    });
    $('.sub-list-record-action').live('click', function() {
        var subList       = $(this).closest('.sub-list'),
            subListRecord = $(this).closest('.sub-list-record'),
            subListRecordAction = $(this),
            targetURL     = subList.data('target-url'),
            actionID      = $(this).data('action-id'),
            actionName    = $(this).data('action-name'),
            parentID      = subList.data('parent-record-id'),
            recordID      = $(this).data('record-id'),
            recordTitle   = $('.title', subListRecord).html(),
            actionStateID = 'action_gridFieldAlterAction?StateID=' + actionID,
            targetData    = {};
            targetData[actionStateID]           = "";
            targetData["SubObjectID"]        = recordID;
            targetData["SubObjectParentID"]  = parentID;
            targetData["SecurityID"]         = $('input[name="SecurityID"]').val();
            
        if (subListRecord.hasClass('action-in-progress')) {
            return;
        }
        subListRecord.addClass('action-in-progress');
        $.post(targetURL, targetData, function(data, textStatus) {
            if (textStatus === 'success') {
                if (actionName === 'remove') {
                    successRemove(subListRecordAction, parentID, recordID, recordTitle, subListRecord);
                } else if (actionName === 'activate' ||
                           actionName === 'deactivate' ||
                           actionName === 'default' ||
                           actionName === 'undefault') {
                    successButtonSwitch(subListRecordAction, parentID, recordID, recordTitle, subListRecord);
                }
                subListRecord.removeClass('action-in-progress');
            }
        });
    });

    var successRemove = function(button, parentID, recordID, recordTitle, subListRecord) {
        var subList    = $(subListRecord).closest('.sub-list'),
            fieldName  = subList.data('field-name'),
            selectList = $('select[name="' + fieldName + 'SubObjects\[' + parentID + '\]"]');
        selectList.append('<option value="' + recordID + '">' + recordTitle + '</option>');

        var options = $('option', selectList);
        var arr = options.map(function (_, o) {
            return {t: $(o).text(), v: o.value};
        }).get();
        arr.sort(function (o1, o2) {
            return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0;
        });
        options.each(function (i, o) {
            o.value = arr[i].v;
            $(o).text(arr[i].t);
        });

        subListRecord.fadeOut('slow');
    };
    var successButtonSwitch = function(button, parentID, recordID, recordTitle, subListRecord) {
        var alterActionName = button.data('alter-action-name'),
            alterActionID   = button.data('alter-action-id'),
            alterIcon       = button.data('alter-icon'),
            alterTitle      = button.data('alter-title'),
            actionName      = button.data('action-name'),
            actionID        = button.data('action-id'),
            icon            = button.data('icon'),
            title           = button.attr('title');

            button.data('alter-action-name', actionName);
            button.data('alter-action-id', actionID);
            button.data('alter-icon', icon);
            button.data('alter-title', title);
            button.data('action-name', alterActionName);
            button.data('action-id', alterActionID);
            button.data('icon', alterIcon);
            button.attr('title', alterTitle);

            button.removeClass('font-icon-' + icon);
            button.addClass('font-icon-' + alterIcon);

            if (actionName === 'activate') {
                subListRecord.removeClass('active0');
                subListRecord.addClass('active1');
            } else if (actionName === 'deactivate') {
                subListRecord.removeClass('active1');
                subListRecord.addClass('active0');
            } else if (actionName === 'default') {
                subListRecord.removeClass('default0');
                subListRecord.addClass('default1');
            } else if (actionName === 'undefault') {
                subListRecord.removeClass('default1');
                subListRecord.addClass('default0');
            }
    };

    var scLoadSubObjectsRepeat = 0,
        scLoadSubObjects = function() {
            var currentRun = 0,
                maxRuns    = 20,
                interval   = setInterval(function() {
                    if ($('.sub-object-lists .sub-list').length > 0) {
                        clearInterval(interval);
                        $('.sub-object-lists').each(function() {
                            var subObjectLists      = $(this),
                                targetGridFieldName = $(this).attr('data-target-gridfield'),
                                targetGridField     = $('.grid.field[data-name="' + targetGridFieldName + '"]'),
                                targetGridFieldRows = $('table tbody tr', targetGridField);

                            targetGridFieldRows.each(function() {
                                var currentRow = $(this),
                                    recordID   = $(this).attr('data-id'),
                                    subList    = $('.sub-list[data-parent-record-id="' + recordID + '"]', subObjectLists);
                                if (subList.length > 0) {
                                    var evenOdd = currentRow.hasClass('even') ? 'even' : 'odd',
                                        subRow  = '<tr class="sub-row ' + evenOdd + '"><td colspan="' + $('td', currentRow).length + '">' + subList.wrap('<div>').parent().html(); + '</td></tr>';
                                    subList.unwrap();
                                    subList.remove();
                                    currentRow.after(subRow);
                                }
                            });
                        });
                        if (scLoadSubObjectsRepeat > 0) {
                            scLoadSubObjectsRepeat--;
                            setTimeout(scLoadSubObjects, 400);
                        }
                    }
                    if (currentRun >= maxRuns) {
                        clearInterval(interval);
                        if (scLoadSubObjectsRepeat > 0) {
                            scLoadSubObjectsRepeat--;
                            setTimeout(scLoadSubObjects, 400);
                        }
                    }
                    currentRun++;
            }, 200);
    };
    scLoadSubObjects();
    setInterval(function() {
        if ($('.sub-object-lists .sub-list').length > 0 &&
            $('.sub-object-lists').length > 0) {
            scLoadSubObjects();
        }
    }, 500);
})(jQuery);