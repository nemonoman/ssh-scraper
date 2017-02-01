<?

function isthisdb($severstring) {
    $x = onerow("SHOW VARIABLES WHERE Variable_name = 'hostname'");
    $host = $x['Value'];
    return str_replace($severstring, '', $host) <> $host;
}

function isthisserver($severstring) {
    $host = $_SERVER['SERVER_NAME'];
    return str_replace($severstring, '', $host) <> $host;
}

function dosql($query, $dbname = DB_DATABASE) {
    if (!$dbname) {
        $dbname = DB_DATABASE;
    }
    $errorlevel = error_reporting(0);
    error_reporting(0);
    global $mysqlerror;
    global $dontusesql; //this is a phpinclude file
    if ($dbname) {
        if ($dontusesql) {

        } else {
            $link = mysql_connect('localhost', 'root', 'donn0330x');
            if (!$link) {
                die('Could not connect: ' . mysql_error());
            }
            $db_selected = mysql_select_db($dbname, $link);
            if (!$db_selected) {
                die(":::Could not get database $dbname: " . mysql_error());
            }
        }
    }
    $result = mysql_query($query);
    $mysqlerror = mysql_error($link);
    if ($dbname && $link) {
        $errorlevel = error_reporting();
        error_reporting(0);
        mysql_close($link);
        error_reporting($errorlevel);
    }
    mysql_select_db(DB_DATABASE);
    if ($mysqlerror) {
        //echo "$query<BR> $dbname <B>DOSQLERROR is $mysqlerror</B><br><br>";
    }
    error_reporting(errorlevel);
    return $result;
}

function oneresult($query, $db = DB_DATABASE) {
    if (!$db) {
        $db = DB_DATABASE;
    }
    global $dontusesql; //this is a phpinclude file

    if ($db) {
        return oneDBresult($query, $db);
    }

    $result = dosql($query, $db);
    $row = mysql_fetch_array($result);
    mysql_select_db(DB_DATABASE);
    return $row[0];
}

function echoresult($query, $db = DB_DATABASE) {
	  $error='';
    if (!$db) {
        $db = DB_DATABASE;
    }
    //showvalue("echoresult($query,$db",DB_DATABASE);
    if ($db) {
        $row[0] = oneDBresult($query, $db);
    } else {
        $result = dosql($query, $db);
        $row = mysql_fetch_array($result);
    }
    $error =' errorno: ' . mysql_errno();
    echo "<PRE style=text-align:left>$query
  -- >$row[0]  $error Database=$db</PRE>";
//  showvalue(DB_DATABASE);
    if (DB_DATABASE) {
        //mysql_select_db(DB_DATABASE);
    }
    return $row[0];
}

function querytoarray($query, $dbname = DB_DATABASE) {
    if (!$dbname) {
        $dbname = DB_DATABASE;
    }
    global $dontusesql; //this is a phpinclude file
    if ($dbname) {
        if ($dontusesql) {

        } else {
            $link = mysql_connect('localhost', 'root', 'donn0330x');
            if (!$link) {
                die('Could not connect: ' . mysql_error());
            }
            $db_selected = mysql_select_db($dbname, $link);
            if (!$db_selected) {
                die("Sorry -- Could not get database $dbname: " . mysql_error());
            }
        }
    }

    $result = dosql($query, $dbname);
    while ($row = mysql_fetch_array($result)) {
        if ($row[0]) {
            $return[] = $row[0];
        }
    }
    if ($dbname && $link) {
        $errorlevel = error_reporting();
        error_reporting(0);
        mysql_close($link);
        error_reporting($errorlevel);
    }
    mysql_select_db(DB_DATABASE);
    return $return;
}

function oneDBresult($query, $dbname = DB_DATABASE) {
    if (!$dbname) {
        $dbname = DB_DATABASE;
    }
    global $dontusesql; //this is a phpinclude file

    if ($dbname) {
        if ($dontusesql) {

        } else {
            $link = mysql_connect('localhost', 'root', 'donn0330x');
            if (!$link) {
                die('Could not connect: ' . mysql_error());
            }
            $db_selected = mysql_select_db($dbname, $link);
            if (!$db_selected) {
                die(" -- Could not get database $dbname: " . mysql_error());
            }
        }
        $errorlevel = error_reporting();
        error_reporting(0);
        ;
        $result = dosql($query, $dbname);
        if ($result) {
            $row = mysql_fetch_array($result);
            error_reporting($errorlevel);
            return $row[0];
        } else {
            error_reporting($errorlevel);
            return '';
        }
        if ($dbname && $link) {
            $errorlevel = error_reporting();
            error_reporting(0);
            mysql_close($link);
            error_reporting($errorlevel);
        }
    } else {
        return oneresult($query, $dbname);
    }
}

function echosql($query, $db = '') {
    if (!$db) {
        $db = DB_DATABASE;
    }
    if ($db) {
        $dbstr = "USING DATABASE $db\n";
    }
    echo "<PRE>$dbstr$query</PRE>";
    dosql($query, $db);
}

function debugvalue() {
    global $debugvalue;
    //$debugvalue=1; //1 to see use database table 'debug';
//debug value is good to debug ajax, as variables are stored
//in database tables, instead of throwing off page creation/javascript, etc, etc.
//an alternative to showvalue, alerts, etc.

    if (!$debugvalue) {
        return;
    }
    global $debugnow;
    if (!$debugnow) {
        $debugnow = oneresult("SELECT NOW()");
    }
    $params = func_get_args();

    if (count($params) == 1) {
        $return = debugarray($params[0], 'ShowValues');
    } else {
        $return = debugarray($params, 'ShowValues');
    }
    $mydebugarraybuilder = addslashes($return);
    oneresult("INSERT INTO debug SET now='$debugnow', text='$mydebugarraybuilder'");
}

function showvalue() {
    $params = func_get_args();

    if (count($params) == 1) {
        $return = testarray($params[0], 'ShowValues');
    } else {
        $return = testarray($params, 'ShowValues');
    }
    global $returntestarray;
    if ($returntestarray) {
        return $return;
    }
}

function testarray($thearray = 'array', $arrayname = 'Test Array', $spacer = '') {
    global $iteration;
    if (isset($iteration)) {

    } else {
        $iteration = 1;
    }
    if (is_object($thearray)) {
        $thearray = (array) $thearray;
    }
    global $testarraybuilder;
    if (!$spacer) {
        $testarraybuilder = '';
    }
    global $arraycount;
    $holdarrayname = $arrayname;
    if (is_array($thearray)) {
        //$arraycount = array_search ($thearray,$GLOBALS);
        if ($arraycount) {
            $itemname = "Array$arraycount";
        }
        $arraycount++;
    } else {
        $x[] = $thearray;
        unset($thearray);
        $thearray = $x;
        unset($x);
    }
    global $sitename;
    if (($arraycount != 'Test Array' || $arraycount != 'ShowValues') && $arraycount && $arraycount <> 'argv') {
        $arrayname = $sitename;
    } else {

    }
    if ($arrayname == '$db') {
        $arrayname = $holdarrayname;
    }

    $calledfromtestarray = $spacer;
    if (!$calledfromtestarray) {
        //$bkg="background-color:#FFDEAD;";
        $testarraybuilder.= "<PRE >
      <div style=padding:22px;>";
        $testarraybuilder.= "<span>$arrayname</span><BR>\n";
        if ($arrayname == 'ShowValues') {
            global $showvalueiteration;
            $showvalueiteration++;
            if ($showvalueiteration > 1) {
                $iteration = " Iteration  $showvalueiteration\n";
            }
            //$bkg="background-color:#FFDEAD;";
        }
    }
    if (!is_array($thearray)) {
        $arraycount = array_search($thearray, $GLOBALS);
        $testarraybuilder.= "$spacer$arrayname => $thearray";
    } elseif (!count($thearray)) {
        $testarraybuilder.= "$spacer" . "$v2  >>EMPTY << \n";
        $spacer = $holdspacer;
        return;
    } else {
        $testarraybuilder.= "$spacer" . "$arrayname$iteration\n";
        $iteration = '';
    }
    $spacer .= '    ';
    foreach ($thearray as $key => $value) {
        if (is_object($value)) {
            $value = (array) $value;
        }
        if (gettype($value) == 'array') {
            $holdspacer = $spacer;
            if (count($value)) {
                testarray($value, $v2 . '[' . $key . ']', $spacer);
            }
            $spacer = $holdspacer;
        } else {
            $testarraybuilder.= "$spacer" . '[' . $key . "] => '$value'\n";
        }
    }
    if (!$calledfromtestarray) {
        $testarraybuilder.= "\n</div></PRE>";
        global $returntestarray;
        if ($returntestarray) {
            return $testarraybuilder;
        } else {
            echo $testarraybuilder;
        }
    }
}

function debugarray($thearray = 'array', $arrayname = 'Test Array', $spacer = '') {
    if (is_object($thearray)) {
        $thearray = (array) $thearray;
    }
    global $debugarraybuilder;
    if (!$spacer) {
        $debugarraybuilder = '';
    }
    global $debugarraycount;
    $holdarrayname = $arrayname;
    if (is_array($thearray)) {
        //$arraycount = array_search ($thearray,$GLOBALS);
        if ($debugarraycount) {
            $itemname = "Array$debugarraycount";
        }
        $debugarraycount++;
    } else {
        $x[] = $thearray;
        unset($thearray);
        $thearray = $x;
        unset($x);
    }
    if (($debugarraycount != 'Test Array' || $debugarraycount != 'ShowValues') && $debugarraycount && $debugarraycount <> 'argv') {
        $arrayname = $itemname;
    } else {

    }
    if ($arrayname == '$db') {
        $arrayname = $holdarrayname;
    }

    $calledfromtestarray = $spacer;
    if (!$calledfromtestarray) {
        $bkg = "background-color:#FFDEAD;";
        if ($arrayname == 'ShowValues') {
            global $showvalueiteration;
            $showvalueiteration++;
            if ($showvalueiteration > 1) {
                $iteration = " Iteration  $showvalueiteration\n";
            }
        }
    }
    if (!is_array($thearray)) {
        $debugarraycount = array_search($thearray, $GLOBALS);
        $debugarraybuilder.= "$spacer$arrayname => $thearray";
    } elseif (!count($thearray)) {
        $debugarraybuilder.= "$spacer" . "$v2  >>EMPTY << \n";
        $spacer = $holdspacer;
        return;
    } else {
        $debugarraybuilder.= "$spacer" . "$arrayname$iteration\n";
        $iteration = '';
    }
    $spacer .= '    ';
    foreach ($thearray as $key => $value) {
        if (is_object($value)) {
            $value = (array) $value;
        }
        if (gettype($value) == 'array') {
            $holdspacer = $spacer;
            if (count($value)) {
                debugarray($value, $v2 . '[' . $key . ']', $spacer);
            }
            $spacer = $holdspacer;
        } else {
            $debugarraybuilder.= "$spacer" . '[' . $key . "] => '$value'\n";
        }
    }
    if (!$calledfromtestarray) {

        global $returntestarray;
        if (true) {
            return $debugarraybuilder;
        } else {

        }
    }
}

function onerow($query, $dbname = DB_DATABASE) {
    global $dontusesql; //this is a phpinclude file
    if (!$dbname) {
        $dbname = DB_DATABASE;
    }
    if ($dbname) {
        if ($dontusesql) {

        } else {
            $link = mysql_connect('localhost', 'root', 'donn0330x');
            if (!$link) {
                die('Could not connect: ' . mysql_error());
            }
            $db_selected = mysql_select_db($dbname, $link);
            if (!$db_selected) {
                die(" ----> Could not get database $dbname: " . mysql_error());
            }
        }
    }
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    if ($dbname && $link) {
        $errorlevel = error_reporting();
        error_reporting(0);
        mysql_close($link);
        error_reporting($errorlevel);
    }
    mysql_select_db(DB_DATABASE);
    return $row;
}
function onelongrow($query, $dbname = DB_DATABASE) {
    global $dontusesql; //this is a phpinclude file
    if (!$dbname) {
        $dbname = DB_DATABASE;
    }
    if ($dbname) {
        if ($dontusesql) {

        } else {
            $link = mysql_connect('localhost', 'root', 'donn0330x');
            if (!$link) {
                die('Could not connect: ' . mysql_error());
            }
            $db_selected = mysql_select_db($dbname, $link);
            if (!$db_selected) {
                die(" ----> Could not get database $dbname: " . mysql_error());
            }
        }
    }
    $result = mysql_query($query);
    $row = mysql_fetch_array($result);
    if ($dbname && $link) {
        $errorlevel = error_reporting();
        error_reporting(0);
        mysql_close($link);
        error_reporting($errorlevel);
    }
    mysql_select_db(DB_DATABASE);
    return $row;
}
function allrows($query, $dbname = DB_DATABASE) {
    if (!$db) {
        $db = DB_DATABASE;
    }
    global $dontusesql; //this is a phpinclude file

    if ($dbname) {
        if ($dontusesql) {

        } else {
            $link = mysql_connect('localhost', 'root', 'donn0330x');
            if (!$link) {
                die('Could not connect: ' . mysql_error());
            }
            $db_selected = mysql_select_db($dbname, $link);
            if (!$db_selected) {
                die(" :::: Could not get database $dbname: " . mysql_error());
            }
        }
    }
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        //showvalue("in allrows", 'ROW!', $row, 'allrows', $rows);
        if ($row) {
            $rows[] = $row;
        }
    }
    if ($dbname && $link) {
        $errorlevel = error_reporting();
        error_reporting(0);
        mysql_close($link);
        error_reporting($errorlevel);
    }
    mysql_select_db(DB_DATABASE);
    return $rows;
}

/* SHORTURLs -- BABYLON COUNTER and Babylon URL UTILS

  Used in /blocks/SocialMedia to make a short url for Twitter Sharing

  Make a short url code
  Equate it to a full url
  Track usage of the code

  USAGE
  http://htwx.ws?D5Ig
  will redirect to
  http://silverleaf.hotwaxsites.com/index.php/home/blog/contact_us/localinfo

  Tables in HotWaxips:
  babyloncounter -> the base60 counter
  shorturl -> code to full index, including site that created the short url
  redirect -> ip and timestamp: how htwx.ws was handed the redirect


  OK, you want to have a way to make a short url for twitter, etc.
  So get a unique ID
  But you want it encoded, like bit.ly
  So
  Take a number and translate it into base 60
  12,117,360 becomes a 4-digit base60

  the babyloncounter database is 6 digits. Max size
   48,211,200,000
since key5 can go up to 62

  To expand, you need to add 2 more KEYS to table babyloncounter
  Update shorturl to varchar6
  and update the babyloncounter coding arrays

  This just does 4 digits, but go crazy if you want

  The Babyloncounter is like one of those little crowd counter clickers
  set up on base 60
  the gears flip every 60th digit

  The website htwx.ws is set up
  with .htaccess
  taking http://htwx.ws/AAAAAA
  parsing the AAAAAA into a query string
  finding the URL in hotwaxips. shorturl based on 'AAAAAA'
  and redirecting to http://google.com or whatever the URL was.

 */

function gettinyurl($tinypage, $tinysite, $tinyonly) {
    if (!$tinyonly) {
        $tinyonly = 0;
    }
    //because you can't do cross domain ajax..
    //send the url to hotwaxweb/tinyurl and get the tinyurl in return;
    //TINYSITE is the NAME of the tinysite making the call.
    //returns http://htwx.ws/AAAAAA
    //If tinyurl=1, returns AAAAAA
    $tinypage = urlencode($tinypage);
    $tinyrurl = file_get_contents("http://hotwaxweb.com/tinyurl.php?tinypage=$tinypage&tinysite=$tinysite&tinyonly=$tinyonly");
//	showvalue("gettinyurl($tinypage,$tinysite)","http://hotwaxweb.com/tinyurl.php?tinypage=$tinypage&tinysite=$tinysite",$tinyrurl);
   return $tinyrurl;
}

function babyloncounter($iterations) {
    if (!$iterations) {
        $iterations = 1;
    }
    $i = 0;
    while ($iterations > $i) {
        oneresult("
UPDATE babyloncounter SET
currentvalue = currentvalue +1,
key6 = if( key1 +1 >59 AND key2 +1 >59 AND key3 +1 >59 AND key4 +1 >59 AND key5 +1 >59, key6 +1, key6 ) ,
key5 = if( key1 +1 >59 AND key2 +1 >59 AND key3 +1 >59 AND key4 +1 >59, if( key5 +1 >59, 0, key5 +1 ) , key5 ) ,
key4 = if( key1 +1 >59 AND key2 +1 >59 AND key3 +1 >59, if( key4 +1 >59, 0, key4 +1 ) , key4 ) ,
key3 = if( key1 +1 >59 AND key2 +1 >59, if( key3 +1 >59, 0, key3 +1 ) , key3 ) ,
key2 = if( key1 +1 >59, if( key2 +1 >59, 0, key2 +1 ) , key2 ) ,
key1 = if( key1 +1 >59, 0, key1 +1 )
", 'hotwaxips');
        $i++;
    }
    $babyloncounter = onerow("SELECT * FROM babyloncounter", 'hotwaxips');
    return $babyloncounter;
}

/* shorturlfrombabylonarray($babyloncounter)
  the babyloncounter param is an array of each 'window' of the babylon counter
  Each window is a number between 0-59
  I've made up some random 60-char arrays of A-Za-z0-9
  ->See Babyloncounterarray
  This translates eachwindow into a 4char string
 */

function shorturlfrombabylonarray($babyloncounter) {
    foreach ($babyloncounter as $x) {
        $row[] = $x / 1;
    }
    $currentvalue = $row[0] + ($row[1] * 60) + ($row[2] * 3600) + ($row[3] * 216000)+ ($row[4] *  12960000 )+ ($row[5] * 777600000);
    global $babyloncounterarray;
    babyloncounterarray();
    $char0 = $babyloncounterarray[0][$row[0]];
    $char1 = $babyloncounterarray[1][$row[1]];
    $char2 = $babyloncounterarray[2][$row[2]];
    $char3 = $babyloncounterarray[3][$row[3]];
    $char4 = $babyloncounterarray[4][$row[4]];
    $char5 = $babyloncounterarray[5][$row[5]];
    $shorturl = "$char0$char1$char2$char3$char4$char5";
    //showvalue($babyloncounter, $row, "$char0 $char1 $char2 $char3 $char4 $char5");
    //die("shorturl is $shorturl ADIOS!");
    return $shorturl;
}

/*
  THIS IS WHERE THE WORK GETS DONE
  This
  Updates the babyloncounter
  Takes tne new value and creates a base60 charcode (shorturl)
  creates a charcode->fullblown record including the Sitename of the creating site.


  THIS TABLE GETS LOOKED AT BY the index.php of http://htwx.ws
  (saved as htwxwsindex.php)
  which looks up the full url
  captures the ip address and time in table redirect
  and redirects to the full url
 */

function shorthotwaxurl($url, $site) {
    global $dontusesql;
    $holdsqlstate = $dontusesql;
    $dontusesql = false;
    if (!$site) {
        $site = DB_DATABASE;
    }
    $shorturl=oneresult("SELECT babylonurl FROM shorturl WHERE url='$url' AND site='$site'",'hotwaxips');
    if(!$shorturl){
	    $newarray = babyloncounter();
	    $shorturl = shorturlfrombabylonarray($newarray);
	    $urlnumber = valuefrombabyloncodeshorturl($shorturl);
	    oneresult("INSERT INTO shorturl SET babylonurl='$shorturl',url='$url',site='$site',urlnumber='$urlnumber'", 'hotwaxips');
	  }
	  $dontusesql = $holdsqlstate;
    return $shorturl;
}

function valuefrombabyloncodeshorturl($shorturl) {
//	showvalue("IN valuefrombabyloncodeshorturl($shorturl)");
    global $babyloncounterarray;
    babyloncounterarray();

    $check0 = array_search($shorturl[0], $babyloncounterarray[0]);
    $check1 = array_search($shorturl[1], $babyloncounterarray[1]);
    $check2 = array_search($shorturl[2], $babyloncounterarray[2]);
    $check3 = array_search($shorturl[3], $babyloncounterarray[3]);
    //	showvalue($shorturl[0],$check0, $babyloncounterarray);

    $checkvalue = $check0 + ($check1 * 60) + ($check2 * 3600) + ($check3 * 216000);
    return $checkvalue;
}

/*
  I've made up some random 60-char arrays of A-Za-z0-9
  this the global Babyloncounterarray
  global because it gets used a lot fast
  DO NOT MESS WITH IT EVER!

 */

function babyloncounterarray() {
    //DO NOT MESS WITH IT EVER!
    global $babyloncounterarray;
    if (!$babyloncounterarray) {
        $babyloncounterarray[0][] = 'k';
        $babyloncounterarray[0][] = '4';
        $babyloncounterarray[0][] = 'o';
        $babyloncounterarray[0][] = '2';
        $babyloncounterarray[0][] = 'i';
        $babyloncounterarray[0][] = 's';
        $babyloncounterarray[0][] = 'j';
        $babyloncounterarray[0][] = 'P';
        $babyloncounterarray[0][] = 'L';
        $babyloncounterarray[0][] = 'g';
        $babyloncounterarray[0][] = 'A';
        $babyloncounterarray[0][] = '7';
        $babyloncounterarray[0][] = 'w';
        $babyloncounterarray[0][] = 'J';
        $babyloncounterarray[0][] = 'z';
        $babyloncounterarray[0][] = 'D';
        $babyloncounterarray[0][] = 'h';
        $babyloncounterarray[0][] = '6';
        $babyloncounterarray[0][] = 'K';
        $babyloncounterarray[0][] = 'E';
        $babyloncounterarray[0][] = 'Y';
        $babyloncounterarray[0][] = 't';
        $babyloncounterarray[0][] = 'd';
        $babyloncounterarray[0][] = 'e';
        $babyloncounterarray[0][] = 'V';
        $babyloncounterarray[0][] = 'N';
        $babyloncounterarray[0][] = 'I';
        $babyloncounterarray[0][] = 'H';
        $babyloncounterarray[0][] = 'G';
        $babyloncounterarray[0][] = 'F';
        $babyloncounterarray[0][] = 'Z';
        $babyloncounterarray[0][] = '0';
        $babyloncounterarray[0][] = 'r';
        $babyloncounterarray[0][] = 'n';
        $babyloncounterarray[0][] = 'f';
        $babyloncounterarray[0][] = '5';
        $babyloncounterarray[0][] = 'p';
        $babyloncounterarray[0][] = 'l';
        $babyloncounterarray[0][] = 'Q';
        $babyloncounterarray[0][] = '1';
        $babyloncounterarray[0][] = 'T';
        $babyloncounterarray[0][] = 'R';
        $babyloncounterarray[0][] = 'M';
        $babyloncounterarray[0][] = 'X';
        $babyloncounterarray[0][] = 'x';
        $babyloncounterarray[0][] = '8';
        $babyloncounterarray[0][] = 'S';
        $babyloncounterarray[0][] = 'O';
        $babyloncounterarray[0][] = '9';
        $babyloncounterarray[0][] = 'U';
        $babyloncounterarray[0][] = 'y';
        $babyloncounterarray[0][] = 'u';
        $babyloncounterarray[0][] = 'v';
        $babyloncounterarray[0][] = 'c';
        $babyloncounterarray[0][] = 'a';
        $babyloncounterarray[0][] = 'b';
        $babyloncounterarray[0][] = 'C';
        $babyloncounterarray[0][] = 'B';
        $babyloncounterarray[0][] = 'W';
        $babyloncounterarray[0][] = '3';
        $babyloncounterarray[0][] = 'm';
        $babyloncounterarray[0][] = 'q';
//DO NOT MESS WITH IT EVER!
//DO NOT MESS WITH IT EVER!
        $babyloncounterarray[1][] = 'q';
        $babyloncounterarray[1][] = 'm';
        $babyloncounterarray[1][] = 'c';
        $babyloncounterarray[1][] = 'M';
        $babyloncounterarray[1][] = 'z';
        $babyloncounterarray[1][] = '8';
        $babyloncounterarray[1][] = 'k';
        $babyloncounterarray[1][] = 'G';
        $babyloncounterarray[1][] = 'P';
        $babyloncounterarray[1][] = '5';
        $babyloncounterarray[1][] = '9';
        $babyloncounterarray[1][] = 'g';
        $babyloncounterarray[1][] = 't';
        $babyloncounterarray[1][] = 'o';
        $babyloncounterarray[1][] = 'n';
        $babyloncounterarray[1][] = '7';
        $babyloncounterarray[1][] = 'p';
        $babyloncounterarray[1][] = 'j';
        $babyloncounterarray[1][] = 'Y';
        $babyloncounterarray[1][] = 'B';
        $babyloncounterarray[1][] = '4';
        $babyloncounterarray[1][] = '3';
        $babyloncounterarray[1][] = '6';
        $babyloncounterarray[1][] = 'J';
        $babyloncounterarray[1][] = 'U';
        $babyloncounterarray[1][] = 'y';
        $babyloncounterarray[1][] = 'S';
        $babyloncounterarray[1][] = 'X';
        $babyloncounterarray[1][] = 'T';
        $babyloncounterarray[1][] = 'R';
        $babyloncounterarray[1][] = 'E';
        $babyloncounterarray[1][] = 'b';
        $babyloncounterarray[1][] = 'A';
        $babyloncounterarray[1][] = 'f';
        $babyloncounterarray[1][] = 'Q';
        $babyloncounterarray[1][] = 'w';
        $babyloncounterarray[1][] = 'r';
        $babyloncounterarray[1][] = 'u';
        $babyloncounterarray[1][] = 'a';
        $babyloncounterarray[1][] = 'C';
        $babyloncounterarray[1][] = 's';
        $babyloncounterarray[1][] = 'H';
        $babyloncounterarray[1][] = 'h';
        $babyloncounterarray[1][] = 'I';
        $babyloncounterarray[1][] = 'N';
        $babyloncounterarray[1][] = '2';
        $babyloncounterarray[1][] = 'v';
        $babyloncounterarray[1][] = 'W';
        $babyloncounterarray[1][] = 'D';
        $babyloncounterarray[1][] = 'L';
        $babyloncounterarray[1][] = 'e';
        $babyloncounterarray[1][] = 'V';
        $babyloncounterarray[1][] = 'F';
        $babyloncounterarray[1][] = 'O';
        $babyloncounterarray[1][] = '0';
        $babyloncounterarray[1][] = 'd';
        $babyloncounterarray[1][] = '1';
        $babyloncounterarray[1][] = 'i';
        $babyloncounterarray[1][] = 'K';
        $babyloncounterarray[1][] = 'Z';
        $babyloncounterarray[1][] = 'l';
        $babyloncounterarray[1][] = 'x';
//DO NOT MESS WITH IT EVER!
//DO NOT MESS WITH IT EVER!
        $babyloncounterarray[2][] = 'k';
        $babyloncounterarray[2][] = 'I';
        $babyloncounterarray[2][] = 'j';
        $babyloncounterarray[2][] = 'r';
        $babyloncounterarray[2][] = 's';
        $babyloncounterarray[2][] = 'l';
        $babyloncounterarray[2][] = 'A';
        $babyloncounterarray[2][] = 't';
        $babyloncounterarray[2][] = 'g';
        $babyloncounterarray[2][] = 'P';
        $babyloncounterarray[2][] = '4';
        $babyloncounterarray[2][] = 'D';
        $babyloncounterarray[2][] = 'M';
        $babyloncounterarray[2][] = '1';
        $babyloncounterarray[2][] = 'F';
        $babyloncounterarray[2][] = 'Q';
        $babyloncounterarray[2][] = 'C';
        $babyloncounterarray[2][] = 'G';
        $babyloncounterarray[2][] = 'm';
        $babyloncounterarray[2][] = 'S';
        $babyloncounterarray[2][] = 'Y';
        $babyloncounterarray[2][] = 'w';
        $babyloncounterarray[2][] = '3';
        $babyloncounterarray[2][] = 'a';
        $babyloncounterarray[2][] = '7';
        $babyloncounterarray[2][] = 'c';
        $babyloncounterarray[2][] = 'T';
        $babyloncounterarray[2][] = 'e';
        $babyloncounterarray[2][] = 'U';
        $babyloncounterarray[2][] = 'd';
        $babyloncounterarray[2][] = 'X';
        $babyloncounterarray[2][] = 'H';
        $babyloncounterarray[2][] = 'p';
        $babyloncounterarray[2][] = '8';
        $babyloncounterarray[2][] = 'u';
        $babyloncounterarray[2][] = 'f';
        $babyloncounterarray[2][] = 'z';
        $babyloncounterarray[2][] = '5';
        $babyloncounterarray[2][] = '2';
        $babyloncounterarray[2][] = 'q';
        $babyloncounterarray[2][] = 'L';
        $babyloncounterarray[2][] = 'B';
        $babyloncounterarray[2][] = 'V';
        $babyloncounterarray[2][] = 'E';
        $babyloncounterarray[2][] = 'K';
        $babyloncounterarray[2][] = 'W';
        $babyloncounterarray[2][] = '6';
        $babyloncounterarray[2][] = 'i';
        $babyloncounterarray[2][] = 'y';
        $babyloncounterarray[2][] = 'h';
        $babyloncounterarray[2][] = 'O';
        $babyloncounterarray[2][] = 'J';
        $babyloncounterarray[2][] = 'b';
        $babyloncounterarray[2][] = 'x';
        $babyloncounterarray[2][] = 'R';
        $babyloncounterarray[2][] = 'v';
        $babyloncounterarray[2][] = 'Z';
        $babyloncounterarray[2][] = '9';
        $babyloncounterarray[2][] = '0';
        $babyloncounterarray[2][] = 'n';
        $babyloncounterarray[2][] = 'o';
        $babyloncounterarray[2][] = 'N';
//DO NOT MESS WITH IT EVER!
//DO NOT MESS WITH IT EVER!


        $babyloncounterarray[3][] = 'g';
        $babyloncounterarray[3][] = '6';
        $babyloncounterarray[3][] = '9';
        $babyloncounterarray[3][] = '4';
        $babyloncounterarray[3][] = 'I';
        $babyloncounterarray[3][] = 'k';
        $babyloncounterarray[3][] = 'P';
        $babyloncounterarray[3][] = 'N';
        $babyloncounterarray[3][] = '2';
        $babyloncounterarray[3][] = 'G';
        $babyloncounterarray[3][] = '0';
        $babyloncounterarray[3][] = 'h';
        $babyloncounterarray[3][] = 'C';
        $babyloncounterarray[3][] = '7';
        $babyloncounterarray[3][] = 'z';
        $babyloncounterarray[3][] = 'T';
        $babyloncounterarray[3][] = 'r';
        $babyloncounterarray[3][] = 'Y';
        $babyloncounterarray[3][] = 'y';
        $babyloncounterarray[3][] = 'Z';
        $babyloncounterarray[3][] = 'o';
        $babyloncounterarray[3][] = 'Q';
        $babyloncounterarray[3][] = 'q';
        $babyloncounterarray[3][] = 'F';
        $babyloncounterarray[3][] = '3';
        $babyloncounterarray[3][] = 's';
        $babyloncounterarray[3][] = 'w';
        $babyloncounterarray[3][] = 'e';
        $babyloncounterarray[3][] = 'L';
        $babyloncounterarray[3][] = 'm';
        $babyloncounterarray[3][] = 'V';
        $babyloncounterarray[3][] = 'x';
        $babyloncounterarray[3][] = '5';
        $babyloncounterarray[3][] = 'u';
        $babyloncounterarray[3][] = 't';
        $babyloncounterarray[3][] = 'c';
        $babyloncounterarray[3][] = 'p';
        $babyloncounterarray[3][] = '8';
        $babyloncounterarray[3][] = 'v';
        $babyloncounterarray[3][] = 'R';
        $babyloncounterarray[3][] = 'S';
        $babyloncounterarray[3][] = 'l';
        $babyloncounterarray[3][] = 'K';
        $babyloncounterarray[3][] = '1';
        $babyloncounterarray[3][] = 'B';
        $babyloncounterarray[3][] = 'H';
        $babyloncounterarray[3][] = 'd';
        $babyloncounterarray[3][] = 'M';
        $babyloncounterarray[3][] = 'A';
        $babyloncounterarray[3][] = 'X';
        $babyloncounterarray[3][] = 'b';
        $babyloncounterarray[3][] = 'j';
        $babyloncounterarray[3][] = 'E';
        $babyloncounterarray[3][] = 'J';
        $babyloncounterarray[3][] = 'W';
        $babyloncounterarray[3][] = 'O';
        $babyloncounterarray[3][] = 'U';
        $babyloncounterarray[3][] = 'D';
        $babyloncounterarray[3][] = 'i';
        $babyloncounterarray[3][] = 'f';
        $babyloncounterarray[3][] = 'a';
        $babyloncounterarray[3][] = 'n';

        $babyloncounterarray[4][] = 'k';
        $babyloncounterarray[4][] = '4';
        $babyloncounterarray[4][] = 'o';
        $babyloncounterarray[4][] = '2';
        $babyloncounterarray[4][] = 'i';
        $babyloncounterarray[4][] = 's';
        $babyloncounterarray[4][] = 'j';
        $babyloncounterarray[4][] = 'P';
        $babyloncounterarray[4][] = 'L';
        $babyloncounterarray[4][] = 'g';
        $babyloncounterarray[4][] = 'A';
        $babyloncounterarray[4][] = '7';
        $babyloncounterarray[4][] = 'w';
        $babyloncounterarray[4][] = 'J';
        $babyloncounterarray[4][] = 'z';
        $babyloncounterarray[4][] = 'D';
        $babyloncounterarray[4][] = 'h';
        $babyloncounterarray[4][] = '6';
        $babyloncounterarray[4][] = 'K';
        $babyloncounterarray[4][] = 'E';
        $babyloncounterarray[4][] = 'Y';
        $babyloncounterarray[4][] = 't';
        $babyloncounterarray[4][] = 'd';
        $babyloncounterarray[4][] = 'e';
        $babyloncounterarray[4][] = 'V';
        $babyloncounterarray[4][] = 'N';
        $babyloncounterarray[4][] = 'I';
        $babyloncounterarray[4][] = 'H';
        $babyloncounterarray[4][] = 'G';
        $babyloncounterarray[4][] = 'F';
        $babyloncounterarray[4][] = 'Z';
        $babyloncounterarray[4][] = '0';
        $babyloncounterarray[4][] = 'r';
        $babyloncounterarray[4][] = 'n';
        $babyloncounterarray[4][] = 'f';
        $babyloncounterarray[4][] = '5';
        $babyloncounterarray[4][] = 'p';
        $babyloncounterarray[4][] = 'l';
        $babyloncounterarray[4][] = 'Q';
        $babyloncounterarray[4][] = '1';
        $babyloncounterarray[4][] = 'T';
        $babyloncounterarray[4][] = 'R';
        $babyloncounterarray[4][] = 'M';
        $babyloncounterarray[4][] = 'X';
        $babyloncounterarray[4][] = 'x';
        $babyloncounterarray[4][] = '8';
        $babyloncounterarray[4][] = 'S';
        $babyloncounterarray[4][] = 'O';
        $babyloncounterarray[4][] = '9';
        $babyloncounterarray[4][] = 'U';
        $babyloncounterarray[4][] = 'y';
        $babyloncounterarray[4][] = 'u';
        $babyloncounterarray[4][] = 'v';
        $babyloncounterarray[4][] = 'c';
        $babyloncounterarray[4][] = 'a';
        $babyloncounterarray[4][] = 'b';
        $babyloncounterarray[4][] = 'C';
        $babyloncounterarray[4][] = 'B';
        $babyloncounterarray[4][] = 'W';
        $babyloncounterarray[4][] = '3';
        $babyloncounterarray[4][] = 'm';
        $babyloncounterarray[4][] = 'q';

        $babyloncounterarray[5][] = 'q';
        $babyloncounterarray[5][] = 'm';
        $babyloncounterarray[5][] = 'c';
        $babyloncounterarray[5][] = 'M';
        $babyloncounterarray[5][] = 'z';
        $babyloncounterarray[5][] = '8';
        $babyloncounterarray[5][] = 'k';
        $babyloncounterarray[5][] = 'G';
        $babyloncounterarray[5][] = 'P';
        $babyloncounterarray[5][] = '5';
        $babyloncounterarray[5][] = '9';
        $babyloncounterarray[5][] = 'g';
        $babyloncounterarray[5][] = 't';
        $babyloncounterarray[5][] = 'o';
        $babyloncounterarray[5][] = 'n';
        $babyloncounterarray[5][] = '7';
        $babyloncounterarray[5][] = 'p';
        $babyloncounterarray[5][] = 'j';
        $babyloncounterarray[5][] = 'Y';
        $babyloncounterarray[5][] = 'B';
        $babyloncounterarray[5][] = '4';
        $babyloncounterarray[5][] = '3';
        $babyloncounterarray[5][] = '6';
        $babyloncounterarray[5][] = 'J';
        $babyloncounterarray[5][] = 'U';
        $babyloncounterarray[5][] = 'y';
        $babyloncounterarray[5][] = 'S';
        $babyloncounterarray[5][] = 'X';
        $babyloncounterarray[5][] = 'T';
        $babyloncounterarray[5][] = 'R';
        $babyloncounterarray[5][] = 'E';
        $babyloncounterarray[5][] = 'b';
        $babyloncounterarray[5][] = 'A';
        $babyloncounterarray[5][] = 'f';
        $babyloncounterarray[5][] = 'Q';
        $babyloncounterarray[5][] = 'w';
        $babyloncounterarray[5][] = 'r';
        $babyloncounterarray[5][] = 'u';
        $babyloncounterarray[5][] = 'a';
        $babyloncounterarray[5][] = 'C';
        $babyloncounterarray[5][] = 's';
        $babyloncounterarray[5][] = 'H';
        $babyloncounterarray[5][] = 'h';
        $babyloncounterarray[5][] = 'I';
        $babyloncounterarray[5][] = 'N';
        $babyloncounterarray[5][] = '2';
        $babyloncounterarray[5][] = 'v';
        $babyloncounterarray[5][] = 'W';
        $babyloncounterarray[5][] = 'D';
        $babyloncounterarray[5][] = 'L';
        $babyloncounterarray[5][] = 'e';
        $babyloncounterarray[5][] = 'V';
        $babyloncounterarray[5][] = 'F';
        $babyloncounterarray[5][] = 'O';
        $babyloncounterarray[5][] = '0';
        $babyloncounterarray[5][] = 'd';
        $babyloncounterarray[5][] = '1';
        $babyloncounterarray[5][] = 'i';
        $babyloncounterarray[5][] = 'K';
        $babyloncounterarray[5][] = 'Z';
        $babyloncounterarray[5][] = 'l';
        $babyloncounterarray[5][] = 'x';
    }
    //$test=$babyloncounterarray[2][4];
    //showvalue("INITIALIZED test is $test",$babyloncounterarray);
}

function setbabyloncounter($number) {
    oneresult("DELETE FROM babyloncounter", 'hotwaxips');
    oneresult("INSERT INTO babyloncounter SET currentvalue=0 ", 'hotwaxips');
    //set babyloncounter to a starting number;
    babyloncounter($number);
}

function txtsanitize($string, $maxlength = 0) {
	/**
	NON object version of sanitize(str) from
	C:\Users\Jay\Desktop\HotWax\concrete\helpers\text.php
	 * Strips tags and optionally reduces string to specified length.
	 * @param string $string
	 * @param int $maxlength
	 * @return string
	 */

		$text = trim(strip_tags($string));
		if ($maxlength > 0) {
			if (function_exists('mb_substr')) {
				$text = mb_substr($text, 0, $maxlength, APP_CHARSET);
			} else {
				$text = substr($text, 0, $maxlength);
			}
		}
		if ($text == null) {
			return ""; // we need to explicitly return a string otherwise some DB functions might insert this as a ZERO.
		}
		return $text;
	}

?>