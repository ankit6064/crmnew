<script>
    // Dropdown toggle
    document.querySelectorAll('.dropdown .menu-title').forEach(item => {
        item.addEventListener('click', () => {
            item.parentElement.classList.toggle('active');
        });
    });
</script>

<script>
document.getElementById("profileToggle").addEventListener("click", function(e) {
  e.stopPropagation(); // prevent immediate window click close

  const menu = document.getElementById("profileMenu");
  const arrow = this.querySelector(".dropdown-arrow i");

  const isOpen = menu.style.display === "block";
  
  // Toggle menu
  menu.style.display = isOpen ? "none" : "block";

  // Toggle arrow
  if (isOpen) {
    arrow.classList.remove("fa-chevron-down");
    arrow.classList.add("fa-chevron-right");
  } else {
    arrow.classList.remove("fa-chevron-right");
    arrow.classList.add("fa-chevron-down");
  }
});

// Close menu if clicked outside
window.addEventListener("click", function(e) {
  const toggle = document.getElementById("profileToggle");
  const menu = document.getElementById("profileMenu");
  const arrow = toggle.querySelector(".dropdown-arrow i");

  if (!toggle.contains(e.target)) {
    menu.style.display = "none";
    // Reset arrow
    arrow.classList.remove("fa-chevron-down");
    arrow.classList.add("fa-chevron-right");
  }
});
</script>


     
	 
	 
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script> -->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script> -->
    
