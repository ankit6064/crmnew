<script>
    // Dropdown toggle
    document.querySelectorAll('.dropdown .menu-title').forEach(item => {
        item.addEventListener('click', () => {
            item.parentElement.classList.toggle('active');
        });
    });
</script>

<script>
	 document.getElementById("profileToggle").addEventListener("click", function() {
  const menu = document.getElementById("profileMenu");
  menu.style.display = menu.style.display === "block" ? "none" : "block";
});

// Close menu if clicked outside
window.addEventListener("click", function(e) {
  if (!document.getElementById("profileToggle").contains(e.target)) {
    document.getElementById("profileMenu").style.display = "none";
  }
});
	 </script>

     
	 
	 
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script> -->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
    
