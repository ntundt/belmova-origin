class Sidebar {
	constructor(elements=[], view=[]) {
		this.active = {
			array_element: elements[0],
			dom_element: null,
			id: 0
		}
		this.elements = elements;
		this.view = view;
		Sidebar.instance = this;
		this.drawMyself(this.elements, this.view);
	}
	drawMyself(elements=[], view=[]) {
		var sidebar = document.createElement("div");
		sidebar.classList.add("sidebar", "piece-of-paper");
		document.body.appendChild(sidebar);
		for (let i = 0; i < elements.length; i++) {
			var sb_element = document.createElement("div");
			sb_element.classList.add("sb-elem");
			if (elements[i].selected) {
				this.active = {
					array_element: elements[i],
					dom_element: sb_element,
					id: i
				};
				sb_element.classList.add("sb-elem-active");
				elements[i].onInitExec ? elements[i].onselect() : null;
			}
			sb_element.setAttribute("sb_elem_index", i);
			if (elements[i].onselect !== undefined) sb_element.onclick = function() {
				Sidebar.activate(this);
			};	
			if (elements[i].text !== undefined) sb_element.innerText = elements[i].text;
			sidebar.appendChild(sb_element);
		}
	}
	static getInstance() {
		if (Sidebar.instance === undefined) Sidebar.instance = new Sidebar([{
			text: "Click at me",
			onselect: function(){ alert("Please, create a sidebar before getting an instance :3") }
		}]);
		return Sidebar.instance;
	}
	static activate(sb_element) {
		Sidebar.getInstance().active.dom_element.classList.remove("sb-elem-active");
		sb_element.classList.add("sb-elem-active");
		Sidebar.getInstance().active = {
			array_element: Sidebar.getInstance().elements[sb_element.attributes.sb_elem_index.value],
			dom_element: sb_element,
			id: sb_element.attributes.sb_elem_index.value
		};
		Sidebar.getInstance().active.array_element.onselect();
	}
}