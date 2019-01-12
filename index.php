<?php

if (isset($_GET['activity'])) {
	switch ($_GET['activity']) {
	case 'bugtracker':
		include 'markup/bugtracker.phtml';
	}
} else {
	include 'markup/index.phtml';
}