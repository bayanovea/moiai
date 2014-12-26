<?php
	interface IDB {
		public function connect($host, $user, $password, $db);
		public function defaultConnect();
	}
?>