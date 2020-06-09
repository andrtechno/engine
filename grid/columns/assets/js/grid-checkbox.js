

            $(document).on('click','#'+gridID+' .select-on-check-all',function() {
            
                var checked=this.checked;
                
                $('input[name=\"selection[]\"]').each(function() {
                    $('input[name=\"selection[]\"]:checkbox').prop('checked',checked);
                });

                grid_selections = $('#'+gridID).yiiGridView('getSelectedRows');
                console.log(grid_selections);
            });

            
            $(document).on('click','#'+gridID+' input[name=\"selection[]\"]',function(e) {
                grid_selections = $('#'+gridID).yiiGridView('getSelectedRows');
                console.log('CHECKED',grid_selections);
            });
            
            
            function gridAction(that) {
            
                var keys = $('#'+gridID).yiiGridView('getSelectedRows');
                var url = $(that).attr('href');
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {id: grid_selections},
                    success: function (data) {
                        if(data.success){
                            common.notify(data.message,'success');
                            $.pjax.reload('#pjax-'+gridID, {timeout: false});
                        }else{
                            common.notify(data.message,'error');
                        }
                    }
                });
                return false;
            }