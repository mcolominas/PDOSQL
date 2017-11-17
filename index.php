<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>MySQL PDO</title>

	<style>
		table,td, th {
 			border: 1px solid black;
 			border-spacing: 0px;
 		}
 		td, th{
 			padding: 2px;

 		}
	</style>
</head>
<body>
	<?php
		define("HOST", "localhost");
		define("USER", "root");
		define("PASSWORD", "1234");
		define("BBDD", "world");

		try{
			$pdo = new PDO ("mysql:host=".HOST.";dbname=".BBDD, 
							USER, 
							PASSWORD, 
							array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		}catch (PDOException $e){
			echo "Failed to get DB handle: ". $e->getMessage() ."\n";
			exit;
		}

		$consulta = "SELECT Continent FROM country GROUP BY Continent";
		$query = $pdo->prepare($consulta);
		$query->execute();
	?>
	<form action="index.php" method="post">
		<select name="id">
			<?php while($row = $query->fetch()){ 
				$selected = "";
				if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && $_POST['id'] == $row["Continent"])
					$selected = "selected"?>
				<option value="<?php echo $row["Continent"]; ?>" <?php echo $selected; ?>><?php echo $row["Continent"]; ?></option>";
			<?php } ?>
		</select>
		<input type="submit" name="enviar" value="enviar">
	</form>

	<?php 
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])){
			$nombreContienente = $_POST['id'];

			$consultaPoblacionContinente = "SELECT SUM(Population) FROM country WHERE Continent = '".$nombreContienente."';";
			$consultaPaisesContinente = "SELECT Name, Population FROM country WHERE Continent = '".$nombreContienente."';";

			$query = $pdo->prepare($consultaPoblacionContinente);
			$query->execute();
			$poblacionContienente = "";
			if($row = $query->fetch())
				$poblacionContienente = $row['SUM(Population)'];

			$query = $pdo->prepare($consultaPaisesContinente);
			$query->execute();
	?>

	<br>
	<table>
		<thead>
			<tr><td colspan="2" align="center" bgcolor="cyan">Llistat de paises de <?php echo $nombreContienente; ?><br>
			Población: <?php echo $poblacionContienente; ?></td></tr>
			<tr><th>Pais</th><th>Población</th></tr>
		</thead>
			<?php while($row = $query->fetch()){ ?>
	 			<tr>
		 			<td><?php echo $row["Name"]; ?></td>
		 			<td><?php echo $row["Population"]; ?></td>
	 			</tr>
 			<?php } ?>
	</table>

		<?php } ?>
</body>
</html>