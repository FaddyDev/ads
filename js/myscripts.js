// JavaScript Document
function numbersonly(e){
    var unicode=e.charCode? e.charCode : e.keyCode
    if (unicode!=8 & unicode!=9 & unicode!=13 & unicode!=46 & unicode!=37 & unicode!=39 ){ //if the key isn't the backspace,tab,enter,delete,left and right arrow keys (which we should allow)
        if (unicode<48||unicode>57) //if not a number
            return false //disable key press
    }
	}