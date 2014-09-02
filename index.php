<?php include('../../inc/header.html'); ?>
<script type="text/javascript">
var running = false;
var queued = false;

/* Update the output or wait for a previous update to finish
   then update it. There is no need to have multiple instances
   waiting simultaneously, though, since they would all do
   the same thing. Just in case someone types faster than
   their text can be encoded. */
function showOutput() {
    if(running) {
	if(queued) {
	    /* We are already waiting to update the output. */
	    return;
	}
	/* Update the output after the previous update finishes. Try again in a second. */
	queued = true;
	setTimeout(doShowOutput,1000);
	return;
    }
    doShowOutput();
}

function doShowOutput() {
    if(running) {
	/* Previous update still running, wait another second. */
	setTimeout(doShowOutput,1000);
	return;
    }
    running = true;
    queued = false;
    var sel = document.getElementById('fmt');
    var fmt = sel.options[sel.selectedIndex].value;
    var out = '';
    var text = document.getElementById('text').value;
    for(var i=0;i<text.length;i++) {
	/* this loop iterates for each character/byte */
	var val = text.charCodeAt(i);
	if(out!='') {
	    /* separate each byte with a space in the output for readability */
	    out+=' ';
	}
	if(fmt=='hex') {
	    var byte = '';
	    /* 2 hex digits in a byte. Start with the least significant (right to left). */
	    for(var h=0;h<2;h++) {
		var digit = val % 16;
		val = Math.floor(val / 16);
		if(digit<10) {
		    /* numerical value is equal to hex value */
		    byte = digit.toString() + byte;
		}
		else {
		    /* hex letter equivalent of numerical value */
		    byte = String.fromCharCode(55 + digit) + byte;
		}
	    }
	    out+= byte;
	}
	else if(fmt=='bin') {
	    var byte = '';
	    /* 8 bits in a byte. Start with the least significant (right to left). */
	    for(var d=0;d<8;d++) {
		var bit = val % 2;
		byte = bit.toString()+byte;
		val = Math.floor(val / 2);
	    }
	    out+= byte;
	}
	else {
	    /* numerical byte representation is easy */
	    out+= val.toString();
	}
    }
    document.getElementById('output').value = out;
    running = false;
}
window.onload = function() {
    showOutput();
    document.getElementById('text').onkeyup = showOutput;
    document.getElementById('fmt').onchange = showOutput;
};
</script>
<h2>Text Encoder</h2>
output: <select id="fmt">
<option value="hex">Hexadecimal</option>
<option value="byte">Numerical Bytes</option>
<option value="bin">Binary</option>
</select>
<h3>Original Text</h3>
<textarea rows="6" cols="50" id="text">enter text here</textarea>
<h3>Output</h3>
<textarea rows="6" cols="50" id="output" disabled="disabled"></textarea>
<?php include('../../inc/footer.html'); ?>
