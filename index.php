<?php

$host	= "localhost";
$port	= "3307";
$dbname	= "p7";
$charset = "utf8";
$user	= "root";
$pass	= "root";

try {
	$db = new PDO(
		"mysql:host=$host:$port;dbname=$dbname;charset=$charset",
		$user,
		$pass,
		[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
	);
} catch (Exception $e) {
	die("Erreur : " . $e->getMessage());
};

/**
 *! CRUD
 */

// READ
function select($db, $enabled = "BOTH") {

	if ($enabled === "BOTH") {
		$query = "SELECT * FROM users";
		$stmt = $db->prepare($query);
		$stmt->execute();
	} else {
		$query = "SELECT * FROM users WHERE enabled = :enabled";
		$stmt = $db->prepare($query);
		$stmt->execute([
			"enabled" => $enabled,
		]);
	};

	$users = $stmt->fetchAll();
	return $users;

}

// CREATE
function insert($db, $name, $enabled) {
	
	$query = "INSERT INTO users(name, enabled) VALUES (:name, :enabled)";
	$stmt = $db->prepare($query);

	$stmt->execute([
		"name" => $name,
		"enabled" => $enabled,
	]);

}

function update($db, $id, $name = NULL, $enabled = NULL) {

	if (!isset($name) && !isset($enabled)) return;
	
	// $modifications = [];

	if (isset($name)) {
		// array_push($modifications, "name" => $name);

		$query = "UPDATE users SET name = :name WHERE id = :id";
		$stmt = $db->prepare($query);

		$stmt->execute([
			"id" => $id,
			"name" => $name,
		]);

	}

	if (isset($enabled)) {
		// array_push($modifications, "enabled" => $enabled);

		$query = "UPDATE users SET name = :name WHERE id = :id";
		$stmt = $db->prepare($query);

		$stmt->execute([
			"id" => $id,
			"enabled" => $enabled,
		]);

	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	if ($_POST["type"] === "CREATE") {
		$name = $_POST["name"];
		$enabled = $_POST["enabled"];
	
		insert($db, $name, $enabled);
	}

	if ($_POST["type"] === "UPDATE") {
		$id = $_POST["user"];
		$name = $_POST["name"];
		$enabled = $_POST["enabled"];

		update($db, $id, $name, $enabled);
	}

	if ($_POST["type"] === "DELETE") {

	}

}

$users = select($db);
$usersEnabled = select($db, true);
$usersDisabled = select($db, false);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>P7</title>
</head>
<body>
	<h1>CRUD</h1>
	<div>
		<h3>READ</h3>
		<?php foreach ($users as $user) { ?>
			<p><?= $user["id"] . " - " . $user["name"] . " - " . $user["enabled"]; ?></p>
		<?php } ?>
	</div>
	<div>
		<h3>CREATE</h3>
		<form method="POST" action=<?= $_SERVER["PHP_SELF"]; ?> >
			<input type="hidden" name="type" value="CREATE" />
			Name : <input type="text" name="name" /><br>
			Enabled : <select name="enabled" required>
				<option value="1">True</option>
				<option value="0">False</option>
			</select><br>
			<button type="submit">Ajouter</button>
		</form>
	</div>
	<div>
		<h3>UPDATE</h3>
		<form method="POST" action=<?= $_SERVER["PHP_SELF"]; ?> >
			<input type="hidden" name="type" value="UPDATE" />
			User : <select name="user" required>
				<?php foreach ($users as $user) { ?>
					<option value=<?= $user["id"]; ?> ><?= $user["id"] . " - " . $user["name"] ?></option>
				<?php } ?>
			</select><br>
			Name : <input type="text" name="name" /><br>
			Enabled : <select name="enabled" required>
				<option value="1">True</option>
				<option value="0">False</option>
			</select><br>
			<button type="submit">Ajouter</button>
		</form>
	</div>
</body>
</html>