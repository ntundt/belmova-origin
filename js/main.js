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