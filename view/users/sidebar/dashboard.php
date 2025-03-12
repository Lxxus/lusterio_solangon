<?php
include("../../../dB/config.php");

// Fetch stock summary
$stockSummaryQuery = "
    SELECT 
        SUM(CASE WHEN stock_quantity < 5 THEN stock_quantity ELSE 0 END) AS low_stock,
        SUM(CASE WHEN stock_quantity BETWEEN 5 AND 20 THEN stock_quantity ELSE 0 END) AS medium_stock,
        SUM(CASE WHEN stock_quantity > 20 THEN stock_quantity ELSE 0 END) AS high_stock
    FROM products";
$stockSummary = $conn->query($stockSummaryQuery)->fetch_assoc();

// Fetch recent user activity
$recentActivity = [
    ["Added 'Silver Necklace' to cart", "Mar 10, 2025"],
    ["Purchased 'Gold Ring'", "Mar 9, 2025"],
    ["Browsed 'Mystic Beads Bracelet'", "Mar 8, 2025"],
    ["Reviewed 'Diamond Earrings'", "Mar 7, 2025"],
    ["Wishlist updated - 'Pearl Pendant'", "Mar 6, 2025"],
    ["Logged in to account", "Mar 5, 2025"]
];


// Fetch featured products
$featuredProductsQuery = "SELECT product_name, price, stock_quantity FROM products ORDER BY RAND() LIMIT 3";
$featuredProducts = $conn->query($featuredProductsQuery);

// Fetch top-selling products
$topSellingQuery = "SELECT product_name, price, stock_quantity FROM products ORDER BY stock_quantity DESC LIMIT 3";
$topSellingProducts = $conn->query($topSellingQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: #F6F0F0;
            font-family: 'Poppins', sans-serif;
        }

        h2 {
            color: #735240;
            font-weight: bold;
        }

        .dashboard-card {
            border-radius: 12px;
            color: white;
            padding: 20px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .card-low { background: #F8C4B4; }
        .card-medium { background: #E5EBB2; }
        .card-high { background: #BCE29E; }
        .card-box { background: white; color: #333; padding: 20px; border-radius: 12px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1); }

        .btn-dark {
            background-color: #735240;
            border: none;
        }

        .btn-dark:hover {
            background-color: #5a3d2f;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
        }
        .equal-height-container {
            display: flex;
            gap: 20px;
            align-items: stretch;
        }

        .equal-height-container > .table-container {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            flex-grow: 1;
        }
        .product-list .card-box {
        flex: 1 1 calc(33.33% - 10px); /* Ensures 3 products fit evenly */
        max-width: 32%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 20px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background: white;
        margin-bottom: 20px;
    }

    .product-list .card-box h5 {
        margin-bottom: 10px;
    }

    .product-list .card-box p {
        margin: 5px 0;
    }

    .product-list .card-box .btn {
        margin-top: auto;
        background-color: #735240;
        color: white;
        border: none;
    }

    .product-list .card-box .btn:hover {
        background-color: #5a3d2f;
    }
        a {
            text-decoration: none !important;
            color: inherit;
        }
            a:hover, a:focus {
            text-decoration: none !important;
        }

    </style>
</head>
<body>

<div class="container py-4">
    <h2 class="mb-4">User Dashboard</h2>

    <div class="row">
        <!-- Stock Overview -->
        <div class="col-lg-4">
            <div class="card dashboard-card card-low mb-4">
                <div class="card-body">
                    <h5 class="fw-bold">Low Stock</h5>
                    <h4><?= $stockSummary['low_stock']; ?> Items</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card dashboard-card card-medium mb-4">
                <div class="card-body">
                    <h5 class="fw-bold">Medium Stock</h5>
                    <h4><?= $stockSummary['medium_stock']; ?> Items</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card dashboard-card card-high mb-4">
                <div class="card-body">
                    <h5 class="fw-bold">High Stock</h5>
                    <h4><?= $stockSummary['high_stock']; ?> Items</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Top Selling Products -->
    <div class="equal-height-container">
    <!-- Recent Activity -->
    <div class="table-container">
        <h4 class="fw-bold">Recent Activity</h4>
        <ul class="list-group flex-grow-1">
            <?php foreach ($recentActivity as $activity) : ?>
                <li class="list-group-item"><?= $activity[0] ?> <small class="text-muted"><?= $activity[1] ?></small></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Top Selling Products -->
    <div class="table-container">
        <h4 class="fw-bold">Top Selling Products</h4>
        <div class="product-list">
            <?php while ($product = $topSellingProducts->fetch_assoc()) : ?>
                <div class="card card-box text-center">
                    <h5><?= $product['product_name']; ?></h5>
                    <p>₱<?= number_format($product['price'], 2); ?></p>
                    <p class="text-muted">Stock: <?= $product['stock_quantity']; ?></p>
                    <button class="btn btn-sm btn-dark">View Details</button>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- Featured Products -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="table-container">
            <h4 class="fw-bold">Featured Products</h4>
            <div class="product-list">
                <?php while ($product = $featuredProducts->fetch_assoc()) : ?>
                    <div class="card card-box text-center">
                        <h5><?= $product['product_name']; ?></h5>
                        <p>₱<?= number_format($product['price'], 2); ?></p>
                        <p class="text-muted">Stock: <?= $product['stock_quantity']; ?></p>
                        <button class="btn btn-sm btn-dark">View Details</button>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

    <!-- Stock Summary Chart -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="chart-container">
                <h4 class="fw-bold">Stock Summary</h4>
                <canvas id="stockChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
var ctx = document.getElementById("stockChart").getContext("2d");
var stockChart = new Chart(ctx, {
    type: "bar",
    data: {
        labels: ["Low Stock", "Medium Stock", "High Stock"],
        datasets: [{ label: "Stock Count", data: [<?= $stockSummary['low_stock']; ?>, <?= $stockSummary['medium_stock']; ?>, <?= $stockSummary['high_stock']; ?>], backgroundColor: ["#F8C4B4", "#E5EBB2", "#BCE29E"] }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>
</body>
</html>
