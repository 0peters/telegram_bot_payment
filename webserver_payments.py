import json
import requests
from flask import Flask, render_template, request, jsonify, Response
import os
import mercadopago
from pagseguro import PagSeguro

app = Flask(__name__)
sdk = mercadopago.SDK(os.getenv('MP_ACCESS_TOKEN'))
config = {'sandbox': True} #Caso for usar em produção remover esta opção
pg = PagSeguro(email=os.getenv('PS_EMAIL'), token=os.getenv('PS_TOKEN'), config=config)
TOKEN = os.getenv('BOTTOKEN')

def send_for_bot(id_webapp, texto_to_send):
    res = {
        'type': 'article',
        'id': 1,
        'title': 'Result',
        'input_message_content': {
            'message_text': texto_to_send
        },
        'description': 'WebApp result'
    }
    
    base_url = f'https://api.telegram.org/bot{TOKEN}' \
                f'/answerWebAppQuery?web_app_query_id={id_webapp}&result={json.dumps(res)}'
    req = requests.get(base_url)
    response = Response(req.json())
    return response,200

def get_transation_status(transactionCode):
    transactionCode = transactionCode.replace("-","")
    # URL_STAUTS=f"https://api.pagseguro.com/digital-payments/v1/transactions/{transactionCode}/status"
    URL_STAUTS=f"https://sandbox.api.pagseguro.com/digital-payments/v1/transactions/{transactionCode}/status"
    response = requests.get(URL_STAUTS)
    return response.text

@app.route('/process_payment', methods=['POST'])
def process_payment():
    # print(request.get_data())
    request_values = request.get_json()
    # print(request_values)
    
    payment_data = {
        "transaction_amount": float(request_values["transaction_amount"]),
        "token": request_values["token"],
        "description":request_values["description"],
        "installments": int(request_values["installments"]),
        "payment_method_id": request_values["payment_method_id"],
        "issuer_id": request_values["issuer_id"],
        "payer": {
            "email": request_values["payer"]["email"],
            "identification": {
                "type": request_values["payer"]["identification"]["type"], 
                "number": request_values["payer"]["identification"]["number"]
            }
        }
    }
    
    # print(payment_data)

    payment_response = sdk.payment().create(payment_data)
    payment = payment_response["response"]
    
    # print(json.dumps(payment, indent = 4))

    print("status =>", payment["status"])
    print("status_detail =>", payment.get("status_detail"))
    print("id=>", payment.get("id"))
    print("description=>", payment.get("description"))
    return jsonify(payment), 200

@app.get('/returnResult')
def result():
    args = request.args['query']
    texto = request.args['dados']
    print(args, texto)
    return send_for_bot(args, texto)
   
@app.route('/notify', methods=['POST'])
def notification_view():
    print(request.get_data())
    notification_code = request.form.get('notificationCode')
    notification_data = pg.check_notification(notification_code)
    print(notification_data.code, notification_data.status) #status 3 = paga
    print(get_transation_status(notification_data.code))
    #utilizar estes dados para fazer a integração com o bot localmente.
    return 'ok',200

@app.route('/')
def home():
   return render_template('index.html', public_key=os.getenv('MP_PUBLIC_KEY'))
   
@app.errorhandler(404)
def page_not_found(error):
    return render_template('404.html', title = '404'), 404
   
if __name__ == '__main__':
   # app.run()
   app.run(host="0.0.0.0", port="8082")