function escapeRegExp(str) {
	return str.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}
String.prototype.replaceAll = function(search, replacement) {
	search = escapeRegExp(search);
	var target = this;
	return target.replace(new RegExp(search, 'g'), replacement);
};
document.addEventListener("click", function(e) {
	box = document.getElementById("topProfileMenu");
	if (e.target.closest(".top-menu-item") || e.target.closest(".account-settings")) return;
	if (box === null) return;
	box.classList.toggle("dblock", false);
});
Element.prototype.remove = function() {
	this.parentElement.removeChild(this);
}
NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
	for(var i = this.length - 1; i >= 0; i--) {
		if(this[i] && this[i].parentElement) {
			this[i].parentElement.removeChild(this[i]);
		}
	}
}

function getCookie(name) {
	var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}
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
var getGET = (name) => {
	if (name = (new RegExp('[?&]' + encodeURIComponent(name) + '=([^&]*)')).exec(location.search)) {
		return decodeURIComponent(name[1]);
	}
}
var goTo = (to) => {
	window.open(to, "_self");
}
var openPage = (page) => {
	goTo(URL + page);
}
function openTopProfileMenu() {
	document.getElementById("topProfileMenu").classList.toggle("dblock");//.classList.toggle("dblock");
}