from telebot import types
import telebot
import json
from pagseguro import PagSeguro
import os

config = {'sandbox': True} #Caso for usar em produção remover esta opção
pg = PagSeguro(email=os.getenv('PS_EMAIL'), token=os.getenv('PS_TOKEN'), config=config)
bot = telebot.TeleBot(os.getenv('BOTTOKEN'))
WEBAPP_BASEURL="https://yourwebapp.domain:8082" #servidor HTTPS para processar requisições do WebApps
URL_SERVER="https://publicaddresss.server:8082" #acesso ao servidor para atualizações de pagseguro (normalmente o mesmo do acima mas este não precisa ser https)

def webAppKeyboardInline(value=15, time=1, url=""):
    keyboard = types.InlineKeyboardMarkup(row_width=1)
    if url != "":
        keyboard.add(types.InlineKeyboardButton(text="Pagamento com pagseguro", url=url))
    else:
        webApp = types.WebAppInfo(url=f'{WEBAPP_BASEURL}?price={value}&meses={time}')
        keyboard.add(types.InlineKeyboardButton(text="Pagamento com cartão", web_app=webApp))
        keyboard.add(types.InlineKeyboardButton('PasSeguro', callback_data='pagamento'))
    return keyboard

@bot.message_handler(commands=['start'])
def start(message):
   bot.send_message( message.chat.id, 'teste de webApps', parse_mode="Markdown", reply_markup=webAppKeyboardInline(url=""))
   
@bot.callback_query_handler(func=lambda call: True)
def iq_callback(query):
    if query.data == "pagamento":
        message = query.message
        bot.edit_message_text("Aguarde...",
            message.chat.id,
            message.id)
        print(query.id, query.data)
        pg.sender = {
            # "name": "Nome Cliente",
            # "area_code": 11,
            # "phone": 987654321,
            "email": "emaildocliente@gmail.com",
        }
        pg.shipping = None
        pg.reference_prefix = None
        pg.extra_amount = '0.00'
        # pg.add_item(id="0001", description="Bot Premium", amount='15.00', quantity=1)
        pg.items = [{"id": "0001", "description": "Bot Premium", "amount":"15.00", "quantity": 1}]
        pg.redirect_url = f'{URL_SERVER}/ok'
        pg.notification_url = f'{URL_SERVER}/notify'
        response = pg.checkout()
        print(response.code, response.payment_url, response.errors)
        if response.payment_url:
            bot.edit_message_text("Acesse o link e realize o pagamento:",
                            message.chat.id,
                            message.id, reply_markup=webAppKeyboardInline(url=response.payment_url)) 
        else:
            bot.edit_message_text(f"Houve um erro:{response.errors}",
                            message.chat.id,
                            message.id, reply_markup=webAppKeyboardInline(url=""))

@bot.message_handler(content_types="web_app_data")
def answer(message):
   print(message.web_app_data.data)
   bot.send_message(message.chat.id, f"Mensagem sendData: {message.web_app_data.data}") 

@bot.message_handler(func=lambda call: True)
def new_mes(message):
    print(message.text)

if __name__ == '__main__':
   bot.polling()