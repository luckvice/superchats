/*

    [Implemetação de CHAT em JS conectando com WebSockets]

*/

jQuery(function($){

    var websocket_server = new WebSocket("ws://192.168.4.88:8001/");
    websocket_server.onopen = function(e) {
        websocket_server.send(
            JSON.stringify({
                'type':'socket',
                'user_id':'Input hidden da sessao'
            })	
        );
        

            websocket_server.send(
                JSON.stringify({
                    'type':'chat',
                    'user_id':$('#nickname').val(),
                    'chat_msg':'Entrou no chat'
                })
            );

    }
    websocket_server.onerror = function(e) {
        // Errorhandle
        window.alert("///**Erro de websocket**///");
    }

    websocket_server.onmessage = function(e)
    {
        var json = JSON.parse(e.data);
        switch(json.type) {
            case 'chat':
                $('#saida_chat').append(json.msg);
                break;
        }
    }



    $('#btn-enviar').click(function() {
    enviaChat($('#entrada_chat').val());
    });

    $("#entrada_chat").emojioneArea();

    $('#entrada_chat').on('keyup',function(e){
        if(e.keyCode==13 )
        {
             enviaChat($(this).val());
        }
    });

    function enviaChat(chat_msg){
        console.log('preciosou tecla');
        
        var chat_msg = chat_msg;
        var nickname = $('#nickname').val();

        console.log('Debug: MENSAGEM['+chat_msg+"] De usuario: ["+nickname+"]");
        websocket_server.send(
            JSON.stringify({
            'type':'chat',
            'user_id': nickname,//input hidden
            'chat_msg':chat_msg

            })
        );
        $('#entrada_chat').val('');
    }
});