<footer>
    <div class="container">
        <p>© <?php echo date("Y"); ?> Error404Absent's Projects Dashboard</p>
    </div>
</footer>

<script>
    // Search & Filter Functionality
    function filterProjects() {
        let input = document.getElementById("search-bar").value.toLowerCase();
        let cards = document.querySelectorAll(".card");

        cards.forEach(card => {
            let projectName = card.querySelector("a").innerText.toLowerCase();
            card.style.display = projectName.includes(input) ? "block" : "none";
        });
    }
</script>

</body>

</html>