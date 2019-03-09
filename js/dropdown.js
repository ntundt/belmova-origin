var selected;

function onDropdownSelect(elem) {
	var selector = document.getElementById("typeSelector");
	var final = "";
	selector.innerText = elem.innerText;
	var nums = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
	for (i = 0; i < elem.id.length; i++) {
		if (inArray(elem.id.charAt(i), nums)) {
			final += elem.id.charAt(i);
		} else {
			if (0 < final.length) {
				if ("_" != final.charAt(final.length - 1)) {
					final += "_";
				}
			}
		}
	}
	setSelected(final);
}

class DropdownSelector {
	constructor(element, parameters) {
		this.parameters = parameters;
		this.selectorContainer = element;
		this.selected = null;
		this.drawSelector();
	}
	drawSelector() {
		this.selectorContainer.classList.toggle("dropdown-act-select", true);
		var dropfield = document.createElement("div");
		dropfield.classList.toggle("dropfield", true);
		this.selectorContainer.appendChild(dropfield);
		var dropdownContent = document.createElement("div");
		dropdownContent.classList.toggle("dropdown-content-selector", true);
		this.selectorContainer.appendChild(dropdownContent);
		for (var i = 0; i < this.parameters.length; i++) {
			var element = document.createElement("div");
			element.classList.toggle("dropdown-elem", true);
			element.innerText = this.parameters[i].value;
			element.id = this.parameters[i].key;
			dropdownContent.appendChild(element);
		}
	}
	getSelected() {
		
	}
}