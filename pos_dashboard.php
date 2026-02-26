<?php
session_start();
require_once 'db_connect.php';

// Check admin/manager access
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS & Inventory Dashboard - Tavern Publico</title>
    <link rel="stylesheet" href="CSS/admin.css"> <!-- Reusing admin styles -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* POS Specific Styles */
        .pos-container { display: flex; flex-direction: column; height: calc(100vh - 80px); padding: 20px; gap: 20px; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; flex-direction: column; }
        .stat-card h3 { margin: 0 0 10px 0; font-size: 1.1em; color: #555; }
        .stat-value { font-size: 2em; font-weight: bold; color: #333; }

        /* Tabs */
        .tab-nav { display: flex; gap: 10px; border-bottom: 2px solid #eee; margin-bottom: 20px; }
        .tab-btn { padding: 10px 20px; border: none; background: none; cursor: pointer; font-weight: 600; color: #666; border-bottom: 3px solid transparent; transition: all 0.3s; }
        .tab-btn.active { color: #000; border-bottom-color: #FFD700; }
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }

        /* POS Grid */
        .pos-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; height: 100%; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; overflow-y: auto; padding-right: 10px; max-height: 70vh; }
        .menu-item-card { background: white; border: 1px solid #eee; border-radius: 8px; padding: 10px; text-align: center; cursor: pointer; transition: transform 0.2s; position: relative; }
        .menu-item-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .menu-item-card img { width: 100%; height: 100px; object-fit: cover; border-radius: 5px; margin-bottom: 8px; }
        .menu-item-card h4 { margin: 5px 0; font-size: 0.9em; }
        .menu-item-card .price { font-weight: bold; color: #d32f2f; }
        .stock-badge { position: absolute; top: 5px; right: 5px; background: #333; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7em; }
        .stock-low { background: #e74c3c; }

        /* Cart */
        .cart-panel { background: white; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; height: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .cart-items { flex-grow: 1; overflow-y: auto; margin-bottom: 15px; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .cart-total { font-size: 1.5em; font-weight: bold; text-align: right; margin-top: auto; padding-top: 15px; border-top: 2px solid #eee; }
        .checkout-btn { width: 100%; padding: 15px; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 1.2em; cursor: pointer; margin-top: 15px; font-weight: bold; }
        .checkout-btn:hover { background: #218838; }

        /* Inventory Table */
        .inventory-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .inventory-table th, .inventory-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        .inventory-table th { background-color: #f8f9fa; font-weight: 600; }

        /* Financial Progress */
        .progress-bar-container { width: 100%; height: 25px; background-color: #e0e0e0; border-radius: 12px; overflow: hidden; margin-top: 10px; }
        .progress-bar { height: 100%; background: linear-gradient(90deg, #FFD700, #ffb300); text-align: center; line-height: 25px; color: #333; font-weight: bold; width: 0%; transition: width 0.5s ease; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="page-wrapper">
        <aside class="admin-sidebar">
             <div class="sidebar-header"><img src="Tavern.png" alt="Home Icon" class="home-icon"></div>
            <nav>
                <ul class="sidebar-menu">
                    <li class="menu-item"><a href="admin.php"><i class="material-icons">dashboard</i> Dashboard</a></li>
                    <li class="menu-item active"><a href="pos_dashboard.php"><i class="material-icons">point_of_sale</i> POS & Stock</a></li> <!-- NEW LINK -->
                    <li class="menu-item"><a href="reservation.php"><i class="material-icons">event_note</i> Reservation</a></li>
                    <li class="menu-item"><a href="update.php"><i class="material-icons">file_upload</i> Upload Management</a></li>
                </ul>
            </nav>
        </aside>

        <div class="admin-content-area">
             <header class="main-header">
                <div class="header-content">
                    <h1 class="header-page-title">POS & Inventory Manager</h1>
                </div>
            </header>

            <main class="pos-container">

                <div class="tab-nav">
                    <button class="tab-btn active" onclick="openTab('dashboard')">Overview</button>
                    <button class="tab-btn" onclick="openTab('pos')">Point of Sale</button>
                    <button class="tab-btn" onclick="openTab('inventory')">Stock Management</button>
                    <button class="tab-btn" onclick="openTab('financials')">Financial Batch</button>
                </div>

                <!-- TAB 1: DASHBOARD -->
                <div id="dashboard" class="tab-content active">
                    <div class="dashboard-grid">
                        <div class="stat-card">
                            <h3>1st Batch Investment Recovery</h3>
                            <div class="stat-value" id="batchCollected">₱0.00</div>
                            <small>Goal: <span id="batchTarget">₱10,000.00</span></small>
                            <div class="progress-bar-container">
                                <div class="progress-bar" id="batchProgress" style="width: 0%">0%</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <h3>Today's Sales</h3>
                            <div class="stat-value" id="todaySales">₱0.00</div>
                        </div>
                        <div class="stat-card">
                            <h3>Low Stock Items</h3>
                            <div class="stat-value" id="lowStockCount" style="color: #e74c3c">0</div>
                        </div>
                    </div>
                    <div class="dashboard-grid" style="grid-template-columns: 2fr 1fr;">
                        <div class="stat-card">
                            <canvas id="salesChart"></canvas>
                        </div>
                        <div class="stat-card">
                            <h3>Top Selling Items</h3>
                            <canvas id="topItemsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: POS -->
                <div id="pos" class="tab-content">
                    <div class="pos-layout">
                        <div class="menu-grid" id="posMenuGrid">
                            <!-- Menu Items Injected via JS -->
                        </div>
                        <div class="cart-panel">
                            <h3>Current Order</h3>
                            <div class="cart-items" id="cartItems">
                                <p style="text-align: center; color: #999; margin-top: 50px;">Cart is empty</p>
                            </div>
                            <div class="cart-total">Total: ₱<span id="cartTotal">0.00</span></div>
                            <button class="checkout-btn" onclick="processCheckout()">Checkout (Cash)</button>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: INVENTORY -->
                <div id="inventory" class="tab-content">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <h2>Product Stock (Menu Items)</h2>
                        <button class="btn btn-primary" onclick="alert('Use Update.php to add items, use this table to quick-update stock')">Manage Menu</button>
                    </div>
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Current Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                            <!-- Injected JS -->
                        </tbody>
                    </table>
                </div>

                <!-- TAB 4: FINANCIALS -->
                <div id="financials" class="tab-content">
                    <div class="stat-card" style="max-width: 600px; margin: 0 auto;">
                        <h2>Manage Investment Batch</h2>
                        <p>All sales revenue is tracked against the active "Investment Batch". Use this to track ROI for your initial stock or specific funding rounds.</p>

                        <div class="form-group" style="margin-top: 20px;">
                            <label>Active Batch Name:</label>
                            <input type="text" id="activeBatchName" readonly value="Loading..." style="width: 100%; padding: 10px; margin-bottom: 15px;">

                            <label>Current Target Amount (₱):</label>
                            <input type="number" id="activeBatchTarget" style="width: 100%; padding: 10px; margin-bottom: 15px;">

                            <button class="btn btn-primary" onclick="updateBatchTarget()">Update Target</button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        // --- GLOBAL STATE ---
        let cart = [];
        let menuItems = [];

        // --- INIT ---
        document.addEventListener('DOMContentLoaded', () => {
            loadDashboardData();
            loadMenu();
            loadFinancials();

            // Poll for updates
            setInterval(loadDashboardData, 30000);
        });

        function openTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.querySelector(`button[onclick="openTab('${tabId}')"]`).classList.add('active');
        }

        // --- LOAD DATA ---
        async function loadMenu() {
            const res = await fetch('api_pos.php?action=get_menu');
            const data = await res.json();
            if(data.success) {
                menuItems = data.items;
                renderMenuGrid();
                renderInventoryTable();
            }
        }

        async function loadDashboardData() {
            // 1. Financial Status
            const resFin = await fetch('api_financials.php?action=get_status');
            const dataFin = await resFin.json();
            if(dataFin.success) {
                const batch = dataFin.batch;
                document.getElementById('batchCollected').textContent = '₱' + parseFloat(batch.collected_amount).toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('batchTarget').textContent = '₱' + parseFloat(batch.target_amount).toLocaleString(undefined, {minimumFractionDigits: 2});

                const percent = Math.min(100, (batch.collected_amount / batch.target_amount) * 100);
                document.getElementById('batchProgress').style.width = percent + '%';
                document.getElementById('batchProgress').textContent = Math.round(percent) + '%';
            }

            // 2. Charts
            loadCharts();
        }

        async function loadCharts() {
            // Sales Chart
            const resSales = await fetch('api_stats.php?action=sales_overview');
            const dataSales = await resSales.json();
            if(dataSales.success) {
                renderSalesChart(dataSales.labels, dataSales.data);
            }
        }

        // --- RENDERERS ---
        function renderMenuGrid() {
            const grid = document.getElementById('posMenuGrid');
            grid.innerHTML = menuItems.map(item => `
                <div class="menu-item-card" onclick="addToCart(${item.id})">
                    <span class="stock-badge ${item.stock_quantity < 10 ? 'stock-low' : ''}">${item.stock_quantity} Left</span>
                    <img src="${item.image || 'images/default_food.png'}" alt="${item.name}">
                    <h4>${item.name}</h4>
                    <div class="price">₱${item.price}</div>
                </div>
            `).join('');
        }

        function renderInventoryTable() {
            const tbody = document.getElementById('inventoryTableBody');
            tbody.innerHTML = menuItems.map(item => `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.category}</td>
                    <td>₱${item.price}</td>
                    <td>${item.stock_quantity}</td>
                    <td><button class="btn btn-small" disabled>Use POS to deduct</button></td>
                </tr>
            `).join('');
        }

        // --- POS LOGIC ---
        function addToCart(id) {
            const item = menuItems.find(i => i.id == id);
            if(!item) return;
            if(item.stock_quantity <= 0) { alert('Out of Stock!'); return; }

            const existing = cart.find(c => c.id == id);
            if(existing) {
                if(existing.quantity >= item.stock_quantity) { alert('Not enough stock!'); return; }
                existing.quantity++;
            } else {
                cart.push({ ...item, quantity: 1 });
            }
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            if(cart.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #999; margin-top: 50px;">Cart is empty</p>';
                document.getElementById('cartTotal').textContent = '0.00';
                return;
            }

            let total = 0;
            container.innerHTML = cart.map(item => {
                total += item.price * item.quantity;
                return `
                <div class="cart-item">
                    <div>
                        <strong>${item.name}</strong><br>
                        <small>₱${item.price} x ${item.quantity}</small>
                    </div>
                    <div>
                        <strong>₱${(item.price * item.quantity).toFixed(2)}</strong>
                        <button onclick="removeFromCart(${item.id})" style="background:none;border:none;color:red;cursor:pointer;margin-left:5px;">&times;</button>
                    </div>
                </div>
                `;
            }).join('');
            document.getElementById('cartTotal').textContent = total.toLocaleString(undefined, {minimumFractionDigits: 2});
        }

        function removeFromCart(id) {
            cart = cart.filter(c => c.id != id);
            renderCart();
        }

        async function processCheckout() {
            if(cart.length === 0) return;
            if(!confirm('Confirm checkout? This will deduct stock and record sale.')) return;

            const res = await fetch('api_pos.php?action=checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart: cart, payment_method: 'Cash' })
            });
            const data = await res.json();

            if(data.success) {
                alert('Order Successful!');
                cart = [];
                renderCart();
                loadMenu(); // Refresh stock
                loadDashboardData(); // Refresh financials
            } else {
                alert('Error: ' + data.message);
            }
        }

        // --- FINANCIALS ---
        async function loadFinancials() {
            const res = await fetch('api_financials.php?action=get_status');
            const data = await res.json();
            if(data.success) {
                document.getElementById('activeBatchName').value = data.batch.batch_name;
                document.getElementById('activeBatchTarget').value = data.batch.target_amount;
            }
        }

        async function updateBatchTarget() {
            const target = document.getElementById('activeBatchTarget').value;
            const res = await fetch('api_financials.php?action=update_target', {
                method: 'POST',
                body: JSON.stringify({ target: target })
            });
            const data = await res.json();
            if(data.success) {
                alert('Target Updated');
                loadDashboardData();
            }
        }

        // --- CHARTS ---
        let salesChartInstance = null;
        function renderSalesChart(labels, data) {
            const ctx = document.getElementById('salesChart').getContext('2d');
            if(salesChartInstance) salesChartInstance.destroy();

            salesChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Daily Sales (₱)',
                        data: data,
                        borderColor: '#FFD700',
                        backgroundColor: 'rgba(255, 215, 0, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false }, title: { display: true, text: 'Sales Overview (Last 30 Days)' } } }
            });
        }
    </script>
</body>
</html>
