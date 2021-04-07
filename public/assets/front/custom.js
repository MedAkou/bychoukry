
    
  
    $(".lx-contact-us > a").on("click",function(){
	if($(".lx-contact-us-content").css("display") !== "block"){
		$(".lx-contact-us-content").css("display","block");
		$(this).find("i").attr("class","fa fa-times");
	}
	else{
		$(".lx-contact-us-content").css("display","none");
		$(this).find("i").attr("class","fa fa-phone");
	}
});
    
    
    
    
    
    
    
    $('body #menuclose').click(function(){
      $('.side-navigation').hide();
    });
    
    $('body #menuopen').click(function(){
      $('.side-navigation').show();
    });
    
    


	var later = $('#laterDate').val();
	
	$(function () {
           setInterval(function () {
                var currentDate = new Date().getTime();
                var eventDate = new Date(later).getTime();
                var dateDifference = eventDate - currentDate;
                var seconds = parseInt(dateDifference / 1000);
                var minutes = parseInt(seconds / 60);
                var hours = parseInt(minutes / 60);
                var days = parseInt(hours / 24);
                var months = parseInt(days / 30);
                seconds = seconds - minutes * 60;
                minutes = minutes - hours * 60;
                hours = hours - days * 24;
                days = days - months * 30;
             
                $('#seconds').html(seconds);
                $('#minutes').html(minutes);
                $('#hours').html(hours);
                $('#days').html(days);
                
            })
    }, 1000)
        





function validatePhone(txtPhone) {
    var a = txtPhone;
    var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
    if (filter.test(a)) {
        return true;
    }
    else {
        return false;
    }
}






$('#savecommand').submit(function(e) {


  e.preventDefault();
  e.stopPropagation();



var tel = $('#savecommand [name="phone"]').val();
  
if( tel.length > 10  || tel.length < 9 ){
     $('#phoneAlert').show();
     return false;  
}

if( ! validatePhone (tel))  {
     $('#phoneAlert').show();
     return false;  
}




$('#phoneAlert').hide();

  var datastring = $(this).serialize();

  jQuery.ajax({
    url: '/storeApi',
    type : 'post',
    data: datastring,
    dataType: "html",
    success: function( response ) {
      window.location.href = "/thank-you";
    }
  });

  return false;
});



$('.quantity_area #type').change(function(){
        
    var price = $('.quantity_area #type').val();
    var quantity = $('.quantity_area #type option:selected').attr('data-quantity');
    
   // alert(price);
   // alert(quantity);
    
    
    $('#savecommand [name="price"]').val(price);
    $('#savecommand [name="quantity"]').val(quantity);
});






$('body #close_cmd').click(function(){
    $('.cmd_wrapper').hide();
});



$('.single-submit').click(function(){
  
  var products = $(this).attr('data-products');
  var colors = $(this).attr('data-colors');
  var sizes = $(this).attr('data-sizes');
  
  
  if(colors == 'on'){
    $('body #product_color').show();
  }else {
    $('body #product_color').hide();  
  }
  
   
  if(sizes == 'on'){
    $('#product_size').show();
  }else {
    $('#product_size').hide();  
  }
  
  
  
  $('#savecommand [name="idproducts"]').val(products);
  
  $('.cmd_wrapper').show();
  
});

$("#add-to-cart").click(function(){
  const product = $(this).attr('data-product');
  const color = $(this).attr('data-color');
  const size = $(this).attr('data-size');
  const price = $(this).attr('data-price');
  const products_quantity = $("#products_quantity");

  const formData = new FormData();
  formData.append('product', product);
  formData.append('color', color);
  formData.append('size', size);
  formData.append('price', price);

  $.ajax({
    url: '/addToCart',
    type : 'post',
    processData: false, // important
    contentType: false, // important
    data: formData,
    dataType: "JSON",
    success: function( response ) {
      products_quantity.html(response.products_quantity);
    }
  });

});


$(".remove-from-cart").click(function(){
  const products_quantity = $("#products_quantity");
  const product = $(this).attr('data-product');

  const formData = new FormData();
  formData.append('product', product);

  $.ajax({
    url: '/removeFromCart',
    type : 'post',
    processData: false, // important
    contentType: false, // important
    data: formData,
    dataType: "JSON",
    success: function( response ) {
      products_quantity.html(response);
      $(`#row-${product}`).remove();
    }
  });
})




window.setInterval(function(){
          $(".single-visitors b").text(parseInt($(".single-visitors b").text()) + Math.floor((Math.random() * 5) + 1));
},3000);

  $('.thumbnail-item').click(function(){
    var img = $(this).find('img').attr('src');
    $('.product-slider.product-slider-desktop .preview img').attr('src',img);
  });




$('.quantity-handler i.fa.fa-plus').click(function(){
  var qty = parseInt($('.single-quantity').val());
    qty+=1;
    $('.single-quantity').val(qty);



  $('#savecommand [name="quantity"]').val(qty);




});

$('.quantity-handler i.fa.fa-minus').click(function(){
  var qty = parseInt($('.single-quantity').val());
  if(qty >1){
    qty-=1;
    $('.single-quantity').val(qty);
    $('#savecommand [name="quantity"]').val(qty);
  }  
});

$('#search-icon').click(function(){
  $('.search-form').toggle();
});




 var owl = $('.list-preview');
      owl.owlCarousel({
        margin: 10,
        loop: true,
        rtl: true,
           nav:true,
      navText : ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>'],
        
        responsive: {
          0: {
            items: 3
          },
          600: {
            items: 2
          },
          1000: {
            items: 5
          }
        }
      });






 var owl = $('.categories-style-1');
      owl.owlCarousel({
        margin: 10,
        rtl: true,
              nav:true,
      navText : ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>'],
        
        responsive: {
          0: {
            items: 1
          },
          600: {
            items: 2
          },
          1000: {
            items: 4
          }
        }
      })








$('.thumbnail-item img').click(function(){
  var src = $(this).attr('src');
  $('.thumbnail .preview img').attr('src',src);
});



$('body .owl-carousel2').owlCarousel( {
    loop: true,
    center: true,
    items: 1,
    margin: 30,
    autoplay: true,
    dots:false,
    nav:false,
    autoplayTimeout: 8500,
    smartSpeed: 450,
    responsive: {
      0: {
        items: 1
      },
      768: {
        items: 2
      },
      1170: {
        items: 1
      }
    }
  });