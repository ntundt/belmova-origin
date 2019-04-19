class DropdownSelector {
	constructor(element, parameters) {
		this.parameters = parameters;
		this.selectorContainer = element;
		this.selected = null;
		this.id = DropdownSelector.addSelector(this);
		this.drawMyself();
		DropdownSelector.tmp = this;
		document.addEventListener("click", function(e) {
			var box = DropdownSelector.tmp.optionsContainer;// document.getElementById(DropdownSelector.last_id);
			var box_container = DropdownSelector.tmp.selectorContainer;
			if (DropdownSelector.ignoreNextEvent) {
				DropdownSelector.ignoreNextEvent = false;
				return;
			}
			if (box === null) return;
			box.classList.remove("dd-active", "dd-reverse");
			box_container.childNodes[0].classList.remove("dd-opened-to-top", "dd-opened-to-bottom");
		});
	}
	static highlight(mouse_entered_element) {
		var sel_id = mouse_entered_element.attributes.selector_index.value;
		if (DropdownSelector.selectorsList[sel_id].highlighted !== undefined) 
			DropdownSelector.selectorsList[sel_id].highlighted.classList.remove("dd-elem-highlited");
		DropdownSelector.selectorsList[sel_id].highlighted = mouse_entered_element;
		mouse_entered_element.classList.add("dd-elem-highlited");
	}
	static showOptions(selector_element, selector_id) {
		if (!DropdownSelector.selectorsList[selector_id].opened) {
			DropdownSelector.ignoreNextEvent = true;
			DropdownSelector.selectorsList[selector_id].opened = true;
		} else {
			DropdownSelector.selectorsList[selector_id].opened = false;
		}
		DropdownSelector.selectorsList[selector_id].optionsContainer.classList.add("dd-active");
		if (DropdownSelector.selectorsList[selector_id].getOffset() + 300 > Math.round(window.scrollY) + window.innerHeight) {
			DropdownSelector.selectorsList[selector_id].optionsContainer.classList.add("dd-reverse");
			DropdownSelector.selectorsList[selector_id].selectorContainer.childNodes[0].classList.add("dd-opened-to-top");
		} else {
			DropdownSelector.selectorsList[selector_id].selectorContainer.childNodes[0].classList.add("dd-opened-to-bottom");			
		}
	}
	static addSelector(selector) {
		if (DropdownSelector.selectorsList === undefined) DropdownSelector.selectorsList = [];
		var push_id = DropdownSelector.selectorsList.push(selector);
		DropdownSelector.selectorsList[push_id - 1].id = push_id;
		DropdownSelector.selectorsList[push_id - 1].index = push_id - 1;
		return push_id;
	}
	static select(selector_index, key, placeholder_text) {
		DropdownSelector.selectorsList[selector_index].selected = key;
		DropdownSelector.selectorsList[selector_index].selectorContainer.childNodes[0].childNodes[0].innerText = placeholder_text;
	}
	getOffset() {
		var offsetTop = 0;
		var elem = this.selectorContainer;
		while (elem) {
			offsetTop = offsetTop + parseFloat(elem.offsetTop);
			elem = elem.offsetParent;
		}
		return Math.round(offsetTop);
	}
	create_dd_opt(id, sel_id, text) {
		var dd_opt = document.createElement("li");
		dd_opt.classList.add("dropdown-elem");
		dd_opt.setAttribute("id", id);
		dd_opt.setAttribute("selector_index", sel_id);
		dd_opt.setAttribute("onmouseenter", "DropdownSelector.highlight(this)");
		dd_opt.onclick = function() {
			DropdownSelector.select(this.attributes.selector_index.value, this.attributes.id.value, this.innerText);
		};
		dd_opt.innerText = text;
		return dd_opt;
	}
	createPlaceholder(text) {
		var placeholder = document.createElement("span");
		this.placeholderText = text; 
		placeholder.innerHTML = '<span class="gray">' + text + '</span>';
		placeholder.id = "dd_placeholder";
		return placeholder;
	}
	dd_arrow() {
		var dd_arrow = document.createElement("span");
		dd_arrow.classList.add("dropdown-arrow");
		return dd_arrow;
	}
	drawMyself() {
		this.selectorContainer.classList.add("dropdown-act-select");
		var dropfield = document.createElement("div");
		var dropdownContent = document.createElement("ul");
		dropfield.classList.add("dropfield");
		dropdownContent.classList.add("dropdown-content-selector");
		
		for (var i = 0; i < this.parameters.length; i++) {
			if (this.parameters[i].key != "placeholder") {
				dropdownContent.appendChild(this.create_dd_opt(this.parameters[i].key, this.index, this.parameters[i].value));
			} else {
				dropfield.appendChild(this.createPlaceholder(this.parameters[i].value));
			}
		}
		if (this.placeholderText === undefined) {
			dropfield.appendChild(this.createPlaceholder(l("please_select"))); 
		}
		this.selectorContainer.onclick = function() {
			DropdownSelector.showOptions(this, this.attributes.selector_id.value);
		}
		
		dropfield.appendChild(this.dd_arrow());
		this.selectorContainer.appendChild(dropfield);
		this.selectorContainer.appendChild(dropdownContent);
		this.selectorContainer.setAttribute("selector_id", this.index);
		
		this.optionsContainer = dropdownContent;
	}
	getSelected() {
		return this.selected;
	}
	reset() {
		this.selected = null;
		this.selectorContainer.firstChild.firstChild.innerHTML = "<span class=\"gray\">" + this.placeholderText + "</span>";
	}
}