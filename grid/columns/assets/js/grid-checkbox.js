$(document).on('click', '#' + gridID + ' .select-on-check-all', function () {
    var checked = this.checked;
    $('input[name=\"selection[]\"]').each(function () {
        $('input[name=\"selection[]\"]:checkbox').prop('checked', checked);
    });

    grid_selections = $('#' + gridID).yiiGridView('getSelectedRows');
});


$(document).on('click', '#' + gridID + ' input[name=\"selection[]\"]', function (e) {
    grid_selections = $('#' + gridID).yiiGridView('getSelectedRows');
});

$(document).on('click', '.grid-action', function (e) {
    grid_selections = $('#' + gridID).yiiGridView('getSelectedRows');
    var url = $(this).attr('href');
    console.log('dsa', grid_selections);
    if (grid_selections.length > 0) {


        yii.confirm($(this).data('confirm-info'), function () {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {id: grid_selections},
                success: function (data) {
                    if (data.success) {
                        common.notify(data.message, 'success');
                        $.pjax.reload('#pjax-' + gridID, {timeout: false});
                    } else {
                        common.notify(data.message, 'error');
                    }
                }
            });
        }, function () {
            return false;
        });
    }
    e.preventDefault();
    return false;
});


function gridAction2(that) {
    var keys = $('#' + gridID).yiiGridView('getSelectedRows');
    var url = $(that).attr('href');
    console.log(keys);
    return false;

    if (keys.length > 0) {


        //  if (confirm($(that).data('confirm')+'s')) {
        //     confirmed = true;
        // }
        // if (confirmed) {

        return false;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {id: grid_selections},
            success: function (data) {
                if (data.success) {
                    common.notify(data.message, 'success');
                    $.pjax.reload('#pjax-' + gridID, {timeout: false});
                } else {
                    common.notify(data.message, 'error');
                }
            }
        });
        // }
    } else {
        common.notify('Не выбрано не одного элемента!', 'warning');
    }
    return false;
}