<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Product - Vendor System</title>
    <link rel="stylesheet" href="../../assets/vendor_style.css?v=2">
</head>
<body>

    <a href="../../controllers/logout.php"
       style="position:absolute; top:15px; right:15px; background-color:#e74c3c; color:#fff;
              padding:8px 16px; border-radius:4px; text-decoration:none; font-weight:bold;
              font-family:Arial, sans-serif; z-index:999;">Logout</a>

    <?php include '../../controllers/vendor_controller/navbar.php'; ?>

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
            var lowStock = Number(p.quantity) < 3;
            var rowClass = lowStock ? ' class="low-stock-row"' : '';
            return '<tr' + rowClass + '>' +
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
            fetch('../../controllers/ajax_controller.php?action=search_products&q=' + encodeURIComponent(term))
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
