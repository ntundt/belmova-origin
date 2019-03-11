var element_sel_id;

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
		DropdownSelector.addSelector(this);
		this.drawSelector();
	}
	static addSelector(selector) {
		if (DropdownSelector.selectorsList === undefined) DropdownSelector.selectorsList = [];
		var selector_id = DropdownSelector.selectorsList.push(selector);
		DropdownSelector.selectorsList[selector_id - 1].id = selector_id;
		DropdownSelector.selectorsList[selector_id - 1].index = selector_id - 1;
	}
	static select(selector_index, key, placeholder_text) {
		DropdownSelector.selectorsList[selector_index].selected = key;
		DropdownSelector.selectorsList[selector_index].selectorContainer.childNodes[0].childNodes[1].innerText = placeholder_text;
	}
	static findSelectorWithParameter(parameter) {
		for (var i = 0; i < DropdownSelector.selectorsList.length; i++) {
			for (var j = 0; j < DropdownSelector.selectorsList[i].parameters.length; j++) {
				if (DropdownSelector.selectorsList[i].parameters[j].id = parameter) {
					return DropdownSelector.selectorsList[i];
				}
			}
		}
	}
	drawSelector() {
		this.selectorContainer.classList.toggle("dropdown-act-select", true);
		var dropfield = document.createElement("div");
		dropfield.classList.toggle("dropfield", true);
		this.selectorContainer.appendChild(dropfield);
		var dropdownContent = document.createElement("div");
		dropdownContent.classList.toggle("dropdown-content-selector", true);
		this.selectorContainer.appendChild(dropdownContent);
		var dropdownArrow = document.createElement("span");
		dropdownArrow.classList.toggle("dropdown-arrow", true);
		dropfield.appendChild(dropdownArrow);
		for (var i = 0; i < this.parameters.length; i++) {
			if (this.parameters[i].key != "placeholder") {
				var element = document.createElement("div");
				element.classList.toggle("dropdown-elem", true);
				element.innerText = this.parameters[i].value;
				element.setAttribute("id", this.parameters[i].key);
				var element_sel_id = this.index; 
				element.setAttribute("selector_index", this.index);
				element.onclick = function() {
					DropdownSelector.select(this.attributes.selector_index.value, this.attributes.id.value, this.innerText);
				};
				dropdownContent.appendChild(element);
			} else {
				var placeholder = document.createElement("span");
				placeholder.id = "typeSelector";
				placeholder.innerHTML = "<font style=\"color: #757575\">" + this.parameters[i].value + "</span>";
				dropfield.appendChild(placeholder);
			}
		}
	}
	getSelected() {
		return this.selected;
	}
}