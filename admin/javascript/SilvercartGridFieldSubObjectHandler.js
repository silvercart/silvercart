(function($) {
    $('.col-add-sub-object.action').live('click', function(event) {
        if ($('button.action.add-sub-object-button').has(event.target).length === 0) {
            event.preventDefault();
            return false;
        } else {
            var button   = $('button.action.add-sub-object-button').has(event.target),
                recordID = $(button).attr('data-select-target');
            $('input[name="SubObjectParentID"]').val(recordID);
            scLoadSubObjectsRepeat = 1;
            setTimeout(scLoadSubObjects, 400);
        }
    });
    $('.sub-list-record-remove').live('click', function() {
        if ($(this).hasClass('action-in-progress')) {
            return;
        }
        var subList       = $(this).closest('.sub-list'),
            subListRecord = $(this).closest('.sub-list-record'),
            targetURL     = subList.attr('data-target-url'),
            actionID      = subList.attr('data-action-id'),
            parentID      = subList.attr('data-parent-record-id'),
            recordTitle   = $('.sub-list-record-title', subListRecord).html(),
            recordID      = $(this).attr('data-record-id'),
            actionName    = 'action_gridFieldAlterAction?StateID=' + actionID,
            targetData    = {};
            targetData[actionName]           = "";
            targetData["SubObjectRemovalID"] = recordID;
            targetData["SubObjectParentID"]  = parentID;
        subListRecord.addClass('action-in-progress');
        $.post(targetURL, targetData, function(data, textStatus) {
            if (textStatus === 'success') {
                var selectList = $('select[name="SilvercartProductAttributeSubObjects\[' + parentID + '\]"]');
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
            }
        });
    });
})(jQuery);

var scLoadSubObjectsRepeat = 0,
    scLoadSubObjects = function() {
    (function($) {
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
    })(jQuery);
};