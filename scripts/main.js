$(function(){
    new Typed("#typing",{
        strings: ["драйв", "эмоции", "друзья", "призы"],
        typeSpeed: 80,
        backDelay: 2000,
        backSpeed: 80,
        startDelay: 0,
        loop: true,
        loopCount: Infinity,
        contentType: 'html',      
    });

    $(".review_next").click(function() {
        var currentId = $('#dataReviews').data('current');
        var maxId = $('#dataReviews').data('max');
        var newId = currentId + 1;
        if (newId >= maxId) { newId = 0; }
        $('.review-' + currentId).addClass('invisible');
        $('.review-' + newId).removeClass('invisible');
        $('#dataReviews').data('current', newId);
    });
    
    $(".review_prev").click(function() {
        var currentId = $('#dataReviews').data('current');
        var maxId = $('#dataReviews').data('max');
        var newId = currentId - 1; 
        if (newId < 0) { newId = maxId-1; }
        $('.review-'+currentId).addClass('invisible');
        $('.review-'+newId).removeClass('invisible');
        $('#dataReviews').data('current', newId);
    });

});