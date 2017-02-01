<?
if($hotelid&&$checkout&&$checkin){
$url="https://www.hotels.com/hotel/details.html?q-check-out=$checkout&q-check-in=$checkin&q-room-0-adults=2&hotel-id=$hotelid";
$x=file_get_contents($url);
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
echo "Room type: $rooms[0] Prcie: $prices[0]";
}
?>