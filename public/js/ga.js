var trackerId = 5;

function addEvent(obj, evType, fn, useCapture){
	if (obj.addEventListener) {
		obj.addEventListener(evType, fn, useCapture);
		return true;
	} else if (obj.attachEvent){
		var r = obj.attachEvent('on' + evType, fn);
		return r;
	} else {
		//alert('Handler could not be attached');
	}
}

function callGA() {
    var s2 = document.createElement('script');
    s2.setAttribute('type', 'text/javascript');
    s2.text = 'var pageTracker = _gat._getTracker("UA-2398949-' + trackerId + '"); pageTracker._initData(); pageTracker._trackPageview();';
    document.getElementsByTagName('body').item(0).appendChild(s2);
}

function loadGA() {
    var s1 = document.createElement('script');
    s1.setAttribute('id', 'googleanalytics');
    s1.setAttribute('src', 'http://www.google-analytics.com/ga.js');
    s1.setAttribute('type', 'text/javascript');

	addEvent(s1, 'readystatechange', function () {
	    if ((s1.readyState == 'complete') || (s1.readyState == 'loaded')) {
		callGA();
	    }
	});

    addEvent(s1, 'load', callGA);
    document.getElementsByTagName('head').item(0).appendChild(s1);

    var links = document.getElementsByTagName('a');
    for (var i = 0; i < links.length; i++) {
        if (links[i].rel == 'external') {
            links[i].onclick = function() {
                window.open(this.href);
                return false;
            };
        }
    }
}

addEvent(window, 'load', loadGA);