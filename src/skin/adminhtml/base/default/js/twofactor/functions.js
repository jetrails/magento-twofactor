function changeStep ( target ) {
	var targetId = "#step-" + parseInt ( target );
	var active = document.querySelector ("a[href='" + targetId + "']");
	active.parentElement.classList.remove ("disabled");
	active.click ();
	active.parentElement.classList.add ("disabled");
	history.pushState ( {}, "", document.URL.replace ( /#.*$|$/, targetId ) );
}

function addError ( data ) {
	try {
		var data = JSON.parse ( data );
		var target = data.type == "pin" ? $("#verification-pin") : $("#backup-code");
		target.addClass ("validate").addClass ("invalid");
		target.val ( data.value );
		$( target ).parent ().find ("label").eq ( 0 ).attr ( "data-error", data.message );
		$( target ).parent ().find ("label").eq ( 0 ).data ( "error", data.message );
		target.focus ();
		$( target ).focus ( function () {
			$( this ).removeClass ("validate").removeClass ("invalid");
		});
	}
	catch ( e ) {}
}

function changeAuthType ( type ) {
	var typeFormName = type === "backup-code" ? "form-pin" : "form-code";
	var form = document.getElementById ('form');
	var newAction = form.getAttribute ('action').replace ( /#.*$|$/, '#' + typeFormName );
	form.setAttribute ( "action", newAction );
	history.pushState ( {}, "", document.URL.replace ( /#.*$|$/, '#' + typeFormName ) );
	document.getElementById ( type ).setCustomValidity ('');
	document.getElementById ( type ).value = "";
}

function changeRequired () {
	var hash = window.location.hash === "#form-code" ? "#form-code" : "#form-pin";
	document.getElementById ("backup-code").required = hash === "#form-code";
	document.getElementById ("verification-pin").required = hash === "#form-pin";
	var form = document.getElementById ("form");
	var newAction = form.getAttribute ("action").replace ( /#.*$|$/, hash );
	form.setAttribute ( "action", newAction );
}
