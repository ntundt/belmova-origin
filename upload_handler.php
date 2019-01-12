<?php

if (isset($_FILES['file']['name'])) { 
	if (0 !== $_FILES['file']['size'] and 1024000 >= $_FILES['file']['size']) {
		if (is_uploaded_file($_FILES['file']['tmp_name'])) {
			if (move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['tmp_name'])) {
				echo json_encode(array('filename' => 'uploads/' . $_FILES['file']['tmp_name']));
			}
		}
	}
} else {
	header('Location: http://localhost/', true, 301);
}