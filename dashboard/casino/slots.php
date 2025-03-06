<?php include '../inc/header.php'; ?>

<main class="container-flex">
    <!-- Left Section - Quick Links & Search Bar -->
    <div class="left-section">
        <div class="quick-links">
            <a class="link" href="http://localhost/">Home</a>
            <a class="link" href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a>
            <a class="link" href="../inc/open_folder.php">Open Projects Folder</a>
            <a class="link" href="casino/slots.php">🎰 Play Slots</a>
        </div>

        <div class="search-container">
            <input type="text" id="search-bar" placeholder="Search projects..." onkeyup="filterProjects()">
        </div>
    </div>

    <!-- Right Section - Server Info + Projects -->
    <div class="right-section">
        <div class="server-info">
            <p>PHP Version: <?php echo phpversion(); ?></p>
            <p>MySQL: <?php echo (mysqli_connect("localhost", "root", "") ? "Running" : "Not Running"); ?></p>
        </div>

        <main class="container-flex">
            <div class="slot-container">
                <h2>🎰 Slot Machine</h2>
                <p>Balance: <span id="balance">100</span> Coins</p> <br>
                <p>Choose Bet Amount:</p>
                <input type="number" id="bet-amount" min="1" max="100" value="10">

                <div class="slot-machine">
                    <div class="reel" id="reel1">🍒</div>
                    <div class="reel" id="reel2">🍋</div>
                    <div class="reel" id="reel3">🍉</div>
                </div>

                <button id="spin-button">Spin</button>
                <p id="result-message">Click Spin to Play!</p> <br>
            </div>

            <div class="slot-container">
                <h2>⛏️ Mine for Coins</h2>
                <button id="mine-button">⛏️ Mine</button>
                <p id="mine-result">Click the pickaxe to start mining!</p>
            </div>

            <div class="slot-container">
                <p>If you run out of coins, <br> add more:</p>
                <input type="number" id="add-coins" min="10" max="500" value="50">
                <button id="add-coins-button" disabled>Add Coins</button> <!-- Button starts locked -->
                <br><br>
                <p>Repay Debt:</p>
                <input type="number" id="repay-amount" min="1" value="10">
                <button id="repay-debt-button" disabled>Repay Debt</button> <br><br>
                <p>Debt: <span id="debt">0</span> Coins</p>
            </div>

            <!-- Stock Market UI -->
            <div class="slot-container">
                <h2>📈 Fake Stock Market</h2>
                <p>Stock Price: <span id="stock-price">100.00</span> Coins</p>
                <p>Your Investment: <span id="stock-investment">0.00</span> Coins</p>
                <input type="number" id="invest-amount" min="10" max="500" value="10">
                <button id="invest-button">📥 Invest</button>
                <button id="sell-button" disabled>📤 Sell</button>
                <p id="stock-message">Watch the market and invest wisely!</p>

                <!-- Stock Price Graph -->
                <canvas id="stockChart"></canvas>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        </main>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let balance = localStorage.getItem("balance") ? parseInt(localStorage.getItem("balance")) : 100;
                let debt = localStorage.getItem("debt") ? parseFloat(localStorage.getItem("debt")) : 0;
                let borrowCount = localStorage.getItem("borrowCount") ? parseInt(localStorage.getItem("borrowCount")) : 0;
                let stockPrice = localStorage.getItem("stockPrice") ? parseFloat(localStorage.getItem("stockPrice")) : 100;
                let stockInvestment = localStorage.getItem("stockInvestment") ? parseFloat(localStorage.getItem("stockInvestment")) : 0;
                let stockHistory = JSON.parse(localStorage.getItem("stockHistory")) || [100];

                const balanceElement = document.getElementById("balance");
                const debtElement = document.getElementById("debt");
                const mineButton = document.getElementById("mine-button");
                const mineResult = document.getElementById("mine-result");
                const stockPriceElement = document.getElementById("stock-price");
                const stockInvestmentElement = document.getElementById("stock-investment");
                const investButton = document.getElementById("invest-button");
                const sellButton = document.getElementById("sell-button");
                const stockMessage = document.getElementById("stock-message");
                const investAmountInput = document.getElementById("invest-amount");

                function updateButtons() {
                    if (balance <= 0) {
                        addCoinsButton.removeAttribute("disabled");
                    } else {
                        addCoinsButton.setAttribute("disabled", true);
                    }

                    if (debt > 0 && balance > 0) {
                        repayDebtButton.removeAttribute("disabled");
                    } else {
                        repayDebtButton.setAttribute("disabled", true);
                    }
                }

                function updateBalance(amount, message) {
                    balance += amount;
                    balanceElement.innerText = balance;
                    mineResult.innerText = message;
                    updateStorage();
                }

                function updateStorage() {
                    localStorage.setItem("balance", balance);
                    localStorage.setItem("debt", debt.toFixed(2));
                    localStorage.setItem("stockPrice", stockPrice.toFixed(2));
                    localStorage.setItem("stockInvestment", stockInvestment.toFixed(2));
                    localStorage.setItem("stockHistory", JSON.stringify(stockHistory));
                }

                function updateStockDisplay() {
                    stockPriceElement.innerText = stockPrice.toFixed(2);
                    stockInvestmentElement.innerText = stockInvestment.toFixed(2);

                    if (stockInvestment > 0) {
                        sellButton.removeAttribute("disabled");
                    } else {
                        sellButton.setAttribute("disabled", true);
                    }
                }

                // Initialize stock chart
                const ctx = document.getElementById("stockChart").getContext("2d");
                const stockChart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: Array.from({
                            length: stockHistory.length
                        }, (_, i) => i + 1),
                        datasets: [{
                            label: "Stock Price",
                            data: stockHistory,
                            borderColor: "rgba(75, 192, 192, 1)",
                            backgroundColor: "rgba(75, 192, 192, 0.2)",
                            borderWidth: 2,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: false
                            }
                        }
                    }
                });


                function updateChart() {
                    stockChart.data.labels.push(stockChart.data.labels.length + 1);
                    stockChart.data.datasets[0].data.push(stockPrice);
                    if (stockChart.data.labels.length > 20) {
                        stockChart.data.labels.shift();
                        stockChart.data.datasets[0].data.shift();
                    }
                    stockChart.update();
                }

                // Stock price fluctuation every 5 seconds
                setInterval(() => {
                    let change = (Math.random() * 10 - 5).toFixed(2); // -5% to +5% change
                    stockPrice += (stockPrice * change) / 100;
                    stockPrice = Math.max(stockPrice, 10); // Minimum stock price is 10
                    stockHistory.push(stockPrice);
                    if (stockHistory.length > 20) stockHistory.shift();
                    updateStockDisplay();
                    updateStorage();
                    updateChart();
                }, 5000);

                // Invest in stock
                investButton.addEventListener("click", function() {
                    let investAmount = parseInt(investAmountInput.value);

                    if (investAmount > balance) {
                        stockMessage.innerText = "❌ Not enough coins to invest!";
                        return;
                    }

                    balance -= investAmount;
                    stockInvestment += investAmount / stockPrice; // Store investment in stock units

                    balanceElement.innerText = balance;
                    updateStockDisplay();
                    updateStorage();
                    stockMessage.innerText = `✅ Invested ${investAmount} coins at ${stockPrice.toFixed(2)}!`;
                });

                // Sell stocks
                sellButton.addEventListener("click", function() {
                    let sellValue = Math.floor(stockInvestment * stockPrice * 100) / 100; // Rounds down to 2 decimals
                    balance += sellValue;
                    stockInvestment = 0;

                    balanceElement.innerText = balance.toFixed(2); // Ensures balance is always shown with 2 decimals
                    updateStockDisplay();
                    updateStorage();
                    stockMessage.innerText = `📤 Sold your stocks for ${sellValue.toFixed(2)} coins!`;
                });

                // Mining Mechanic with Cooldown
                mineButton.addEventListener("click", function() {
                    if (mineButton.disabled) return; // Prevents multiple clicks

                    mineButton.disabled = true; // Disable button
                    setTimeout(() => {
                        mineButton.disabled = false; // Re-enable after 0.5s
                    }, 500);

                    let find = Math.random() * 100; // Generate a random number between 0-100
                    let earned = 1;
                    let message = "⛏️ You mined +1 coin!";

                    if (find < 0.5) { // 0.5% chance
                        earned = 50;
                        message = "💎 You found a Diamond! +50 coins!";
                    } else if (find < 1.5) { // 1% chance
                        earned = 25;
                        message = "🏆 You struck Gold! +25 coins!";
                    } else if (find < 3.5) { // 2% chance
                        earned = 10;
                        message = "🥈 You found Silver! +10 coins!";
                    } else if (find < 8.5) { // 5% chance
                        earned = 5;
                        message = "🪙 You found Bronze! +5 coins!";
                    }

                    updateBalance(earned, message);
                });

                // Set initial values in UI
                balanceElement.innerText = balance;
                debtElement.innerText = debt.toFixed(2);
                updateStockDisplay();

                document.getElementById("spin-button").addEventListener("click", function() {
                    let betAmount = parseInt(document.getElementById("bet-amount").value);

                    if (balance <= 0) {
                        document.getElementById("result-message").innerText = "❌ Out of Coins!";
                        return;
                    }

                    if (betAmount > balance) {
                        document.getElementById("result-message").innerText = "❌ Not Enough Coins!";
                        return;
                    }

                    balance -= betAmount;
                    balanceElement.innerText = balance;
                    updateButtons();
                    updateStorage();

                    // Randomly select symbols
                    let reels = ["🍒", "🍋", "🍉", "⭐", "🍇", "🔔", "💰"];
                    let reel1 = reels[Math.floor(Math.random() * reels.length)];
                    let reel2 = reels[Math.floor(Math.random() * reels.length)];
                    let reel3 = reels[Math.floor(Math.random() * reels.length)];

                    document.getElementById("reel1").innerText = reel1;
                    document.getElementById("reel2").innerText = reel2;
                    document.getElementById("reel3").innerText = reel3;

                    // Check for win condition
                    if (reel1 === reel2 && reel2 === reel3) {
                        let winAmount = betAmount * 2;
                        balance += winAmount;
                        document.getElementById("result-message").innerText = `🎉 WIN! +${winAmount} Coins!`;
                    } else {
                        document.getElementById("result-message").innerText = "❌ Try Again!";
                    }

                    balanceElement.innerText = balance;
                    updateButtons();
                    updateStorage();
                });

                // **Ensure users cannot enter more than 250 in the input field**
                addCoinsInput.addEventListener("input", function() {
                    let value = parseInt(addCoinsInput.value);
                    if (value > 250) {
                        addCoinsInput.value = 250; // Force max limit
                    } else if (value < 10) {
                        addCoinsInput.value = 10; // Ensure minimum of 10
                    }
                });

                // Add Coins Button
                addCoinsButton.addEventListener("click", function() {
                    let addAmount = parseInt(addCoinsInput.value);

                    if (addAmount > 250) {
                        addAmount = 250; // Ensure max limit
                    }

                    balance += addAmount;
                    debt += addAmount;
                    borrowCount++;

                    balanceElement.innerText = balance;
                    debtElement.innerText = debt.toFixed(2);
                    updateButtons();
                    updateStorage();

                    document.getElementById("result-message").innerText = `✅ Borrowed ${addAmount} Coins!`;

                    if (borrowCount >= 750) {
                        alert("🛑 Uh-oh, you're in deep debt! Maybe take a break?");
                    }
                });

                // Repay Debt Button
                repayDebtButton.addEventListener("click", function() {
                    let repayAmount = parseInt(document.getElementById("repay-amount").value);

                    if (repayAmount > balance) {
                        document.getElementById("result-message").innerText = "❌ Not Enough Coins to Repay!";
                        return;
                    }

                    balance -= repayAmount;
                    debt -= repayAmount;

                    if (debt < 0) {
                        debt = 0;
                    }

                    balanceElement.innerText = balance;
                    debtElement.innerText = debt.toFixed(2);
                    updateButtons();
                    updateStorage();
                });

                // Interest on debt every 5 seconds
                setInterval(() => {
                    if (debt > 0) {
                        let interest = Math.ceil(debt * 0.01 * 100) / 100;
                        debt += interest;
                        debtElement.innerText = debt.toFixed(2);
                        updateStorage();
                    }

                    if (debt >= 1000) {
                        alert("💸 You're BANKRUPT! Resetting balance and debt...");
                        balance = 100;
                        debt = 0;
                        balanceElement.innerText = balance;
                        debtElement.innerText = debt.toFixed(2);
                        updateStorage();
                    }
                    updateButtons();
                }, 5000);
            });
        </script>
    </div>
</main>

<?php include '../inc/footer.php'; ?>