const mercadoPagoPublicKey = document.getElementById("mercado-pago-public-key").value;
const mercadopago = new MercadoPago(mercadoPagoPublicKey);
const MainButton = window.Telegram.WebApp.MainButton;
let cardPaymentBrickController;

// MainButton.setText('Submit');

function show_detail(result){
	if (result.status == 400)
	{
		document.getElementById("payment-id").innerText = "-";
		document.getElementById("payment-status").innerText = result.status;
		document.getElementById("payment-result").innerText = result.message; //JSON.stringify(result);
	} else {
		document.getElementById("payment-id").innerText = result.id;
		document.getElementById("payment-status").innerText = result.status;
		document.getElementById("payment-result").innerText = result.description
	}
}

async function loadPaymentForm() {
    const productCost = document.getElementById('amount').value;
	const productDescription = document.getElementById('description').value;
    const settings = {
        initialization: {
            amount: productCost,
        },
        callbacks: {
            onReady: () => {
                console.log('brick ready')
            },
            onError: (error) => {
                alert(JSON.stringify(error))
            },
            onSubmit: (cardFormData) => {
				cardFormData['description']=productDescription;
                proccessPayment(cardFormData)
            }
        },
        locale: 'pt',
        customization: {
            paymentMethods: {
                maxInstallments: 5
            },
            visual: {
                style: {
                    theme: 'dark',
                    customVariables: {
                        formBackgroundColor: '#1d2431',
                        baseColor: 'aquamarine'
                    }
                }
            }
        },
    }

    const bricks = mercadopago.bricks();
    cardPaymentBrickController = await bricks.create('cardPayment', 'mercadopago-bricks-contaner__PaymentCard', settings);
};

const proccessPayment = (cardFormData) => {
	// console.log(JSON.stringify(cardFormData))
    fetch("/process_payment", {
        method: "POST",
        headers: {
			'Accept': 'application/json',
            "Content-Type": "application/json",
        },
        body: JSON.stringify(cardFormData),
    })
    .then(response => {
        return response.json();
    })
    .then(result => {
		console.log(result)
		show_detail(result);
		$('.container__payment').fadeOut(500);
		setTimeout(() => { $('.container__result').show(500).fadeIn(); }, 500);
		// window.location.origin;
		retorno = "error:unknow";
		if (result.status == 400){
			retorno = "error:"+result.message;
		} else {
			retorno = "payment_id:"+result.id+"-"+time;
		}
		console.log(retorno);
		MainButton.show();
		MainButton.onClick(() => {
			try{
				//window.Telegram.WebApp.sendData(`${retorno}`);//(JSON.stringify(result));
				fetch(`/returnResult.php?query=${window.Telegram.WebApp.initDataUnsafe.query_id}&dados=${retorno}`)
						// .then(response=>response.text())
						// .then(data=>{ console.log(data); })
			} catch (e) {
				console.log(e.message);
			}
		});
		
		setTimeout(function() {
			try{
				//window.Telegram.WebApp.sendData(`${retorno}`);//(JSON.stringify(result));
				fetch(`/returnResult.php?query=${window.Telegram.WebApp.initDataUnsafe.query_id}&dados=${retorno}`);
			} catch (e) {
				console.log(e.message);
			}
		}, 3000);
		
		// try{
			// window.Telegram.WebApp.close();
		// } catch (e) {
			// console.log(e.message);
		// }
    })
    .catch(error => {
		console.log(error);
		$('.container__payment').fadeOut(500);
        alert("Unexpected error\n"+JSON.stringify(error)+"\n"+error);
    });
}

loadPaymentForm();