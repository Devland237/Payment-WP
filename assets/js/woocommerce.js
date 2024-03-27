// const successCallback = function( data ) {



// 	const checkoutForm = $( 'form.woocommerce-checkout' )



// 	// add a token to our hidden input field

// 	// console.log(data) to find the token

// 	checkoutForm.find( '#misha_token' ).val( data.token )



// 	// deactivate the tokenRequest function event

// 	checkoutForm.off( 'checkout_place_order', tokenRequest )



// 	// submit the form now

// 	checkoutForm.submit()



// }



// const errorCallback = function( data ) {

//     console.log( data )

// }



// const tokenRequest = function() {



// 	// here will be a payment gateway function that process all the card data from your form,

// 	// maybe it will need your Publishable API key which is misha_params.publishableKey

// 	// and fires successCallback() on success and errorCallback on failure

// 	return false

		

// }



// jQuery( function( $ ){



// 	const checkoutForm = $( 'form.woocommerce-checkout' )

// 	checkoutForm.on( 'checkout_place_order', tokenRequest )



// })



// console.log("bonjour");



// let label = document.querySelector("for=viazipay-cc");





function description_method(id) {



	 let descrip = document.querySelector("#"+id);

	 let all =document.querySelectorAll(".viazipay_method_woocommerce");

	//  descrip.style.display = "block";



	// console.log(descrip);

	all.forEach((value)=>{

	for(let i=0; i<3; i++)



	{



		// let aa = document.querySelector("#viazipay_method_"+i);



			if(value.id !== id){

			descrip.style.display = "block";

		}

		else{

			descrip.style.display = "block";

		}

		// console.log(aa);

	}

	})





	// let all =document.querySelectorAll(".viazipay_method_woocommerce");



	// all.forEach((value)=>{

		

	// 	if(value.id==id){

	// 		descrip.style.display = "block";

	// 	}

	// 	else{

	// 		descrip.style.display = "none";

	// 	}

	// 	console.log(value.id);



	// })

}







//  function description_method1() {

// 	for(let i=0; i<3; i++)



// 	{



// 		let descrip = document.querySelector("#viazipay_method_"+i);

// 		console.log(descrip);



// 	}



// 	// descrip.forEach((value)=>{

// 	// 	value.style.display = "block";

// 	// })



	

// 	console.log("cc");

// }



