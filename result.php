<html>
<head>
    <title>Uiniversity_of_Limerick_Timetable</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>

    </style>
</head>
<body>
<?php
function parse($id){

    $x= file("http://www.timetable.ul.ie/tt2.asp?T1=".$id);

    $day = -1;

    $item = '<p><font size="1" color="#800000"><b>';

    for($i=0;$i<count($x);$i++){
        if( trim($x[$i])== '<td align="justify" valign="top">' ||trim($x[$i])== '<td valign="top" align="justify">' )
            $day++;
        if( substr(trim($x[$i]),0,strlen($item))==$item){
            unset($class);
            while($i<count($x) && trim($x[$i])!='<hr>'){
                $class[]= $x[$i];
                $i++;
            }
            $c['start']=substr(trim($class[0]),-5);
            $c['end']=trim($class[2]);
            $c['module']=trim($class[4]);
            $c['length'] = intval(substr($c['start'],0,2))-intval(substr($c['end'],0,2));
            $c['room']=trim($class[8]);
            $c['modulename']=getModuleName($c['module']);
            //echo $c['modulename'];
            $c['type']=trim(strip_tags(str_replace('-','',$class[5])));
            $c['group']=trim($class[6]);
            $c['duration']=trim($class[11]);
            $data['classes'][$day][$c['start']][]=$c;


            for($j=1;$j<$c['length'];$j++){
                $start=(intval(substr($c['start'],0,2))+j).":00";
                $data['classes'][$day][$start][]=$c;
            }
        }

    }
    return $data;

}
function getModuleName($id){
    $x = file("http://193.1.101.55/tt_moduledetails_res.asp?T1=".$id);
    return (trim(strip_tags($x[38])));

}
//$calid = $_GET['id'];

$calid = "11095091";

$data=parse($calid);

echo '<table id="tt" border="solid">';


echo '<tr><td align="center">Monday</td><td align="center">Tuesday</td><td align="center"> Wednesday</td><td align="center">Thursday</td><td align="center">Friday</td><td align="center">Saturday</td></tr>';

echo '<tr>';
for($i=0; $i<6; $i++){
    echo '<td valign="top" align="justify">';
    for($j=9; $j<18; $j++){
        if($j<10)
            $start='0'.$j.':00';
        else
            $start= $j.':00';
        if(array_key_exists($i,$data['classes'])&&array_key_exists($start,$data['classes'][$i])){
            $ltime = count($data['classes'][$i][$start]);
            for($k=0; $k<$ltime; $k++){
                echo '<div draggable="true" style="background-color:#9EBBFF">';
                echo '<font color="#780000 ">'.$data['classes'][$i][$start][$k]['start'];
                echo "-".$data['classes'][$i][$start][$k]['end'];
                echo '<br />'.$data["classes"][$i][$start][$k]["module"].'<br />';
                echo $data['classes'][$i][$start][$k]['room'].'<br />';
                echo ($data['classes'][$i][$start][$k]['type']);

                if($data['classes'][$i][$start][$k]['type']!="LEC"){
                    echo '-'.$data['classes'][$i][$start][$k]['group'];
                }
                echo "<br />";
                echo $data['classes'][$i][$start][$k]['duration']."<br />";
                echo '</font>';
                echo '</div>';
                if(count($data['classes'][$i][$start]))
                    echo '<hr />';
            }
        }
    }
    echo '</td>';
}
echo "</tr></table>";
?>
<font size="serif" color="black"><a align="right" href="ical.php?id=<?php echo $calid ?>">Download Timetable in a File</a></font>


</body>
</html>
