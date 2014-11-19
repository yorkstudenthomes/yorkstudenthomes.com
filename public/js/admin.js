jQuery(function ($) {
    var newRows = 0;

    function _needsPreview(flag) {
        return function () {
            $('#preview').attr('disabled', !flag);
            $('#save').attr('disabled', flag);
        };
    }

    // preview handling
    $('textarea').keydown(_needsPreview(true));
    $('#details').on('reset', _needsPreview(true));
    $('#preview').click(function () {
        $.post('/admin/markup', $('textarea').serialize(), function (response) {
            $.each(response, function (id, value) {
                $('#' + id).html(value);
            });
        }).done(_needsPreview(false));
    });

    // rented confirmation
    $('#confirm_rented').click(function () {
        $(this).hide().next('#confirm_dialog').show();
    });

    // rented cancel
    $('#cancel_rented').click(function () {
        $(this).closest('#confirm_dialog').hide().prev('#confirm_rented').show();
    });

    // add bill
    $('#add_bill').click(function (e) {
        e.preventDefault();

        var $lastRow = $(this).parent().prev('.dynamic-row'),
            $newRow = $lastRow.clone(),
            newId = 'new' + (++newRows);

        // update labels
        $newRow.find('.for_price').attr('for', 'bill_' + newId + '_price');
        $newRow.find('.for_desc').attr('for', 'bill_' + newId + '_desc');

        // update inputs
        $newRow.find('.bill_price').val('').removeClass('error').prop({
            id:           'bill_' + newId + '_price',
            name:         'bill[' + newId + '][price]',
            defaultValue: ''
        });
        $newRow.find('.bill_desc').val('').prop({
            id:           'bill_' + newId + '_desc',
            name:         'bill[' + newId + '][desc]',
            defaultValue: ''
        });

        $newRow.insertAfter($lastRow);
        $lastRow.closest('fieldset').find('.remove_link').show();
    });

    // add feature
    $('#add_feature').click(function (e) {
        e.preventDefault();

        var $lastRow = $(this).parent().prev('.dynamic-row'),
            $newRow = $lastRow.clone(),
            newId = 'new' + (++newRows);

        $newRow.find('textarea').val('').prop({
            id:           'feature_' + newId,
            name:         'feature[' + newId + ']',
            defaultValue: ''
        });
        $newRow.find('.feature').text('').attr('id', 'preview_' + newId);

        $newRow.insertAfter($lastRow);
        $lastRow.closest('fieldset').find('.remove_link').show();
    });

    // remove bill / feature
    $(document).on('click', '.remove_link', function (e) {
        e.preventDefault();

        var $fieldset = $(this).closest('fieldset');
        $(this).closest('.dynamic-row').remove();

        // hide final remove link
        var $links = $fieldset.find('.remove_link');
        if ($links.length == 1) {
            $links.hide();
        }
    });
});
