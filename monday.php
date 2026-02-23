<?php
//  $totalstudent="20";
//  $totalpresent="20";

//  $maths="sarey bache hai aj bum maths prhege";
//  $arts="sb nahi aw hai to arts prhege";
// if ($totalpresent==$totalstudent) {
//      echo "$maths";
// }
// else {
//     echo "$arts";
    

// }
//     echo"<br>";
// $fruits=["apple", "banana"];
// print_r($fruits);


// echo"<br>";
// $automatic=array("washing machine"=>array("color"=>"white",
// "price"=>"100,000"));

// $machine=array($automatic);

// print_r($machine);

// echo"<br>";

// $A=$A1grade:80;
// $B=$Agrade:70;
// $C=$Bgrade:60;

// if ($salma==$A1grade:80) {
//     echo $A;
// }
// else {
//     echo $B;
// }

// $fruits=["apple" , "banana" , "kiwi"];

  
// for ($i=0; $i < count($fruits); $i++) { 
//     print_r "".($fruits[$i]) ."";
// }

// $students_report = [
//     ["Name" => "Sania", "Grade" => "A" ,"average"=>"80", "total"=>"120"],
// ];
// echo "<pre>";
// print_r($students_report);
// echo "</pre>";

// $students_report = [
//     ["Name" => "Sania", "Grade" => "A", "average" => "80", "total" => "120"],
//     ["Name" => "John", "Grade" => "B", "average" => "70", "total" => "110"], 
//      ["Name" => "hussain", "Grade" => "A", "average" => "80", "total" => "120"],
//     ["Name" => "abbas", "Grade" => "B", "average" => "70", "total" => "110"], 
// ];

// for ($i = 0; $i < count($students_report); $i++) {
//     echo "<h3>Student Record " . ($i + 1) . "</h3>";
    
    
//     echo "Name: " . $students_report[$i]["Name"] . "<br>";
//     echo "Total: " . $students_report[$i]["total"] . "<br>";
//     echo "Average: " . $students_report[$i]["average"] . "<br>";
//     echo "Grade: " . $students_report[$i]["Grade"] . "<br>";
//     echo "<hr>"; 
// }

$aboutfruits=[
     
    ["name"=>"apple",
    "nutriteint"=>"vitamin A",
    "calories"=>"120"],

     ["name"=>"apple",
    "nutriteint"=>"vitamin A",
    "calories"=>"120"],

     [
    "name"=>"apple",
    "nutriteint"=>"vitamin A",
    "calories"=>"120"
    ],
];
   
foreach($aboutfruits as $fruits) {
    foreach($fruits as $key=>$value){
        echo $key . ": " . $value . "<br>";
    }
}

$i=1;
while($i <= 10){
    echo "10 *" . $i . " = " . 10*$i ." ; ";
    $i++; 
}

echo"<br>";
echo"<br>";

for ($i=1; $i < 10; $i++) { 
    echo "10 *" . $i . " = " . 10*$i ." ; ";
}
echo"<br>";
echo"<br>";
$i = 1;
do  {

     echo "10 *" . $i . " = " . 10*$i ." ; "; 
     $i++;
}while($i <= 10)
?>