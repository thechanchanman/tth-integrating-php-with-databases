<?php
function get_catalog_count($category = null) {
	$category = strtolower($category);
	include("connection.php");

	try {
		$sql = "SELECT COUNT(media_id) FROM Media";
		if (!empty($category)) {
			$result = $db->prepare($sql . " WHERE LOWER(category) = ?");
			$result->bindParam(1, $category, PDO::PARAM_STR);
		} else {
			$result = $db->prepare($sql);
		}
		$result->execute();
	} catch (Exception $e) {
		echo "bad query";
	}

	$count = $result->fetchColumn(0);
	return $count;
}

function full_catalog_array($limit = null, $offset = 0) {
	include("connection.php");

	try {
		$sql = "SELECT media_id, title, category, img
						FROM Media
						ORDER BY
						REPLACE(
							REPLACE(
								REPLACE(title, 'The ',''),
							'An ',''),
						'A ','')";
		if (is_integer($limit)) {
			$results = $db->prepare($sql . " LIMIT ? OFFSET ?");
			$results->bindParam(1, $limit, PDO::PARAM_INT);
			$results->bindParam(2, $offset, PDO::PARAM_INT);
		} else {
			$results = $db->prepare($sql);
		}
		$results->execute();
	} catch (Exception $e) {
		echo "Unable to retrieve results";
		exit;
	}

	$catalog = $results->fetchAll(PDO::FETCH_ASSOC);
	return $catalog;
}

function category_catalog_array($category, $limit = null, $offset = 0) {
	include("connection.php");
	$category = strtolower($category);
	try {
		$sql = "SELECT media_id, title, category, img
						FROM Media
						WHERE LOWER(category) = ?
						ORDER BY
						REPLACE(
							REPLACE(
								REPLACE(title, 'The ',''),
							'An ',''),
						'A ','')";
		if (is_integer($limit)) {
			$results = $db->prepare($sql . " LIMIT ? OFFSET ?");
			$results->bindParam(1, $category, PDO::PARAM_STR);
			$results->bindParam(2, $limit, PDO::PARAM_INT);
			$results->bindParam(3, $offset, PDO::PARAM_INT);
		} else {
			$results = $db->prepare($sql);
			$results->bindParam(1, $category,PDO::PARAM_STR);
		}
		$results->execute();
	} catch (Exception $e) {
		echo "Unable to retrieve results";
		exit;
	}

	$catalog = $results->fetchAll(PDO::FETCH_ASSOC);
	return $catalog;
}

function random_catalog_array() {
	include("connection.php");

	try {
		$results = $db->query("SELECT media_id, title, category, img FROM Media ORDER BY RANDOM() LIMIT 4");
	} catch (Exception $e) {
		echo "Unable to retrieve results";
		exit;
	}

	$catalog = $results->fetchAll(PDO::FETCH_ASSOC);
	return $catalog;
}

function get_item_html($item) {
	$output =
		'<li><a href="details.php?id=' . $item["media_id"] . '"><img src="'	. $item["img"] . '" alt="' . $item["title"] . '" />'
		. '<p>View Details</p></a></li>';
	return $output;
}

function array_category($catalog, $category) {
	$output = [];

	foreach ($catalog as $id => $item) {
		if ($category == null OR strtolower($category) == strtolower($item["category"])) {
			$sort = $item["title"];
			$sort = ltrim($sort, "The ");
			$sort = ltrim($sort, "A ");
			$sort = ltrim($sort, "An ");
			$output[$id] = $sort;
		}
	}

	asort($output);
	return array_keys($output);
}

function genre_array($category = null) {
	$category = strtolower($category);
	include("connection.php");

	try {
		$sql = "SELECT genre, category FROM Genres JOIN Genre_Categories ON Genres.genre_id = Genre_Categories.genre_id ";
		if (!empty($category)) {
			$results = $db->prepare($sql . " WHERE LOWER(category) = ? ORDER BY genre");
			$results = $db->bindParam(1, $category, PDO::PARAM_STR);
		} else {
			$results = $db->prepare($sql . " ORDER BY genre");
		}
		$results->execute();
	} catch (Exception $e) {
			echo "Bad query";
	}

	$genres = [];
	while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
		$genres[$row["category"]][] = $row["genre"];
	}
	return $genres;
}
