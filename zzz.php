<!DOCTYPE html>
<html>
<head>
<title>SwingShift Scraper</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
function validateurl(){
	document.getElementById('urlsubmit').disabled=true;
	myurl=$('#sitehotelurl').val();
	if(!myurl){
		return;
	}
	testtype=$('#site').val();
	if(testtype==1){//hotels.com
		mytest=myurl.replace('otels.com','')!=myurl;
		if(!mytest){
			alert('This does not appear to be a hotels.com url');
			return;
		}
		mytest=myurl.replace('hotel-id','')!=myurl;
		if(!mytest){
			alert('This URL does not appear be complete');
			return;
		}
	}
		if(testtype==2){//booking.com
		mytest=myurl.replace('booking.com','')!=myurl;
		if(!mytest){
			alert('This does not appear to be a booking.com url');
			return;
		}
		mytest=myurl.replace('dest_id','')!=myurl;
		if(!mytest){
			alert('This URL does not appear be complete');
			return;
		}
	}
	document.getElementById('urlsubmit').disabled=false;
}

function checkready(){
	hchoice=$('#hotel').val();
	schoice=$('#site').val();
	$('#urllabel').html('');
	$('#showme').hide();
	console.log('checkready with hchoice='+hchoice+' schoice='+schoice);
	if(hchoice&&schoice){
		thehotel=$('#hotel option:selected').html();
		thesite=$('#site option:selected').html();
	$('#urllabel').html('URL for '+thesite+', '+thehotel+':');
	$('#showme').show();
	}
	validateurl();
}
</script>
</head>
<body>
<? error_reporting(E_ERROR | E_PARSE);ini_set('display_errors',1);
define("DB_DATABASE", "SwingShift");
include_once '../jsqlutils.php';
if($_POST['sitehotelurl']){
	//showvalue($_POST);
	if($site==1){
		$idfindstart=explode('hotel-id=',$sitehotelurl);
		$idfindend=explode('&',$idfindstart[1]);
		$lookup1=$idfindend[0];
	}
	if($site==2){
		$bookingarray=explode('/',$sitehotelurl);
		$nationitem=count($bookingarray)-2;
		$showthis=count($bookingarray)-1;
		$lookup1=$bookingarray[$nationitem];
		$lookuptext=explode('?',$bookingarray[$showthis]);
		$lookup2=$lookuptext[0];
		$idfindstart=explode('dest_id=',$lookuptext[1]);
		$idfindend=explode(';',$idfindstart[1]);
		$lookup3=$idfindend[0];
	}
	//showvalue($lookup1,$lookup2,$lookup3);
	oneresult("DELETE FROM SiteHotels WHERE siteid=$site AND hotelid=$hotel");
	oneresult("INSERT INTO SiteHotels SET
	siteid=$site,
	hotelid=$hotel,
	lookup1='$lookup1',
	lookup2='$lookup2',
	lookup3='$lookup3'");
	$hotelname=oneresult("SELECT name FROM Hotels WHERE id=$hotel");
	$sitname=oneresult("SELECT name FROM Sites WHERE id=$site");
}

//$sitequery="SELECT CONCAT('<option value=',id,'>',name,'</option>') FROM Sites ORDER BY 1";
//$siteoptions=querytoarray($sitequery);
//$siteoptionstr=implode("\n",$siteoptions);
//$siteselect="<select name=site id=site onchange=checkready()>
//<option></option>
//$siteoptionstr
//</select>";
//WE ONLY HAVE Hotels.com, so:
$siteselect="<select name=site id=site onchange=checkready()>
<option value=1 selected>Hotels.com</option>
</select>";
$hotelquery="SELECT CONCAT('<option value=',id,'>',name,'</option>') FROM Hotels ORDER BY name";
$hoteloptions=querytoarray($hotelquery);
$hoteloptionstr=implode("\n",$hoteloptions);
$hotelselect="<select id=hotel onchange=checkready()  name=hotel>
<option></option>
$hoteloptionstr
</select>";
echo "
<style>
body {font-family:Helvetica,Arial; font-size:14px; color:#777 }
table {border:1px solid #CCC;border-radius:6px;}
td {padding:4px;}
</style>
<form method=POST>
<table style=width:750px;margin:20px;>
<tr><td colspan=4 style=cell-padding:0;padding:4px;background:#DDD;text-align:center;font-size:125%;font-weight:bold;>SETUP</td></tr>
<tr><td style=text-align:right;>Site</td><td>$siteselect</td><td style=text-align:right;>Hotel</td><td>$hotelselect</td></tr>
<tr id=showme style=display:none><td colspan=4><div id=urllabel></div> &nbsp;
<input id=sitehotelurl type=text oninput=validateurl() name=sitehotelurl style=width:680px></td></tr>
<tr><td colspan=4><center><input id=urlsubmit disabled=true type=submit value='Submit URL'></center></td></tr></table>
</form>";


$i=0;
while($i<60){
	$dates[]=oneresult("SELECT CURRENT_DATE() + INTERVAL $i DAY");
	$i++;
}
$fromhotelscom=querytoarray("SELECT CONCAT('<option value=',ssid,'>',name,'</option>')
FROM SiteHotels, Hotels
WHERE SiteHotels.siteid=1
AND Hotels.id=SiteHotels.hotelid");
$hotelscomptions=implode("\n",$fromhotelscom);
$hotels=" Hotel: <select id=ssid name=ssid>
<option></option>
$hotelscomptions
</select>";
foreach($dates as $date){
	if($did1){
		$datesout.="<option value=$date>$date</option>";
		}
		$did1=true;
	$datesin.="<option value=$date>$date</option>";
}
$hotels=str_replace($ssid,"$ssid selected",$hotels);
$datesout=str_replace("=$checkout","=$checkout selected",$datesout);
$datesin=str_replace("=$checkin","=$checkin selected",$datesin);
echo "<form method=POST>";
echo "Check in <select id=checkin name=checkin>$datesin</select>";
echo " Check out <select id=checkout name=checkout>$datesout</select>";
echo "$hotels
<input type=submit value='Find Rooms'>
</form>";

if($ssid&&$checkout&&$checkin){
	//showvalue($_POST);
	$hotelid=oneresult("SELECT id FROM Hotels WHERE ssid='$ssid'");
	$bookingchoice=1;
if($bookingchoice==1){
	$lookup1=oneresult("SELECT lookup1 FROM SiteHotels WHERE siteid=1 AND hotelid=$hotelid");

	echo "<h3>API=<a target=ss href=http://htwx.ws/ss/?ssid=$ssid&checkin=$checkin&checkout=$checkout>http://htwx.ws/ss/?ssid=$ssid&checkin=$checkin&checkout=$checkout</a> <input type=button onclick=getrooms() value='Get Rooms'></h3>";
echo "<script>
	function getrooms(){
		var allajax='http://htwx.ws/ss/?ssid=$ssid&checkin=$checkin&checkout=$checkout';
		$.ajax({
			async: false,
			url: allajax,
			type: 'GET',
			dataType : 'json',
			success: function(data, textStatus, xhr) {
				console.log(data);
hotelname=data.HotelName    ;
checkin=data.Checkin  ;
stay=data.Stay     ;
availability=data.Availablity;
roomtype=data.RoomType ;
baseprice=data.BasePrice;
extended=data.ExtendedPrice;
swingshiftprice=data.SwingShiftPrice;
html='Hotel Name: '+hotelname;
html+='<BR>Availability: '+availability;
html+='<BR>Check in: '+checkin;
html+='<BR>Length of Stay: '+stay;
html+='<BR>Room Type: '+roomtype;
html+='<BR>Base Price: $ '+baseprice+' per night';
html+='<BR>Extended Price: $ '+extended;
html+='<BR>SwingShift Price to Customer: $ '+swingshiftprice;
$('#results').html(html);
$('#results').attr('style','width:400px;margin:20px;padding:20px;background:#EDEDED;color:black;');
			}
		});
	}
</script>";

$url="https://www.hotels.com/hotel/details.html?q-check-out=$checkout&q-check-in=$checkin&q-room-0-adults=2&hotel-id=$lookup1";
echo "<BR><a target=_blank href=$url>VIEW ON HOTELS.COM</a>";
//timing'start');
echo "<div  id=results></div>";
die();
$origx=file_get_contents($url);
//echo "<h2>$url</h2><BR><BR>";
$x=$origx;

//showvalue($x);
	$roombase=explode('ria-labelledby="rr-header-room-type"><h3>',$x);
	foreach($roombase as $roomfind){
		$roomdisplays=explode('</h3>',$roomfind);
		if($firstroomwasskipped){
			$rooms[]=$roomdisplays[0];
		}
		$firstroomwasskipped=true;
	}

$pricebase=explode('class="current-price">',$x);
	foreach($pricebase as $pricefind){
		$pricedisplays=explode('</',$pricefind);
		if($firstpricewasskipped){
			$prices[]=$pricedisplays[0];
		}
		$firstpricewasskipped=true;
	}
}
if($bookingchoice==2){
	$lookup1=echoresult("SELECT lookup1 FROM SiteHotels WHERE siteid=2 AND hotelid=$hotelid");
	$lookup2=echoresult("SELECT lookup2 FROM SiteHotels WHERE siteid=2 AND hotelid=$hotelid");
	$lookup3=echoresult("SELECT lookup3 FROM SiteHotels WHERE siteid=2 AND hotelid=$hotelid");
	echo "<h3>Qstring=<a target=ss href=http://htwx.ws/ss/?ssid=$ssid&checkin=$checkin&checkout=$checkout>http://htwx.ws/ss/?ssid=$ssid&checkin=$checkin&checkout=$checkout</a></h3>";
showvalue($lookup1,$lookup2,$lookup3);
$url="http://www.booking.com/hotel/$lookup1/$lookup2?checkin=$checkin;checkout=$checkout;dest_id=$lookup3;group_adults=2;no_rooms=1;room1=A%2CA;sb_price_type=total;type=total;ucfs=1&";


//timing'start');
$origx=file_get_contents($url);
echo "<h2>$url</h2><BR><BR>";
$x=$origx;
$test=str_replace('<i class="b-sprite icon_open"></i>','',$x);
showvalue(htmlentities($x));

//showvalue($x);
	$roombase=explode('<i class="b-sprite icon_open"></i>',$x);
	foreach($roombase as $roomfind){
		$roomdisplays=explode('</a>',$roomfind);
		if($firstroomwasskipped){
			$rooms[]=$roomdisplays[0];
		}
		$firstroomwasskipped=true;
	}

$pricebase=explode('class="current-price">',$x);
	foreach($pricebase as $pricefind){
		$pricedisplays=explode('</',$pricefind);
		if($firstpricewasskipped){
			$prices[]=$pricedisplays[0];
		}
		$firstpricewasskipped=true;
	}
}
	//showvalue("<h2>Now with MODIFIED PRCIE FIND</h2><a target=_blank href=$url>VIEW ON HOTELS.COM</a>","<h3>available rooms:</h3>",$rooms, "prices: ",$prices);

//		showvalue('COUNT X1!!!!!!', count($x1),'NOW EACH x1',$x1);
}


/*
Setup for booking.com
$hotels=" Booking.com hotel: <select id=hotelid name=hotelid>
<option value=the-plaza.html>plaza  </option>
<option value=newyork-510-west-42.html> out nyc</option>
<option value=westin-beach-resort-fort-lauderdale.html>westin ft laud </option>
</select>";

http://www.booking.com/hotel/us/$hotelid?checkin=$checkin;checkout=$checkout;group_adults=2;no_rooms=1;
http://www.booking.com/hotel/us/the-plaza.html?checkin=2016-07-30;checkout=2016-07-31;dest_id=20088325;group_adults=2;no_rooms=1;room1=A%2CA;sb_price_type=total;
http://www.booking.com/hotel/us/newyork-510-west-42.html?checkin=2016-07-30;checkout=2016-07-31;dest_id=20088325;group_adults=2;no_rooms=1;room1=A%2CA;sb_price_type=total;
https://www.booking.com/hotel/us/westin-beach-resort-fort-lauderdale.html?checkin=2016-07-30;checkout=2016-07-31;dest_id=20088325;group_adults=2;no_rooms=1;room1=A%2CA;sb_price_type=total;

explode ('data-room-name-en="',$x);
foreach(
explode '"',

http://www.booking.com/hotel/us/new-york-517-lexington-avenue.html?label=gen173nr-1FCAEoggJCAlhYSDNiBW5vcmVmcgV1c19uY4gBAZgBMbgBDMgBDNgBAegBAfgBAqgCAw;sid=8c30148792c963281e9f9aad1ece6ef1;all_sr_blocks=33073009_91472596_0_1_0;checkin=2016-09-22;checkout=2016-09-23;dest_id=20088325;dest_type=city;dist=0;group_adults=2;highlighted_blocks=33073009_91472596_0_1_0;hpos=1;room1=A%2CA;sb_price_type=total;srfid=0895763db0adcc07eee20ce0ff493d8a8fe1873bX1;type=total;ucfs=1&
http://www.booking.com/hotel/us/garden-inn-queens-jfk.html?dest_id=20089077;all_sr_blocks=5975104_85067799_2_2_0;checkin=2016-09-13;checkout=2016-09-14;group_adults=2;no_rooms=1;room1=A%2CA;sb_price_type=total;type=total;ucfs=1&
http://www.booking.com/hotel/us/new-york-517-lexington-avenue.html?dest_id=20088325;all_sr_blocks=5975104_85067799_2_2_0;checkin=2016-09-13;checkout=2016-09-14;group_adults=2;no_rooms=1;room1=A%2CA;sb_price_type=total;type=total;ucfs=1&
dest_id=20088325;
http://www.booking.com/hotel/us/new-york-517-lexington-avenue.html?label=gen173nr-1FCAEoggJCAlhYSDNiBW5vcmVmcgV1c19uY4gBAZgBMbgBDMgBDNgBAegBAfgBAqgCAw;sid=8c30148792c963281e9f9aad1ece6ef1;all_sr_blocks=33073009_91472596_0_1_0;checkin=2016-09-22;checkout=2016-09-23;dest_id=20088325;dest_type=city;dist=0;group_adults=2;highlighted_blocks=33073009_91472596_0_1_0;hpos=1;room1=A%2CA;sb_price_type=total;srfid=0895763db0adcc07eee20ce0ff493d8a8fe1873bX1;type=total;ucfs=1

THIS IS HOW YOU DO A left JOIN for progress

SELECT Hotels.name, if(min(SiteHotels.siteid)=1,'hotels.com',''),if(max(SiteHotels.siteid)=2,'booking.com','')
FROM Hotels
LEFT JOIN SiteHotels ON Hotels.id = SiteHotels.hotelid

Group by 1 ORDER BY 1

*/


?>
</body>
</html>
