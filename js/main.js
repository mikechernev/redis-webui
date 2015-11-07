var ajaxCall = false,
    redisPattern = '';

setAlert = function(msg, type) {
    type = type || 'danger';
    jQuery('#key-container').html('<div class="alert alert-' + type + '" role="alert">' + msg + '</div>');
};

getKeys = function () {

    var pattern = jQuery('#redis-pattern').val();

    if (pattern.length <= 0 || redisPattern === pattern) {
        return false;
    }
    if (ajaxCall !== false) {
        ajaxCall.abort();
    }

    redisPattern = pattern;

    ajaxCall = jQuery.ajax({
        type: 'POST',
        url: 'ajax/get_keys.php',
        data: {pattern: pattern}
    }).success(function (response) {
        if (response.success !== true) {
            setAlert(response.error || 'An error occured!');
            return;
        }

        var $deleteAll = jQuery('#delete-all');
        if (response.result.length <= 0)
        {
            setAlert('No keys found for this pattern!', 'warning');
            $deleteAll.attr('disabled', 'disabled');
            return;
        }
        var $ul = jQuery('<ul>').addClass('key-list'),
            keys = response.result;

        for (var i in keys) {
            if (keys.hasOwnProperty(i)) {
                $ul.append('<li><a href="javascript:void()">' + keys[i] + '</a><div class="key-value"></div>' +
                    '<button type="button" class="btn btn-danger btn-delete">' +
                        '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>'+
                    '</button>' +
                    '</li>');
            }
        }
        jQuery('#key-container').html($ul).prepend(jQuery('<div>').addClass('result-number').text(response.result.length + ' keys found.'));
        $deleteAll.removeAttr('disabled');
    }).always(function (){
        ajaxCall = false;
    });
    return false;
};

deleteAll = function () {
    jQuery('.btn-delete').click();
    redisPattern = '';
    getKeys();

    return false;
}


jQuery('#submit-redis-pattern').click(getKeys);
jQuery('#redis-pattern').keydown(function (e) {
    if (e.which === 13) {
        redisPattern = '';
        getKeys();
        e.preventDefault();
    }
});
jQuery('#delete-all').click(deleteAll);

jQuery('#key-container').on('click', 'ul.key-list li a', function () {

    var $this = jQuery(this),
        $li = $this.parent();

    if ($li.hasClass('checked')) {
        $this.siblings('.key-value').toggle();
        // $li.removeClass('checked');
        return false;
    }

    jQuery.ajax({
        type: 'POST',
        url: 'ajax/get_key_value.php',
        data: {key: this.text}
    }).success(function (response) {
        if (response.success !== true) {
            alert(response.error || 'An error occured!');
            return;
        }
        var html = '<br>';
        if (response.result === false) {
            html += '<div class="alert alert-danger" role="alert">EMPTY</div>';
        }
        else {
            html += (typeof response.result === 'object' && typeof JSON.stringify === 'function') ? JSON.stringify(response.result) : response.result;
        }

        $li.addClass('checked')
        $this.siblings('.key-value').html(html);
    });

    return false;
});


jQuery('#key-container').on('click', 'ul.key-list li button.btn-delete', function () {

    var $link = jQuery(this).siblings('a');

    jQuery.ajax({
        type: 'POST',
        url: 'ajax/delete_key.php',
        data: {key: $link.text()}
    }).success(function (response) {
        if (response.success !== true) {
            alert(response.error || 'An error occured!');
            return;
        }
        if (response.result === false) {
            alert('Wrong Key!');
            return;
        }
        $link.parent().remove();
    });

    return false;
});

jQuery('#db-select').change(function () {
    console.log(jQuery(this).val());
    var newDb = jQuery(this).val();
    jQuery.ajax({
        type: 'POST',
        url: 'ajax/change_db.php',
        data: {db: newDb}
    }).success(function (response) {
        if (response.success !== true) {
            alert(response.error || 'An error occured!');
            return;
        }
    });

})

jQuery.ajax({
    type: 'POST',
    url: 'ajax/get_dbs.php'
}).success(function (response) {
    if (response.success !== true) {
        setAlert(response.error || 'An error occured!');
        return;
    }
    var dbs = response.result,
        $dbSelect = jQuery('#db-select'),
        selectedString;
    for (var i in dbs) {
        if (dbs.hasOwnProperty(i)) {
            selectedString = (dbs[i] === 'selected') ? 'selected' : '';
            $dbSelect.append('<option value="' + i + '" '+ selectedString+'>' + i + '</option>');
        }
    }
});
