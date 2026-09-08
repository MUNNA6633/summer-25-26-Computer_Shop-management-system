<?php
require_once __DIR__ . "/../../config/config.php";

// ---------- AJAX branch: runs only when JavaScript calls this same file ----------
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    $q = trim($_GET['q'] ?? '');
    $products = [];

    if ($q === '') {
        // No search term yet - return everything
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, category, wholesale_price, retail_price, quantity
             FROM products ORDER BY name ASC"
        );
        mysqli_stmt_execute($stmt);
    } else {
        $like = "%" . $q . "%";
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, category, wholesale_price, retail_price, quantity
             FROM products WHERE name LIKE ? OR category LIKE ? ORDER BY name ASC"
        );
        mysqli_stmt_bind_param($stmt, "ss", $like, $like);
        mysqli_stmt_execute($stmt);
    }

    // bind_result works without needing the mysqlnd driver
    mysqli_stmt_bind_result($stmt, $id, $name, $category, $wholesale_price, $retail_price, $quantity);

    while (mysqli_stmt_fetch($stmt)) {
        $products[] = [
            "id" => $id,
            "name" => $name,
            "category" => $category,
            "wholesale_price" => $wholesale_price,
            "retail_price" => $retail_price,
            "quantity" => $quantity,
        ];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    echo json_encode($products);
    exit;
}

// ---------- Normal branch: a real visitor loading the page ----------
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Product - Vendor System</title>
<link rel="stylesheet" href="../../assets/vendor_style.css">
</head>
<body>

    <?php include __DIR__ . "/../../controllers/vendor_controller/navbar.php";; ?>

    <div class="container">
        <h2>Check Product / Inventory</h2>

        <div class="search-box">
            <input type="text" id="productSearch" placeholder="Search by product name or category">
        </div>

        <p><span id="productCount">0</span> product(s) found</p>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Wholesale Price</th>
                <th>Retail Price</th>
                <th>Quantity</th>
            </tr>
            <tbody id="productTable">
                <!-- Rows are filled in by JavaScript after the AJAX search runs -->
            </tbody>
        </table>
    </div>

    <script>
        // Escapes text before inserting it into the page, so product names/categories
        // can never break the HTML or run as script (basic XSS protection)
        function esc(value) {
            var div = document.createElement('div');
            div.textContent = (value === null || value === undefined) ? '' : String(value);
            return div.innerHTML;
        }

        // Builds one <tr> for a single product
        function buildRow(p) {
            return '<tr>' +
                '<td>' + esc(p.id) + '</td>' +
                '<td>' + esc(p.name) + '</td>' +
                '<td>' + esc(p.category) + '</td>' +
                '<td>Tk ' + parseFloat(p.wholesale_price).toFixed(2) + '</td>' +
                '<td>Tk ' + parseFloat(p.retail_price).toFixed(2) + '</td>' +
                '<td>' + esc(p.quantity) + '</td>' +
                '</tr>';
        }

        // Fetches results from this same file's AJAX branch and refreshes the table
        function runSearch(term) {
            fetch('check_product.php?ajax=1&q=' + encodeURIComponent(term))
                .then(function (response) { return response.json(); })
                .then(function (products) {
                    var tbody = document.getElementById('productTable');
                    var count = document.getElementById('productCount');

                    count.textContent = products.length;

                    if (products.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6">No matching products found.</td></tr>';
                        return;
                    }

                    var html = '';
                    for (var i = 0; i < products.length; i++) {
                        html += buildRow(products[i]);
                    }
                    tbody.innerHTML = html;
                })
                .catch(function () {
                    document.getElementById('productTable').innerHTML =
                        '<tr><td colspan="6">Something went wrong loading products.</td></tr>';
                });
        }

        // Debounce: wait 300ms after the last keystroke before searching,
        // so we don't fire a request on every single letter typed
        var searchTimer = null;
        var searchBox = document.getElementById('productSearch');

        searchBox.addEventListener('input', function () {
            clearTimeout(searchTimer);
            var term = searchBox.value.trim();
            searchTimer = setTimeout(function () {
                runSearch(term);
            }, 300);
        });

        // Load the full product list once when the page first opens
        runSearch('');
    </script>

</body>
</html>
