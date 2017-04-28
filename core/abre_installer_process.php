<?php

	/*
	* Copyright 2015 Hamilton City School District
	*
	* This program is free software: you can redistribute it and/or modify
    * it under the terms of the GNU General Public License as published by
    * the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
	*
    * This program is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    * GNU General Public License for more details.
	*
    * You should have received a copy of the GNU General Public License
    * along with this program.  If not, see <http://www.gnu.org/licenses/>.
    */

	//Check to make sure no configuration file exists
	if (!file_exists('../configuration.php'))
	{
		//Check for Mcrypt
		if(!function_exists('mcrypt_encrypt')){
		    echo "Mcrypt is not installed. Please install Mcrypt to proceed with the installation.";
		    exit();
		}

		//Check connection to database
		$conn = @mysqli_connect($_POST["db_host"], $_POST["db_user"], $_POST["db_password"], $_POST["db_name"]);
		if (mysqli_connect_errno()) {
			echo "Please check your database credentials. Abre was unable to establish a connection.";
			exit();
		}

		//Create Private Directories if don't already exist
	    if (!file_exists("../../".$_POST["abre_private_root"])){
			if (!mkdir("../../".$_POST["abre_private_root"], 0775)) {
			    echo "Unable to create private directory. Make sure your private root directory has write permissions.";
				exit();
			}
		}
		if (!file_exists("../../".$_POST["abre_private_root"]."/directory")){
			if (!mkdir("../../".$_POST["abre_private_root"]."/directory", 0775)) {
			    echo "Unable to create private directory. Make sure your private root directory has write permissions.";
				exit();
			}
		}
		if (!file_exists("../../".$_POST["abre_private_root"]."/directory/images")){
			if (!mkdir("../../".$_POST["abre_private_root"]."/directory/images", 0775)) {
			    echo "Unable to create private directory. Make sure your private root directory has write permissions.";
				exit();
			}
		}
		if (!file_exists("../../".$_POST["abre_private_root"]."/directory/images/employees")){
			if (!mkdir("../../".$_POST["abre_private_root"]."/directory/images/employees", 0775)) {
			    echo "Unable to create private directory. Make sure your private root directory has write permissions.";
				exit();
			}
		}
		if (!file_exists("../../".$_POST["abre_private_root"]."/stream")){
			if (!mkdir("../../".$_POST["abre_private_root"]."/stream", 0775)) {
			    echo "Unable to create private directory. Make sure your private root directory has write permissions.";
				exit();
			}
		}
		if (!file_exists("../../".$_POST["abre_private_root"]."/stream/cache")){
			if (!mkdir("../../".$_POST["abre_private_root"]."/stream/cache", 0775)) {
			    echo "Unable to create private directory. Make sure your private root directory has write permissions.";
				exit();
			}
		}
		if (!file_exists("../../".$_POST["abre_private_root"]."/stream/cache/feed")){
			if (!mkdir("../../".$_POST["abre_private_root"]."/stream/cache/feed", 0775)) {
			    echo "Unable to create private directory. Make sure your private root directory has write permissions.";
				exit();
			}
		}
		if (!file_exists("../../".$_POST["abre_private_root"]."/stream/cache/images")){
			if (!mkdir("../../".$_POST["abre_private_root"]."/stream/cache/images", 0775)) {
			    echo "Unable to create private directory. Make sure your private root directory has write permissions.";
				exit();
			}
		}

		//Create Configuration file
		$myfile = fopen("../configuration.php", "w");
	    if (!$myfile) {
	       echo "Unable to create configuration file. Make sure your public root directory has write permissions.";
	       exit();
	    }

			//Create required database tables
		$sql = "CREATE TABLE `users` (`id` int(11) NOT NULL,`email` text NOT NULL,`superadmin` int(11) NOT NULL DEFAULT '0',`refresh_token` text NOT NULL,`cookie_token` text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$sql .= "ALTER TABLE `users` ADD PRIMARY KEY (`id`);";
		$sql .= "ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";

		$conn->multi_query($sql);

		$sql2 = "CREATE TABLE `users_parent` (`id` int(11) NOT NULL,`email` text NOT NULL,`students` text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$sql2 .= "ALTER TABLE `users_parent` ADD PRIMARY KEY (`id`);";
		$sql2 .= "ALTER TABLE `users_parent` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";

		$conn->multi_query($sql2);
		$conn->close();

			//Setup PHP file
			$txt = "<?php\n\n";
			fwrite($myfile, $txt);

			//Get current URL and write path
			function url(){
				return sprintf(
					"%s://%s%s",
					isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
				    $_SERVER['SERVER_NAME'],
				    $_SERVER['REQUEST_URI']
				);
			}

			$currenturl=url();
			$currenturl = substr($currenturl, 0, strrpos($currenturl, "/core"));
			$txt = '$portal_root = "'.$currenturl.'";';
			fwrite($myfile, $txt);

			$txt = "\n";
			fwrite($myfile, $txt);

			$txt = '$portal_private_root = "'.$_POST["abre_private_root"].'";';
			fwrite($myfile, $txt);

			//Write Google Apps Domain name
			if($_POST['domain_name']!="")
			{
				$txt = "\n";
				fwrite($myfile, $txt);
				$txt = "if (!defined('SITE_GAFE_DOMAIN')){ define('SITE_GAFE_DOMAIN','".$_POST['domain_name']."'); }";
				fwrite($myfile, $txt);
			}

			//Write MySQL host
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = "if (!defined('DB_HOST')){ define('DB_HOST','".$_POST['db_host']."'); }";
			fwrite($myfile, $txt);

			//Write MySQL name
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = "if (!defined('DB_NAME')){ define('DB_NAME','".$_POST['db_name']."'); }";
			fwrite($myfile, $txt);

			//Write MySQL username
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = "if (!defined('DB_USER')){ define('DB_USER','".$_POST['db_user']."'); }";
			fwrite($myfile, $txt);

			//Write MySQL password
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = "if (!defined('DB_PASSWORD')){ define('DB_PASSWORD','".$_POST['db_password']."'); }";
			fwrite($myfile, $txt);

			//Write MySQL key
			$txt = "\n";
			fwrite($myfile, $txt);
			function generateRandomString($length = 32) {
				$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$charactersLength = strlen($characters);
				$randomString = '';
				for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
				}
				return $randomString;
			}

			$randommysqlkey=generateRandomString();
			$txt = "if (!defined('DB_KEY')){ define('DB_KEY','$randommysqlkey'); }";
			fwrite($myfile, $txt);

			//Write Google Console client ID
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = "if (!defined('GOOGLE_CLIENT_ID')){ define('GOOGLE_CLIENT_ID','".$_POST['google_client_id']."'); }";
			fwrite($myfile, $txt);

			//Write Google Console client secret
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = "if (!defined('GOOGLE_CLIENT_SECRET')){ define('GOOGLE_CLIENT_SECRET','".$_POST['google_client_secret']."'); }";
			fwrite($myfile, $txt);

			//Write Google Console API key
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = "if (!defined('GOOGLE_API_KEY')){ define('GOOGLE_API_KEY','".$_POST['google_api_key']."'); }";
			fwrite($myfile, $txt);

			//Write Google Console Domain name
			if($_POST['domain_name']!="")
			{
				$txt = "\n";
				fwrite($myfile, $txt);
				$domain_name_single = substr($_POST['domain_name'], 1);
				$txt = "if (!defined('GOOGLE_HD')){ define('GOOGLE_HD','$domain_name_single'); }";
				fwrite($myfile, $txt);
			}

			//Write cookie encryption
			$txt = "\n";
			fwrite($myfile, $txt);
			$randomcookiekey = substr(md5(microtime()),rand(0,26),10);
			$txt = "if (!defined('PORTAL_COOKIE_KEY')){ define('PORTAL_COOKIE_KEY','$randomcookiekey'); }";
			fwrite($myfile, $txt);

			//Write cookie name
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = "if (!defined('PORTAL_COOKIE_NAME')){ define('PORTAL_COOKIE_NAME','Abre'); }";
			fwrite($myfile, $txt);

			//Write turn off php errors
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = "ini_set('display_errors','off');";
			fwrite($myfile, $txt);

			//Write server path
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = '$portal_path_root = $_SERVER[\'DOCUMENT_ROOT\'];';
			fwrite($myfile, $txt);

			//Write Google redirection location
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = 'if (!defined(\'GOOGLE_REDIRECT\')){ define(\'GOOGLE_REDIRECT\', $portal_root.\'/index.php\'); }';
			fwrite($myfile, $txt);

			//Write Google scopes
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = "if (!defined('GOOGLE_SCOPES')){ define('GOOGLE_SCOPES',serialize (array('https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/plus.login', 'https://www.googleapis.com/auth/gmail.modify', 'https://www.googleapis.com/auth/drive.readonly', 'https://www.googleapis.com/auth/calendar.readonly', 'https://www.googleapis.com/auth/classroom.courses.readonly', 'https://www.googleapis.com/auth/classroom.rosters.readonly'))); }";
			fwrite($myfile, $txt);

			//Write stream cache setting
			$txt = "\n";
			fwrite($myfile, $txt);
			$txt = "if (!defined('STREAM_CACHE')){ define('STREAM_CACHE','true'); }";
			fwrite($myfile, $txt);

			//End PHP file
			$txt = "\n\n?>";
			fwrite($myfile, $txt);

			//Close file
			fclose($myfile);

			//Redirect
			echo "Redirect";

	}

?>
