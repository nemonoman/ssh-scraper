<?
header("Access-Control-Allow-Origin: *");
define("DB_DATABASE", "SwingShift");
include_once 'jsqlutils.php';
$ssid=$_REQUEST['ssid'];
$checkout=$_REQUEST['checkout'];
$checkin=$_REQUEST['checkin'];
$available="None"; //default is None
 //If and when more than one sitescraper, this can be randomized == now 1=hotels.com
if($ssid&&$checkout&&$checkin){
	$hotelid=oneresult("SELECT id FROM Hotels WHERE ssid='$ssid'");
	$hotelname=oneresult("SELECT name FROM Hotels WHERE ssid='$ssid'");
	$bookingchoice=oneresult("SELECT siteid FROM  `SiteHotels` WHERE hotelid =$hotelid ORDER BY RAND( )");
	if($bookingchoice==1){
		$scrapedsite='Hotels.com';
		$lookup1=oneresult("SELECT lookup1 FROM SiteHotels WHERE siteid=1 AND hotelid=$hotelid");
		$url="https://www.hotels.com/hotel/details.html?q-check-out=$checkout&q-check-in=$checkin&q-room-0-adults=2&hotel-id=$lookup1";
		$scrape=file_get_contents($url);
		$roombase=explode('ria-labelledby="rr-header-room-type"><h3>',$scrape);
		foreach($roombase as $roomfind){
			$roomdisplays=explode('</h3>',$roomfind);
			if($firstroomwasskipped){
				$rooms[]=$roomdisplays[0];
			}
			$firstroomwasskipped=true;
		}
		$pricebase=explode('class="current-price">',$scrape);
		foreach($pricebase as $pricefind){
			$pricedisplays=explode('</',$pricefind);
			if($firstpricewasskipped){
				$pricefix=str_replace('$','',$pricedisplays[0]);
				$pricefix=floatval($pricefix);
				$prices[]=$pricefix;
			}
			$firstpricewasskipped=true;
		}
		$baseroom=$rooms[0];
		$baseprice=$prices[0];
	}
	elseif($bookingchoice==2){
		$scrapedsite='Booking.com';
		$lookup1=oneresult("SELECT lookup1 FROM SiteHotels WHERE siteid=2 AND hotelid=$hotelid");
		$lookup2=oneresult("SELECT lookup2 FROM SiteHotels WHERE siteid=2 AND hotelid=$hotelid");
		$lookup3=oneresult("SELECT lookup3 FROM SiteHotels WHERE siteid=2 AND hotelid=$hotelid");
		$url="http://www.booking.com/hotel/$lookup1/$lookup2?checkin=$checkin;checkout=$checkout;dest_id=$lookup3;group_adults=2;no_rooms=1;room1=A%2CA;sb_price_type=total;type=total;ucfs=1&";
		$scrape=curl_contents($url);
		$scrape = preg_replace( '/<!--(.|\s)*?-->/' , '' , $scrape );
$test=str_replace('data-room-name','',$scrape);
//showvalue(strlen($scrape),strlen($test),$url,htmlentities($scrape));
		$roombase=explode('data-room-name-en="',$scrape);
$OK_Not_First=false;
		foreach($roombase as $roomfind){
			$roomdisplays=explode('"',$roomfind);

//showvalue('roomfind ROOM NAME SHOULD BE FIRST!!',$roomfind);
//	showvalue("<h2>HERE IS ROOMDISPLAYs[0] $roomdisplays[0]</h2>",htmlentities($roomdisplays[0]));
	   if($OK_Not_First){
				$rooms[]=$roomdisplays[0];
			}
			$OK_Not_First=true;
		}
		$pricebase=explode('data-price-without-addons="$',$scrape);
		$OK_Not_First=false;
		foreach($pricebase as $pricefind){
			$pricedisplays=explode('"',$pricefind);

			$pricefix=str_replace('$','',$pricedisplays[0]);
			$pricefix=str_replace(',','',$pricedisplays[0]);
			$pricefix=floatval($pricefix);
			if($OK_Not_First){
				$pricefix1=$pricefix;
				$prices[]=$pricefix;
			}
			$OK_Not_First=true;
		}

    $stay=oneresult("SELECT DATEDIFF('$checkout','$checkin')");
		$baseroom=$rooms[0];
		$baseprice=($prices[0])/$stay;
	} //booking.com
	$stay=oneresult("SELECT DATEDIFF('$checkout','$checkin')");
	$baseprice=ceil($baseprice);
	$ssbaseprice=ceil($baseprice/.91);
	$extended=$baseprice*$stay;
	$ssprice=$ssbaseprice*$stay;
	if($baseroom){
		$available='available';
	}
	$return['HotelName']=$hotelname;
	$return['Checkin']=$checkin;
	$return['Stay']=$stay;
	$return['Availablity']=$available;
	$return['RoomType']=$baseroom;
	$return['BasePrice']=$baseprice;
	$return['SwingShiftBasePrice']=$ssbaseprice;
	$return['ExtendedPrice']=$extended;
	$return['SwingShiftPrice']=$ssprice;
	$return['ScrapedSite']=$scrapedsite;
	//echo "<PRE>";print_r($return);echo "</PRE>";
	echo json_encode($return);
}
else{
	$return['HotelName']='No Hotel Specified';
	$return['Checkin']='None Specified';
	$return['Stay']='None Specified';
	$return['Availablity']=$available;
	$return['RoomType']='';
	$return['BasePrice']='';
	$return['SwingShiftBasePrice']='';
	$return['ExtendedPrice']='';
	$return['SwingShiftPrice']='';
	$return['ScrapedSite']="";
	//echo "<PRE>";print_r($return);echo "</PRE>";
	echo json_encode($return);
}
function curl_contents($url,$timeout=5){
        $header = array();
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*".'/'."*;q=0.5";
        $header[] =  "Cache-Control: max-age=0";
        $header[] =  "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: "; // browsers keep this blank.

        $ch = curl_init($url); // initialize curl with given url
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]); // set  useragent
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // write the response to a variable
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects if any
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // max. seconds to execute
        curl_setopt($ch, CURLOPT_FAILONERROR, 1); // stop when it encounters an error
        curl_setopt($ch, CURLOPT_COOKIESESSION, true );
        return @curl_exec($ch);
}
?>