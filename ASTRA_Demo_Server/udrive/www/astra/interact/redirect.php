<?php
if (isset($_GET['URL'])) {
	header("Location: ".$_GET['URL']);
} else {
	echo '<HTML><body>Redirection error! No URL.</body></HTML>';
}?>
