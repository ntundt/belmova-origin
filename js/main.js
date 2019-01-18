function goOut() {
	document.cookie = "sid=a;expires=Thu, 01 Jan 1970 00:00:01 GMT";
	goToMainPage();
}
function goToMainPage() {
	console.log(document.getElementById("goToMainPage"));
	(document.getElementById("goToMainPage")).click();
}