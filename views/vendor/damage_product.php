<?php
require_once "../../controllers/vendor_controller/damage_product_process.php";

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Damage Product - Vendor System</title>
    <link rel="stylesheet" href="../../assets/vendor_style.css?v=2">
</head>
<body>

    <a href="../../controllers/logout.php"
       style="position:absolute; top:15px; right:15px; background-color:#e74c3c; color:#fff;
              padding:8px 16px; border-radius:4px; text-decoration:none; font-weight:bold;
              font-family:Arial, sans-serif; z-index:999;">Logout</a>

    <?php include '../../controllers/vendor_controller/navbar.php'; ?>

    <div class="container">
        <h2>Report Damaged Product</h2>

        <?php if ($flash): ?>
            <section class="summary">
                <p><?= $flash ?></p>
            </section>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
            <label for="productSearch">Product</label>
            <div class="search-box">
                <input type="text" id="productSearch" placeholder="Search by product name or category"
                       autocomplete="off" value="<?= $product ?>">
                <div id="productSuggestions" class="suggestions"></div>
            </div>
            <input type="hidden" id="product" name="product" value="<?= $product ?>">
            <?php if ($productErr): ?><span class="error"><?= $productErr ?></span><?php endif; ?>

            <label for="damage_qty">Damaged Quantity</label>
            <input type="number" id="damage_qty" name="damage_qty" placeholder="Enter damaged quantity" value="<?= $damage_qty ?>">
            <?php if ($damageQtyErr): ?><span class="error"><?= $damageQtyErr ?></span><?php endif; ?>

            <label for="note">Damage Note (optional)</label>
            <textarea id="note" name="note" rows="3" placeholder="Describe the damage (optional)"><?= $note ?></textarea>
            <?php if ($noteErr): ?><span class="error"><?= $noteErr ?></span><?php endif; ?>

            <button type="submit">Report Damage</button>
        </form>

        <h2>Recent Damage Reports</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Damaged Qty</th>
                <th>Note</th>
                <th>Reported At</th>
            </tr>
            <?php
            require "../../config/config.php";
            require "../../models/vendor_model.php";
            $reports = get_damage_reports($conn);
            mysqli_close($conn);
            foreach ($reports as $row):
            ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['product_name']; ?></td>
                <td><?= $row['damage_qty']; ?></td>
                <td><?= $row['note'] ?: '-'; ?></td>
                <td><?= $row['reported_at']; ?></td>
            </tr>
            <?php
            endforeach;
            ?>
        </table>
    </div>
    <script>
        function esc(value) {
            var div = document.createElement('div');
            div.textContent = (value === null || value === undefined) ? '' : String(value);
            return div.innerHTML;
        }

        var searchBox = document.getElementById('productSearch');
        var suggestions = document.getElementById('productSuggestions');
        var hiddenProduct = document.getElementById('product');

        function showSuggestions(products) {
            if (products.length === 0) {
                suggestions.innerHTML = '<div class="suggestion-empty">No matching products.</div>';
                suggestions.style.display = 'block';
                return;
            }

            var html = '';
            for (var i = 0; i < products.length; i++) {
                var p = products[i];
                html += '<div class="suggestion-item" data-name="' + esc(p.name) + '">' +
                    esc(p.name) + ' <span class="suggestion-meta">(' + esc(p.category) +
                    ' - in stock: ' + esc(p.quantity) + ')</span></div>';
            }
            suggestions.innerHTML = html;
            suggestions.style.display = 'block';
        }

        function runSearch(term) {
            if (term === '') {
                suggestions.style.display = 'none';
                return;
            }
            fetch('../../controllers/ajax_controller.php?action=search_products&q=' + encodeURIComponent(term))
                .then(function (response) { return response.json(); })
                .then(showSuggestions)
                .catch(function () {
                    suggestions.innerHTML = '<div class="suggestion-empty">Something went wrong.</div>';
                    suggestions.style.display = 'block';
                });
        }

        var searchTimer = null;
        searchBox.addEventListener('input', function () {
            hiddenProduct.value = '';               // typing invalidates any previous selection
            clearTimeout(searchTimer);
            var term = searchBox.value.trim();
            searchTimer = setTimeout(function () { runSearch(term); }, 300);
        });

        // Clicking a suggestion selects that exact product
        suggestions.addEventListener('click', function (e) {
            var item = e.target.closest('.suggestion-item');
            if (!item) return;
            var name = item.getAttribute('data-name');
            searchBox.value = name;
            hiddenProduct.value = name;
            suggestions.style.display = 'none';
        });

        document.addEventListener('click', function (e) {
            if (e.target !== searchBox) {
                suggestions.style.display = 'none';
            }
        });
    </script>

</body>
</html>
