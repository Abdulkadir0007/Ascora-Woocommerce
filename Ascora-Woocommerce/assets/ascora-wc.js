jQuery(document).ready(function($){
    // Toggle slide-out
    $('#mini-cart-trigger').on('click', function(e){
        e.preventDefault();
        $('#mini-cart-content').toggleClass('open');
    });

    // Close when clicking outside
    $(document).on('click', function(e){
        if(!$(e.target).closest('.custom-mini-cart').length){
            $('#mini-cart-content').removeClass('open');
        }
    });
});
