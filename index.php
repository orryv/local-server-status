<html>
    <head>
        <title>Local Server Status</title>
        <style>
            *{
                font-family: Arial, Helvetica, sans-serif;
            }
            body{
                margin: 0;
                padding: 0;
                background-color: #363636;
            }

            /* content, position horizontally */
            content{
                display: flex;
                flex-direction: row;
                justify-content: center;
                height: auto;
                
            }

            #content{
                background-color: #bababa;
                border-radius: 10px;
                padding: 10px;
                margin-top: 20px;
                margin-bottom: 20px;
            }

            .serverItem{
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 10px;
                border: 1px solid #aeaeae;
                margin: 10px;
                border-radius: 10px;
                background-color: #363636;
                color:white;
                text-decoration: none;
            }

            .serverItem:hover{
                background-color: #505050;
                border: 1px solid #c0c0c0
            }

            .serverItem>.success{
                border-radius: 10px;
                background-color: #00ff00;
                color:black;
                width: 10px;
                height: 10px;
                display:inline-block;
                margin-right: 6px;
            }

            .serverItem>.warning{
                border-radius: 10px;
                background-color: #ffff00;
                color:black;
                width: 10px;
                height: 10px;
                display:inline-block;
                margin-right: 6px;
            }

            .serverItem>.danger{
                border-radius: 10px;
                background-color: #ff0000;
                color:black;
                width: 10px;
                height: 10px;
                display:inline-block;
                margin-right: 6px;
            }

            .serverItem>.statusCode{
                color: #aeaeae;
                width: 40px;
                display:inline-block;
                font-size: 12px;
            }

            .serverItem>.name{
                color:white;
                text-decoration: none;
            }

            .serverItem>.url{
                color: #aeaeae;
                font-size: 12px;
                margin-left: 10px;
            }

            .serverItem>.responseTime{
                color: #aeaeae;
                font-size: 12px;
                float:right;
            }

            .serverItem>.statusMessage{
                color: #aeaeae;
                margin-top: 10px;
                margin-left: 56px;
                font-size: 12px;
            }
        </style>


    </head>
    <body>

        <content>
            <div id="content">
                <div id="serverContainer">
                    
                </div>
            </div>
        </content>



        <a id="serverItemDummy" style="display: none;">
        
        </a>


        
        

    <!-- jquery -->
    <script src="static/js/jquery-3.7.1.min.js"></script>

    <script>
            function updateServerStatus(json) {
                let serverContainer = $('#serverContainer');
                let serverItemDummy = $('#serverItemDummy');

                serverContainer.empty();

                for (let key in json) {
                    console.log(json[key]);

                    let serverItem = serverItemDummy.clone();
                    serverItem.attr('id', json[key].name);
                    serverItem.css('display', 'block');
                    serverItem.attr('href', (json[key].ssl ? 'https://' : 'http://') + json[key].host + ':' + json[key].port);
                    serverItem.attr('target', '_blank');

                    // add status as class to server item
                    serverItem.addClass(json[key].status);

                    // add server status as span with class to server item
                    let statusSpan = $('<span></span>');
                    statusSpan.addClass(json[key].status);
                    // statusSpan.text(json[key].status);
                    statusSpan.appendTo(serverItem);

                    // add statuscode
                    let statusCodeSpan = $('<span></span>');
                    statusCodeSpan.addClass('statusCode');
                    statusCodeSpan.text(json[key].statusCode);
                    statusCodeSpan.appendTo(serverItem);

                    // add name as span with class to server item
                    let nameSpan = $('<span></span>');
                    nameSpan.addClass('name');
                    nameSpan.text(json[key].name);
                    nameSpan.appendTo(serverItem);

                    // add url as span with class to server item
                    let urlSpan = $('<span></span>');
                    urlSpan.addClass('url');
                    urlSpan.text((json[key].ssl ? 'https://' : 'http://') + json[key].host + ':' + json[key].port);
                    urlSpan.appendTo(serverItem);


                    // add response time as span with class to server item
                    let responseTimeSpan = $('<div></div>');
                    responseTimeSpan.addClass('responseTime');
                    responseTimeSpan.text(json[key].responseTime+'s');
                    responseTimeSpan.appendTo(serverItem);

                    // add error message as span with class to server item
                    if(json[key].statusMessage != ''){
                        let statusMessageSpan = $('<div></div>');
                        statusMessageSpan.addClass('statusMessage');
                        statusMessageSpan.text(json[key].statusMessage);
                        statusMessageSpan.appendTo(serverItem);
                    }

                    // add server item to container
                    serverItem.appendTo(serverContainer);

                    // add class to server item
                    serverItem.addClass('serverItem');

                }
            }

            function getData()
            {
                $.ajax({
                    url: "get-server-status.php",
                    success: function (data) {
                        let json = JSON.parse(data);
                        console.log(json);
                        updateServerStatus(json);
                    },
                    error: function (data) {
                        
                    }
                });
            }

            getData();

            setInterval(() => {
                getData();
            }, 5000);

    </script>


</html>