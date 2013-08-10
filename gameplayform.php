<?php
/*
 Plugin Name: Gameplayform
 Plugin URI: http://darcade.de/
 Description: Plugin zum bereitstellen einer Form, um Gameplays zu versenden.
 Version: 0.0.1
 Author: Darcade
 Author URI: http://darcade.de/
 Update Server: http://gameplayform.darcade.de/
 Min WP Version: 1.5
 Max WP Version: 2.0.4
 */

$plugintablename = "darcadetable";
$gameplayformdbversion = "1.0";

if (!class_exists("GameplayFormClass")) {

	class GameplayFormClass {

		public static function doFrontendController() {
			static $form;
			if (!isset($form)) {
				$form = new GameplayFormClass();
				$form -> frontendController();
			}
			return $form;
		}

		public function frontendController() {

			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST' :
					$this -> datenAbspeichern();
					break;
				case 'GET' :
					ob_start();
					$datei = __DIR__ . DIRECTORY_SEPARATOR . 'formular.php';
					ob_end_clean();
					include $datei;
					break;
			}
		}

		public function datenAbspeichern() {
			var_dump($_POST);
		}

		public function admininterface() {
			echo '<div class="wrap">
				<div>
					<h1>Gameplayform</h1>
					<p>Hier kommt später eine Tabelle hin :)</p>
				</div>
			</div><br>';
		}

		private function createtable() {
			global $wpdb, $plugintablename, $gameplayformdbversion;

			$dbname = $wpdb -> prefix . $plugintablename;
			$sql = "CREATE TABLE `" . $dbname . "` (
				`game` text NOT NULL,
				`isithd` text NOT NULL,
				`laenge` text NOT NULL,
				`spieleablauf` longtext NOT NULL,
				`endscore` text NOT NULL,
				`klasse` text NOT NULL,
				`dllink` text NOT NULL,
				`audiovorkommen` text NOT NULL,
				`dllinkaudio` text NOT NULL,
				`sonstiges` longtext NOT NULL,
				`kanal` text NOT NULL
				);";

			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			add_option("gameplayformdb_version", $gameplayformdbversion);
		}



		public function installgpplugin() {
			$this->createtable();
		}

	}

}

$runvariable = new GameplayFormClass;

//*********************************************************************
//**********************FIXME******************************************
//*********************************************************************

function addadmininterface() {
	global $runvariable;
	add_menu_page('Gameplayform', 'Gameplayform', manage_options, __FILE__, array(&$runvariable, 'admininterface'));
}


add_shortcode('gameplayform', array('GameplayFormClass', 'doFrontendController'));
add_action('admin_menu', 'addadmininterface');
?>
