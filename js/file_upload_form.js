let dropArea = document.getElementById('drop-area');
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
	dropArea.addEventListener(eventName, preventDefaults, false);
})

function preventDefaults(e) {
	e.preventDefault();
	e.stopPropagation();
}

;['dragenter', 'dragover'].forEach(eventName => {
	dropArea.addEventListener(eventName, highlight, false);
})

;['dragleave', 'drop'].forEach(eventName => {
	dropArea.addEventListener(eventName, unhighlight, false);
})

function highlight(e) {
	dropArea.classList.add('highlight');
}

function unhighlight(e) {
	dropArea.classList.remove('highlight');
}

dropArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
	let dt = e.dataTransfer;
	let files = dt.files;

	handleFiles(files);
}

function handleFiles(files) {
	files = [...files];
	files.forEach(uploadFile);
	files.forEach(previewFile);
}

function uploadFile(file) {
	var url = 'localhost/upload';
	var xhr = new XMLHttpRequest();
	var formData = new FormData();
	xhr.open('POST', url, true);
	xhr.addEventListener('readystatechange', function(e) {
		if (xhr.readyState == 4 && xhr.status == 200) {
			if(files_s == "") {
				files_s = JSON.parse(xhr.responseText).filename;
			} else {
				files_s = files_s + (files_s.charAt(files_s.length - 1) == ";" ? "" : ";") + JSON.parse(xhr.responseText).filename;
			}
		} else if (xhr.readyState == 4 && xhr.status != 200) {
			alert("Ошибка при загрузке файла!");
		}
	})

	formData.append('file', file);
	xhr.send(formData);
}

function previewFile(file) {
	let reader = new FileReader();
	reader.readAsDataURL(file);
	reader.onloadend = function() {
		let img = document.createElement('img');
		img.src = reader.result;
		document.getElementById('gallery').appendChild(img);
	}
}
