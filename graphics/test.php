<? include '../classes/module.php'; ?>

<?
	$db = new DB();
	$htmlElements = new HtmlElements();
?>

<html>

<head>

<?= $htmlElements->bootstrapFiles() ?>
<?= $htmlElements->mainCssJsFiles() ?>
<?= $htmlElements->flotFiles() ?>	

<script>
var d1 = [
    [Date.UTC(2010,0, 1),160,100], [Date.UTC(2010,0, 2),133, 87], 
    [Date.UTC(2010,0, 3),138, 94], [Date.UTC(2010,0, 4),136, 84],  
    [Date.UTC(2010,0, 5),125, 78], [Date.UTC(2010,0, 6),131, 84],
    [Date.UTC(2010,0, 7),136, 84], [Date.UTC(2010,0, 8),160, 99], 
    [Date.UTC(2010,0, 9),123, 80], [Date.UTC(2010,0,10),138, 85],  
    [Date.UTC(2010,0,11),139, 85], [Date.UTC(2010,0,12),125, 79],
    [Date.UTC(2010,0,13),130, 79], [Date.UTC(2010,0,14),176, 92], 
    [Date.UTC(2010,0,15),137, 79], [Date.UTC(2010,0,16),124, 81],  
    [Date.UTC(2010,0,17),122, 74], [Date.UTC(2010,0,18),130, 82],
    [Date.UTC(2010,0,19),132, 76], [Date.UTC(2010,0,20),134, 83], 
    [Date.UTC(2010,0,21),126, 77], [Date.UTC(2010,0,22),126, 74],  
    [Date.UTC(2010,0,23),121, 79], [Date.UTC(2010,0,24),137, 72],
    [Date.UTC(2010,0,25),138, 74], [Date.UTC(2010,0,26),120, 79],
    [Date.UTC(2010,0,27),123, 79], [Date.UTC(2010,0,28),120, 72],  
    [Date.UTC(2010,0,29),119, 77], [Date.UTC(2010,0,30),131, 81],
    [Date.UTC(2010,0,31),133, 79], [Date.UTC(2010,1, 1),124, 77], 
    [Date.UTC(2010,1, 2),121, 73], [Date.UTC(2010,1, 3),115, 73],  
    [Date.UTC(2010,1, 4),130, 75], [Date.UTC(2010,1, 5),122, 74],
    [Date.UTC(2010,1, 6),117, 72],  [Date.UTC(2010,1, 7),118, 72]
];	
</script>

<script type="text/javascript">
	console.log(d1);
</script>

</head>

<body>
	123
</body>

</html>