<?
 error_reporting(E_ERROR | E_PARSE);ini_set('display_errors',1);
define("DB_DATABASE", "SwingShift");
include_once '../jsqlutils.php';
$rand=rand(5,15);
$checkin=echoresult("SELECT CURRENT_DATE() + INTERVAL $rand DAY");
$checkout=echoresult("SELECT CURRENT_DATE() + INTERVAL (1+$rand) DAY");
$url="http://www.booking.com/hotel/us/avanti-resort.html?checkin=$checkin;checkout=$checkout;dest_id=20023488;group_adults=2;no_rooms=1;room1=A%2CA;sb_price_type=total;type=total;ucfs=1&";
//$url='http://abstract5.com';
//$url='https://www.orbitz.com/Orlando-Hotels-Avanti-International-Resort.h3832.Hotel-Information?chkin=09%2F28%2F2016&chkout=09%2F29%2F2016&rm1=a2&hwrqCacheKey=6fe911cf-02ef-45a4-a4ff-b7df14741bcaHWRQ1473619340792&c=cfd0b1ae-c840-4c77-a129-ec1f4912a54e&&exp_dp=77.07&exp_ts=1473619340552&exp_curr=USD&exp_pg=HSR';
//
//$url='https://www.getaroom.com/hotels/avanti-resort-orlando?check_in=09%2F28%2F2016&check_out=09%2F29%2F2016';
//$url='http://us.venere.com/hotel/details.html?tab=description&destinationId=1404711&destination=Orlando%2C+Florida%2C+United+States+of+America&hotelId=193986&arrivalDate=09-18-16&departureDate=09-19-16&rooms[0].numberOfAdults=2&roomno=1&validate=false&previousDateful=false&reviewOrder=date_newest_first&cur=USD&pos=VCOM_US&hotelid=193986&locale=en_US&wapa2=193986&rffrid=mdp.vcom.US.112.158.02.kwrd%3D532650278';
echo "$url";

if($nocurl){
	$method='file_get_contents';
	showvalue($method,$url);
	$scrape=file_get_contents($url);
}
else{
	$method='curl_contents';
	showvalue($method,$url);
	$scrape=curl_contents($url);
}
$roombase=explode('data-room-name-en="',$scrape);
showvalue('roombase count is ',count($roombase));
$i=0;
		foreach($roombase as $roomfind){
			$roomdisplays=explode('"',$roomfind);
			//showvalue('roomfind ROOM NAME SHOULD BE FIRST!!',$roomfind);
			showvalue("roomdispaly $i strleen is =",strlen($roomdisplays[0]),htmlentities($roomdisplays[0]));
			if($i){
				$rooms[]=$roomdisplays[0];
			}
			$i++;
		}
		$i=0;
		$lastprice=0;
		$thisroom=0;
		$roomcounter=0;
		$pricebase=explode('data-price-without-addons="$',$scrape);
		foreach($pricebase as $pricefind){
			$pricedisplays=explode('"',$pricefind);
			$pricefix=str_replace('$','',$pricedisplays[0]);
			$pricefix=floatval($pricefix);
			$allprices[]=$pricefix; //thisis for testing
			//booking has 2 arrays of prices per room -- the base price extended by staylength
			//array 1 = cash prices
			//array 2= reserve, no cash
			//each array starts with a base price
			//then the 1day, 2day extensions
			//also a lot of "1"s shwo up--so
			//always ignore the first element of these arrays ($i==0)
			//the roomcounter is 0 or 1
			//saving the cash price as [0] and the no cash reserve as [1]
			if($i&&$pricefix>1){
				//its price a price array
				if($lastprice==$pricefix){
					//if they are equal, you found the oneday price, equal to the base price
					//see this gets set right above $i++
					if(!$roomcounter){
						//you found the cash price
						$prices[]=$pricefix;
					}
					//save cash and reserve price for the rooms in the rooms array
					$roomprices[$thisroom][$roomcounter]=$pricefix;
					$roomcounter++;
				}
				if($roomcounter>1){
					//you found both cash and reserve price for this room so onto the next room
					$thisroom++;
					$roomcounter=0;
					//reset so you look for the next cash price
				}
				$lastprice=$pricefix;
				//to check if you found a new price array
			}
			$i++;
		}
		$baseroom=$rooms[0];
		$baseprice=$prices[0];

echo "<hr>ROOM IS $baseroom, PRICE IS $baseprice
<BR>data-price-without-addons=<BR>data-room-name-en=
<hr>";

showvalue($rooms,$prices,$roomprices, $allprices);

//$test=str_replace('data-price-without-addons="$','',$scrape);
//$test2=str_replace('data-room-name-en="','',$scrape);
//showvalue('ORIG '.strlen($scrape),'data-price-without-addons="$ '.strlen($test),'data-room-name-en=" '.strlen($test2));
showvalue(htmlentities($scrape));
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

if(false){
//	?>
//	in orbitz, look for
//	var utag_data =
//	in there find:
//	"roomTypeCode":"200243364","numAdults":0,"numChildren":0,"childAges":[],"numSeniors":0,"averagePrice":
//{"currency":"USD","amount":77.07},"numberOfRoomsAvailable":58,
//
//then look in:
//var roomsAndRatePlans = {"rooms":[
//and find:
//"roomTypeCode":"200243364",
//"name":"Deluxe Room, 2 Queen Beds",
//
//in getaroom.com have to reset date format
//https://www.getaroom.com/hotels/avanti-resort-orlando?check_in=09%2F28%2F2016&check_out=09%2F29%2F2016
//easy to find rooms, however
//  ga("ec:addImpression", {"id":"df74c506-8c27-41fd-8fc1-5fcfea22a8f2","name":"Avanti International Resort","brand":"Independent (INDY:INDIE)","dimension3":"Orlando",
//there are an array of these:
each has:
// "variant":"Deluxe Double Queen"
// "price":"77.4"

//<?
}
?>
