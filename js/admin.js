function AJAXRequest(url, params, readyStateFunc) {
    http_request = false;
    if (window.XMLHttpRequest) { // Mozilla, Safari,...
        http_request = new XMLHttpRequest();

        if (http_request.overrideMimeType) {
            // set type accordingly to anticipated content type
            //http_request.overrideMimeType('text/xml');
            http_request.overrideMimeType('text/html');
        }
    } else if (window.ActiveXObject) { // IE
        try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
        }
    }

    if (!http_request) {
        //alert('Cannot create XMLHTTP instance');
        return false;
    }

    http_request.onreadystatechange = readyStateFunc;
    http_request.open('POST', url, true);
    http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http_request.setRequestHeader("Content-length", params.length);
    http_request.setRequestHeader("Connection", "close");
    http_request.send(params);
}

function addBill() {
    var newId = document.getElementById('next_bill').value;
    var addLink = document.getElementById('bill_link');
    var billFieldset = addLink.parentNode;

    var billRow = document.createElement('span');
    billRow.setAttribute('class', 'billrow');
    billRow.className = 'billrow';

    var priceLabel = document.createElement('label');
    priceLabel.setAttribute('for', 'bill_' + newId + '_price');
    priceLabel.setAttribute('class', 'forprice');
    priceLabel.className = 'forprice';
    priceLabel.innerHTML = '&pound;';
    billRow.appendChild(priceLabel);

    var priceInput = document.createElement('input');
    priceInput.setAttribute('id', 'bill_' + newId + '_price');
    priceInput.setAttribute('type', 'text');
    priceInput.setAttribute('class', 'bill_price');
    priceInput.className = 'bill_price';
    priceInput.setAttribute('name', 'bill[' + newId + '][price]');
    billRow.appendChild(priceInput);

    var descLabel = document.createElement('label');
    descLabel.setAttribute('for', 'bill_' + newId + '_desc');
    descLabel.setAttribute('class', 'bill_desc');
    descLabel.className = 'bill_desc';
    descLabel.appendChild(document.createTextNode('Description'));
    billRow.appendChild(descLabel);

    var descInput = document.createElement('input');
    descInput.setAttribute('id', 'bill_' + newId + '_desc');
    descInput.setAttribute('type', 'text');
    descInput.setAttribute('name', 'bill[' + newId + '][desc]');
    billRow.appendChild(descInput);

    var removeLink = document.createElement('a');
    removeLink.setAttribute('href', '#');
    removeLink.setAttribute('class', 'remove_link');
    removeLink.className = 'remove_link';
    addEvent(removeLink, 'click', function (ev) {
        if (ev.preventDefault) { ev.preventDefault(); } else { ev.returnValue = false; }
        removeRow(removeLink);
    });
    removeLink.innerHTML = 'remove';
    billRow.appendChild(removeLink);

    billRow.appendChild(document.createElement('br'));
    billFieldset.insertBefore(billRow, addLink);

    document.getElementById('next_bill').value = parseInt(newId) + 1;
    document.getElementById('save').disabled = false;
    return false;
}

function addFeature() {
    var newId = document.getElementById('next_feature').value;
    var addLink = document.getElementById('feature_link');
    var featureFieldset = addLink.parentNode;

    var featureRow = document.createElement('span');
    featureRow.setAttribute('class', 'featurerow');
    featureRow.className = 'featurerow';

    var featureInput = document.createElement('textarea');
    featureInput.setAttribute('name', 'feature[' + newId + ']');
    featureRow.appendChild(featureInput);

    var featurePreview = document.createElement('div');
    featurePreview.setAttribute('id', 'preview_' + newId);
    featurePreview.setAttribute('class', 'feature');
    featurePreview.className = 'feature';
    featureRow.appendChild(featurePreview);

    var removeLink = document.createElement('a');
    removeLink.setAttribute('href', '#');
    removeLink.setAttribute('class', 'remove_link');
    removeLink.className = 'remove_link';
    addEvent(removeLink, 'click', function (ev) {
        if (ev.preventDefault) { ev.preventDefault(); } else { ev.returnValue = false; }
        removeRow(removeLink);
    });
    removeLink.innerHTML = 'remove';
    featureRow.appendChild(removeLink);

    featureRow.appendChild(document.createElement('br'));
    featureFieldset.insertBefore(featureRow, addLink);

    document.getElementById('next_feature').value = parseInt(newId) + 1;
    flipPreviewSaveButtons(true);
    return false;
}

function removeRow(item) {
    var row = item.parentNode;
    row.parentNode.removeChild(row);
    return false;
}

var http_request = false;
function previewFeatures() {

    var parameters = '';
    var features = document.getElementsByTagName('textarea');
    for (var i = 0; i < features.length; i++) {
        var feature = features[i],
            featureName = feature.name.substr(8, feature.name.length - 9);

        if (feature.id.substr(0, 6)  == 'house_') {
            featureName = feature.id;
        }

        parameters += encodeURI('&preview_' + featureName + '=' + feature.value);
    }

    AJAXRequest('/admin/markup.php', parameters, displayPreviews);
}

function flipPreviewSaveButtons(mustPreview) {
    document.getElementById('save').disabled = mustPreview;
    document.getElementById('preview').disabled = !mustPreview;
}

function displayPreviews() {
    if (http_request.readyState == 4) {
        if (http_request.status == 200) {
            var previews = eval('(' + http_request.responseText + ')');
            for (preview_id in previews) {
                document.getElementById(preview_id).innerHTML = previews[preview_id];
            }
            flipPreviewSaveButtons(false);
        } else {
            alert('There was a problem with the feature previews.');
        }
    }
}

function showSetPropertyAsRentedConfim() {
    var confirm = document.getElementById('rented');
    confirm.setAttribute('class', 'info');
    confirm.className = 'info';

    var confirmHeading = document.createElement('h4');
    confirmHeading.innerHTML = 'Confirm this property as rented';
    confirm.appendChild(confirmHeading);

    var confirmExplain = document.createElement('p');
    confirmExplain.innerHTML = 'This property will be marked as rented. This will place a message box on the page for this home, and will disable the email link.';
    confirm.appendChild(confirmExplain);

    var confirmAsk = document.createElement('p');
    confirmAsk.innerHTML = 'Are you sure you want to continue?';
    confirm.appendChild(confirmAsk);

    var confirmYes = document.createElement('input');
    confirmYes.setAttribute('type', 'submit');
    confirmYes.setAttribute('value', 'Yes');
    confirmYes.setAttribute('id', 'yes');
    addEvent(confirmYes, 'click', function (ev) {
        document.getElementById('is_rented').value = 1;
    });
    confirm.appendChild(confirmYes);

    var confirmNo = document.createElement('input');
    confirmNo.setAttribute('type', 'button');
    confirmNo.setAttribute('value', 'No');
    confirmNo.setAttribute('id', 'no');
    addEvent(confirmNo, 'click', function (ev) {
        var setRented = document.getElementById('set_rented');
        var confirm = document.getElementById('rented');
        confirm.setAttribute('class', '');
        confirm.className = '';
        confirm.innerHTML = '';
        confirm.appendChild(setRented);
        setRented.style.display = 'block';
    });
    confirm.appendChild(confirmNo);

    document.getElementById('set_rented').style.display = 'none';
}

function initialise() {
    var textboxes = document.getElementsByTagName('textarea');
    for (var i = 0; i < textboxes.length; i++) {
        addEvent(textboxes[i], 'keydown', function () {
            flipPreviewSaveButtons(true);
        });
    }

    if (document.getElementById('details')) {
        addEvent(document.getElementById('details'), 'reset', function (ev) {
            flipPreviewSaveButtons(true);
        });
    }

    if (document.getElementById('set_rented')) {
        addEvent(document.getElementById('set_rented'), 'click', function (ev) {
            showSetPropertyAsRentedConfim();
        });
    }

    if (document.getElementById('set_available')) {
        addEvent(document.getElementById('set_available'), 'click', function (ev) {
            document.getElementById('is_rented').value = 0;
        });
    }

    if (document.getElementById('bill_link')) {
        addEvent(document.getElementById('bill_link'), 'click', function (ev) {
            if (ev.preventDefault) { ev.preventDefault(); } else { ev.returnValue = false; }
            addBill();
        });
    }

    if (document.getElementById('feature_link')) {
        addEvent(document.getElementById('feature_link'), 'click', function (ev) {
            if (ev.preventDefault) { ev.preventDefault(); } else { ev.returnValue = false; }
            addFeature();
        });
    }

    if (document.getElementById('preview')) {
        addEvent(document.getElementById('preview'), 'click', function () {
            previewFeatures();
        });
    }
}

addEvent(window, 'load', initialise);
