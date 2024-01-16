
<li class="nav-item dropdown">
<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-person"></i><span> {{username}} </span>
</a>
<ul class="dropdown-menu border-0 shadow-lg" aria-labelledby="navbarDropdown">
<li><a class="dropdown-item" href="#">ログイン</a></li> 
{% if isUserLogin %}
<li><a class="dropdown-item" href="./login.php?logout=1">logout</a></li>
{% else %}
<li><a class="dropdown-item" href="./login.php">login</a></li>
{% endif %}
</ul> 

<script>
$(function(){
    var entry_url = $("#entry_url").val();

    $("#cart_in").click(function(){
        var item_id = $("#item_id").val();
      
    });
});

</script>

<script>

function addToCart(item_id){
   $(document).ready(function() {
            // Show the "Item added to Cart" alert
            $("#cart-alert").fadeIn().delay(2000).fadeOut();

            // Delay the redirection to let the user see the alert
            $.get("cart.php?item_id=" + item_id, function(data){
                alert("Item has been added");
                location.reload()
            });
  
    });
}
 
</script>

<script>
    setTimeout(() => {
        document.getElementById('loader').style.display= "none"
    },1000)
</script>