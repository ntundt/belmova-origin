function goOut() {
	document.cookie = "sid=a;expires=Thu, 01 Jan 1970 00:00:01 GMT";
	goToMainPage();
}
function goToMainPage() {
	window.open("http://localhost/index", "_self")
}
function goLastPage() {
	// window.onbeforeunload = function() { return "You work will be lost."; };
}