<?php
print "\n<!--  BEGIN include validation-functions -->\n";
function verifyAlphaNum ($testString) {
	return (preg_match ("/^([[:alnum:]]|-|\.| |)+$/", $testString));
}

function verifyEmail ($testString) {
	return filter_var($testString, FILTER_VALIDATE_EMAIL);
}

function verifyNumeric ($testString) {
	return (is_numeric($testString));
}

function verifyPhone ($testString) {
        $regex = '/^(?:1(?:[. -])?)?(?:\((?=\d{3}\)))?([2-9]\d{2})(?:(?<=\(\d{3})\))? ?(?:(?<=\d{3})[.-])?([2-9]\d{2})[. -]?(\d{4})(?: (?i:ext)\.? ?(\d{1,5}))?$/';
	return (preg_match($regex, $testString));
}
function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
function verifyTime ($testTime) {
        $regex = '/^([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/';
	return (preg_match($regex, $testTime));
}
function verifyPassword ($password){
    $regex = "#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#";
    return(preg_match($regex, $password));
}
print "\n<!--  END include validation-functions -->\n";
?>