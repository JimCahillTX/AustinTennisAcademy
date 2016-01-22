<html>
<head>

<style>

body {
        background-color:#0a2464;        
        font-size:13px;
	font-family: Arial, Helvetica, sans-serif;
	color:#FFFFFF;
}

p {
	padding:0px 0px 18px 0px;
}

a {
	text-decoration: underline;
	color:#FFFFFF;
	outline:none;
}
a:hover {
	text-decoration: none;
}

</style>
</head>

<body>
<?php
$services_json = json_decode(getenv("VCAP_SERVICES"),true);
$mysql_config = $services_json["mysql-5.1"][0]["credentials"];
$username = $mysql_config["username"];
$password = $mysql_config["password"];
$hostname = $mysql_config["hostname"];
$port = $mysql_config["port"];
$db = $mysql_config["name"];
$link = mysql_connect("$hostname:$port", $username, $password);

  if (!$link)
    die(mysql_error());

$db_selected = mysql_select_db($db, $link);

$sql = "SELECT * FROM events WHERE (TO_DAYS(NOW())>(TO_DAYS(SignupDeadline) - 45) AND (TO_DAYS(NOW()) <= TO_DAYS(EventEndDate) + 2)) ORDER BY 'SignupDeadline' ASC;";
$records = mysql_query($sql, $link);
  if (!$records)
  {
    die(mysql_error());
  }
  else{       
while($rows = mysql_fetch_array($records)){
echo "<div><h3>" . $rows['EventName'] . "</h3></div>";
echo "<div>Location: " . $rows['EventLocation'] . "</div>";
// echo "<div>Dates: " . date('%b %d', strtotime($row['EventStartDate'])) . " - " . date('%b %d', strtotime($row['EventEndDate'])) . "</div>";
$startdate = strtotime( $rows['EventStartDate'] );
$enddate = strtotime( $rows['EventEndDate'] );
echo "<div>Dates: " . date( 'M j', $startdate ) . " - " . date( 'M j', $enddate ) . "</div>";
$signup = strtotime( $rows['SignupDeadline'] );
$today = strtotime( date("Y-m-d") );
$interval = ($signup - $today) / 60 / 60 / 24; //Convert intervals from seconds to days
$strongred = ( ($interval < 7) && ($interval >= 0) );

   if ($strongred)
   {
   echo "<div>Deadline : " . "<strong><font color='red'>" . date( 'M j', $signup ) . "</font></strong><div>";
   }
   else{
   echo "<div>Deadline : " . date( 'M j', $signup ) . "<div>";
   }

echo "<div>Website: <a target=\"_blank\" " . "href=\"" .$rows['EventURL'] . "\"" . ">" . strval($rows['TournamentID']) . "</a></div>";
   }
}
mysql_close($link); 

?>

</body>
</html>
