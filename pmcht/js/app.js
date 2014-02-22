requirejs.config({
	baseUrl: '',
	paths: {
		"text": "js/text"
	}
});

require(['js/pmcht'], function(pmcht) {
	pmcht.SMACK($("#pmcht"));
});