<?
$diff = floor((gmmktime(0,0,0,1,1,2006) - mktime(0,0,0,1,1,2006)) / 3600);
$TimeZone = $diff * 3600;
$infos1 = getdate(mktime(0, 0, 0, 1, 1, 2006));
$infos2 = getdate(gmmktime(0, 0, 0, 1, 1, 2006));
$infos3 = getdate(gmmktime(0, 0, - $TimeZone, 1, 1, 2006));
$today1 = getdate(mktime());
$today2 = getdate(gmmktime());
$today3 = getdate(gmmktime() - $TimeZone);

?>


<pre>
Time Difference: <?php echo $diff ?><br />
date: <? echo date("Y-m-d H:i:s", time()) ?><br />
gmdate: <? echo gmdate("Y-m-d H:i:s", time()) ?><br />
date <?php echo $diff ?>: <? echo date("Y-m-d H:i:s", time() + $TimeZone) ?><br />
gmdate <?php echo $diff ?>: <? echo gmdate("Y-m-d H:i:s", time() + $TimeZone) ?><br /><br />
<hr />
getdate-mktime: (1/1/2006 00:00:00):<? print_r($infos1) ?><br />
getdate-gmmktime: (1/1/2006 00:00:00):<? print_r($infos2) ?><br />
getdate-gmmktime <?php echo -$diff ?>: (1/1/2006 00:00:00):<? print_r($infos3) ?><br />
<hr />
getdate-mktime: (now):<? print_r($today1) ?><br />
getdate-gmmktime: (now):<? print_r($today2) ?><br />
getdate-gmmktime <?php echo -$diff ?>: (now):<? print_r($today3) ?><br />

</pre>
