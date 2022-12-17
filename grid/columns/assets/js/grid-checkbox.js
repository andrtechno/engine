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
function checkSelected() {
    var selection = $('#' + gridID).yiiGridView('getSelectedRows');
    if (!selection.length) {
        common.notify('Не выбрано не одного элемента!', 'warning');
        return false;
    }
    return selection;
}
$(document).on('click', '.grid-action', function (e) {
    grid_selections = $('#' + gridID).yiiGridView('getSelectedRows');
    var url = $(this).attr('href');
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
