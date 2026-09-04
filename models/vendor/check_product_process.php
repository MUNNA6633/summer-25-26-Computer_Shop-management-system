<?php
//  variables
$search = "";
$searchErr = "";
$isValid = false;
$searchResults = [];

function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}


$hasSearchParam = isset($_GET["search"]);

if ($hasSearchParam && trim($_GET["search"]) === "") {
    $searchErr = "Enter a product name or category to search.";
} elseif ($hasSearchParam) {
    $search = cleanInput($_GET["search"]);
    if (!preg_match("/^[a-zA-Z0-9 \"'.-]+$/", $search)) {
        $searchErr = "Only letters, numbers and spaces allowed.";
    }
}

$isValid = !$searchErr;

if ($isValid) {
    require "db.php";

    if ($hasSearchParam && $search !== "") {
       
        $likePattern = "%" . $search . "%";

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, category, wholesale_price, retail_price, quantity
             FROM products WHERE name LIKE ? OR category LIKE ? ORDER BY name ASC"
        );
        mysqli_stmt_bind_param($stmt, "ss", $likePattern, $likePattern);
        mysqli_stmt_execute($stmt);

       
        mysqli_stmt_bind_result($stmt, $id, $name, $category, $wholesale_price, $retail_price, $quantity);

        while (mysqli_stmt_fetch($stmt)) {
            $searchResults[] = [
                "id" => $id,
                "name" => $name,
                "category" => $category,
                "wholesale_price" => $wholesale_price,
                "retail_price" => $retail_price,
                "quantity" => $quantity,
            ];
        }

        mysqli_stmt_close($stmt);
    } else {
      
        $result = mysqli_query(
            $conn,
            "SELECT id, name, category, wholesale_price, retail_price, quantity
             FROM products ORDER BY name ASC"
        );
        while ($row = mysqli_fetch_assoc($result)) {
            $searchResults[] = $row;
        }
        mysqli_free_result($result);
    }

    mysqli_close($conn);
}
?>
