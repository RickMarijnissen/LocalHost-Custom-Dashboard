<?php include '../inc/header.php'; ?>

<main class="container-flex">
    <!-- Left Section - Quick Links & Search Bar -->
    <div class="left-section">
        <div class="quick-links">
            <a class="link" href="http://localhost/">Home</a>
            <a class="link" href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a>
            <a class="link" href="inc/open_folder.php">Open Projects Folder</a>
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
                <p>If you run out of coins, <br> add more:</p>
                <input type="number" id="add-coins" min="10" max="500" value="50">
                <button id="add-coins-button" disabled>Add Coins</button> <!-- Button starts locked -->
                <br><br>
                <p>Repay Debt:</p>
                <input type="number" id="repay-amount" min="1" value="10">
                <button id="repay-debt-button" disabled>Repay Debt</button> <br><br>
                <p>Debt: <span id="debt">0</span> Coins</p>
            </div>
        </main>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let balance = localStorage.getItem("balance") ? parseInt(localStorage.getItem("balance")) : 100;
                let debt = localStorage.getItem("debt") ? parseFloat(localStorage.getItem("debt")) : 0;
                let borrowCount = localStorage.getItem("borrowCount") ? parseInt(localStorage.getItem("borrowCount")) : 0;

                const balanceElement = document.getElementById("balance");
                const debtElement = document.getElementById("debt");
                const addCoinsButton = document.getElementById("add-coins-button");
                const addCoinsInput = document.getElementById("add-coins");
                const repayDebtButton = document.getElementById("repay-debt-button");

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

                function updateStorage() {
                    localStorage.setItem("balance", balance);
                    localStorage.setItem("debt", debt.toFixed(2));
                    localStorage.setItem("borrowCount", borrowCount);
                }

                // Set initial values in UI
                balanceElement.innerText = balance;
                debtElement.innerText = debt.toFixed(2);

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

                    if (borrowCount >= 5) {
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