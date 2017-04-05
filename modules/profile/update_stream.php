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

//Required configuration files
require_once(dirname(__FILE__) . '/../../core/abre_verification.php');
require_once(dirname(__FILE__) . '/../../core/abre_dbconnect.php');
require_once(dirname(__FILE__) . '/../../core/abre_functions.php');

if(superadmin())
{

  //Add the stream
  $streamid=$_POST["id"];
  $streamtitle=mysqli_real_escape_string($db, $_POST["title"]);
  $rsslink=mysqli_real_escape_string($db, $_POST["link"]);
  $streamgroup=mysqli_real_escape_string($db, $_POST["group"]);

  if($streamid=="")
  {
    var_dump($db);
    $stmt = $db->stmt_init();
    //needed to backtick because SQL doesn't like when you use reserved words
    $sql = "INSERT INTO `streams` (`group`,`title`,`slug`,`type`,`url`,`required`) VALUES ('$streamgroup','$streamtitle','$streamtitle','flipboard','$rsslink','0');";
    $stmt->prepare($sql);
    $stmt->execute();
    $stmt->close();
    $db->close();
  }
  else
  {
    //needed to backtick because SQL doesn't like when you use reserved words
    mysqli_query($db, "UPDATE `streams` set `group`='$streamgroup', `title`='$streamtitle', `slug`='$streamtitle', `type`='flipboard', `url`='$rsslink' where `id`='$streamid'") or die (mysqli_error($db));
  }
}
?>