<? 
$filename = "report-".date('d')."-".date('m')."-20".date('y')."-".date('H')."-".date('i').".csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename); 
?>

<? include '../classes/module.php'; ?>

<?

$db = new DB();
$db->defaultConnect();

$output = "";
$sql = mysql_query("SELECT * FROM `moiai_orderGoals`");
$columns_total = mysql_num_fields($sql);

// Get The Field Name

for ($i = 0; $i < $columns_total; $i++) {
$heading = mysql_field_name($sql, $i);
$output .= '"'.$heading.'";';
}
$output .="\n";

// Get Records from the table

while ($row = mysql_fetch_array($sql)) {
for ($i = 0; $i < $columns_total; $i++) {
$output .='"'.$row["$i"].'";';
}
$output .="\n";
}

// Download the file

$filename = "myFile.csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);

echo $output;
exit;

?>