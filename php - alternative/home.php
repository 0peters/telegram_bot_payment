<?php
$public_key="MercadoPago PublicKey";
$price = (isset($_REQUEST['price'])) ? $_REQUEST['price']:'15';
$meses = (isset($_REQUEST['meses'])) ? $_REQUEST['meses']:'1';
?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="shortcut icon" href="static/img/favicon.ico" />
    <title>Telegram bot: ...</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link
      rel="stylesheet"
      type="text/css"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
    />
    <link rel="stylesheet" type="text/css" href="static/css/index.css" />
	<script src="https://telegram.org/js/telegram-web-app.js"></script>
  </head>
  <body>
    <input
      id="mercado-pago-public-key"
      value="<?=$public_key?>"
      type="hidden"
    />
    <main>
      <!-- Payment -->
      <section class="payment-form dark">
        <div class="container__payment">
          <div class="block-heading">
            <h2>Pagamento com cartão</h2>
            <p>
              Telegram bot: ...
			  <br>
			  <a href="https://t.me/..._bot" target="_blank">@..._bot</a>
            </p>
          </div>
          <div class="form-payment">
            <div class="products">
				<div class="item">
                  User:<span class="price" id="summary-user">loading user...</span>
                </div>
                <div class="item">
                  Valor:<span class="price" id="summary-price">R$ <?=$price?>,00</span>
                </div>
                <div class="total">
                  Tempo:<span class="price" id="summary-total"><?=$meses?> <?php if ($meses === "1") echo "mês"; else echo "meses";?></span>
                </div>
                <input type="hidden" id="amount" value="<?=$price?>"/>
                <input type="hidden" id="description" value="Bot Premium ..."/>
            </div>
            <!-- TODO: Add payment form here -->
            <div id="mercadopago-bricks-contaner__PaymentCard"></div>
          </div>
        </div>
      </section>
      <!-- Result -->
      <section class="shopping-cart dark">
        <div class="container container__result">
          <div class="block-heading">
            <h2>Resumo da compra</h2>
          </div>
          <div class="content">
            <div class="row">
              <div class="col-md-12 col-lg-12">
                <div class="items product info product-details">
                  <div class="row justify-content-md-center">
                    <div class="col-md-4 product-detail">
                      <div class="product-info">
                        <br />
                        <p><b>ID: </b><span id="payment-id"></span></p>
                        <p><b>Status: </b><span id="payment-status"></span></p>
						<p><b>Description: </b><span id="payment-result"></span></p>
                        <br />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
    <footer>
      <div class="footer_logo">
        <img id="horizontal_logo" src="static/img/horizontal_logo.png" />
      </div>
      <!-- <div class="footer_text"> -->
        <!-- <p>Text footer:</p> -->
        <!-- <p> -->
          <!-- <a href="#" -->
            <!-- >footer link</a> -->
        <!-- </p> -->
      <!-- </div> -->
    </footer>
	<p><span id="teste"></span></p>
  </body>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://sdk.mercadopago.com/js/v2"></script>
  <script type="text/javascript">
		window.Telegram.WebApp.ready();
		// const test = document.getElementById("teste");
		// test.innerText = window.Telegram.WebApp.initDataUnsafe.query_id //`${JSON.stringify(window.Telegram.WebApp)}`;
		
		try {
			document.querySelector("#summary-user").innerHTML = window.Telegram.WebApp.initDataUnsafe.user.first_name;
			try {
			  document.querySelector("#summary-user").innerHTML += window.Telegram.WebApp.initDataUnsafe.user.last_name;
			  console.log("load data: ok");
			} catch (e) {
			  console.log(e.message);
			}
			document.querySelector("#summary-user").innerHTML += " id:" + window.Telegram.WebApp.initDataUnsafe.user.id;
			console.log("load data: ok");
		} catch (e) {
			console.log(e.message);
		}
		const time = <?=$meses?>;
	</script>
	<script type="text/javascript" src="static/js/index.js"></script>
</html>
