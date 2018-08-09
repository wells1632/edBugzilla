<?PHP
//
// Get login information
//
require_once('/var/www/secure/MyBugzUser.php');
//
// Constants
//
$tb = '</td><td>';
// 
// Grab posted data
//
$bugID = isset($_POST["bugID"]) ?
  strip_tags($_POST["bugID"]) : NULL;
$edCommID = isset($_POST["edCommID"]) ?
  strip_tags($_POST["edCommID"]) : NULL;
$doit = isset($_POST["DOIT"]) ?
  strip_tags($_POST["DOIT"]) : NULL;
$edComm = isset($_POST["edComm"]) ?
  strip_tags($_POST["edComm"]) : NULL;
$commID = isset($_GET["commID"]) ?
  escapeshellcmd(rawurldecode($_GET["commID"])) : NULL;

//
// Debug Stuff
//
//var_dump($_POST);

//
// Header
//

print "<H2><CENTER>Bugzilla Comment Editor</CENTER></H2>";

// Comment Selection or Edit?
if($edCommID && $edComm) {
  if($doit) {
    $query = "UPDATE longdescs SET thetext = '" . $edComm . "' WHERE comment_id = " . $edCommID;
    $result = @mysql_query($query, $dbc);
    print "<h3>Comment updated. Comments below should be identical</h3>";
  }
  $query = "SELECT bug_id, bug_when, thetext FROM longdescs WHERE comment_id = " . $edCommID;
  $result = @mysql_query($query, $dbc);
  if($result) {
    $row=mysql_fetch_array($result, MYSQL_NUM);
    print "<TABLE BORDER=1>";
    print "<tr><th colspan=3>Original</th></tr>";
    print "<tr><th>Bug ID</th><th>Date/Time</th><th>Comment</th></tr>";
    print "<tr><td>" . $row[0] . $tb . $row[1] . $tb . nl2br($row[2]) . "</td></tr>";
    print "<tr><th colspan=3>Update</th></tr>";
    print "<tr><td>" . $row[0] . $tb . $row[1] . $tb . nl2br($edComm) . "</td></tr>";
    print "</table>";
  }
  print '<FORM METHOD="POST" ACTION="/edBugzilla.php">';
  print "<INPUT TYPE=HIDDEN NAME=edCommID VALUE=" . $edCommID . " />";
  print '<TEXTAREA NAME=edComm STYLE="visibility:hidden; position:absolute;">' . $edComm . '</TEXTAREA>';
  print "<INPUT TYPE=HIDDEN NAME=DOIT VALUE=1 />";
  print "<INPUT TYPE=SUBMIT VALUE='Confirm Edit'>";
  print "</FORM>";
} else if($commID) {
  // Edit Comment
  print '<FORM METHOD="POST" ACTION="/edBugzilla.php">';
  print "<TABLE BORDER=1>";
  print "<tr><th>Bug ID</th><th>Date/Time</th><th>Comment</th></tr>";
  $query = "SELECT bug_id, bug_when, thetext FROM longdescs WHERE comment_id = " . $commID;
  $result = @mysql_query($query, $dbc);
  if($result) {
    $row=mysql_fetch_array($result, MYSQL_NUM);
    print "<tr><td>" . $row[0] . $tb . $row[1] . $tb;
    print "<TEXTAREA NAME=edComm rows=10 cols=80>";
    print $row[2];
    print "</TEXTAREA>";
    print "</td></tr>";
  }
  print "</table>";
  print "<INPUT TYPE=HIDDEN NAME=edCommID VALUE=" . $commID . " />";
  print "<INPUT TYPE=SUBMIT VALUE='Submit'>";
  print "</FORM>";
} else {
  //
  // Comment Selection
  //
  print '<FORM METHOD="POST" ACTION="/edBugzilla.php">';
  print "<TABLE BORDER=1>";
  if($bugID) {
    print '<tr><th>Bug ID</th><td colspan=2><INPUT TYPE=TEXT NAME="bugID" SIZE=15 VALUE="';
    print $bugID . '"></td></tr>';
  } else {
    print '<tr><th>Bug ID</th><td><INPUT TYPE=TEXT NAME="bugID" SIZE=15></td></tr>';
  }
  // If a bugID has already been input, grab the comments for the bug
  if($bugID) {
    print '<tr><th colspan=3><h2>Current Comments</h2></th></tr>';
    print '<tr><th>Date/Time</th><th>Entry</th><th>Edit Comment ID</th></tr>';
    $query = "SELECT bug_when, thetext, comment_id FROM longdescs WHERE bug_id = " . $bugID;
    $result = @mysql_query($query, $dbc);
    if($result) {
      while($row=mysql_fetch_array($result, MYSQL_NUM)) {
	print '<tr><td>' . $row[0] . $tb . nl2br($row[1]) . $tb;
	print '<a HREF="edBugzilla.php?commID=' . $row[2] . '">Edit Comment #';
	print $row[2] . '</a></td></tr>';
      }
    }
  }
  print "</TABLE>";
  print "<INPUT TYPE=SUBMIT VALUE='Submit'>";
  print "</FORM>";
}

