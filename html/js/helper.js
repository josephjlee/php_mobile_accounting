/**
* Funktion zum Absetzen eines GET-Requests
* @param controller = Bezeichnung des Controllers als String (wie in URL)
* @param action = Bezeichnung der Action als String
* @param parameters = Parameter als assoziatives Array Key=>Value
* @param successHandler = Funktions-Handle für Erfolgsfall
* @param errorHandler = Funktions-Handle für Fehlerfall
*/
function doGET(controller, action, parameters, successHandler, errorHandler) {
    var additionalParams = "";
    for(var key in parameters) {
        additionalParams += "&"+key+"="+parameters[key];
    }
    $.ajax({
        type: 'GET',
        url: "../index.php?controller="+controller+"&action="+action+additionalParams,
        dataType:"json",
    }).done(function(data) {
        broker.setConnected();
        successHandler(data);
    }).fail(function(error) {
        if(error.status === 404) {
            broker.setDisconnected();
        } else {
            broker.setConnected();
        }
        errorHandler(error);
    });
}

/**
* Funktion zum Absetzen eines POST-Requests
* @param controller = Bezeichnung des Controllers als String (wie in URL)
* @param action = Bezeichnung der Action als String
* @param parameterObject = Parameter als JSON-Objekt (String)
* @param successHandler = Funktions-Handle für Erfolgsfall
* @param errorHandler = Funktions-Handle für Fehlerfall
*/
function doPOST(controller, action, parameterObject, successHandler, errorHandler) {
    $.ajax({
        type: 'POST',
        url: "../index.php?controller="+controller+"&action="+action,
        dataType:"json",
        contentType:"application/json",
        data: parameterObject,
    }).done(function(data) {
        broker.setConnected();
        successHandler(data);
    }).fail(function(error) {
        if(error.status === 404) {
            broker.setDisconnected();
        } else {
            broker.setConnected();
        }
        errorHandler(error);
    });
}

/**
* Funktion zum Absetzen eines GET-Requests die falls gerade keine Online-Verbindung
* besteht, ein ggf. verfügbares passendes Cache-Element verwendet 
* @param controller = Bezeichnung des Controllers als String (wie in URL)
* @param action = Bezeichnung der Action als String
* @param parameters = Parameter als assoziatives Array Key=>Value
* @param successHandler = Funktions-Handle für Erfolgsfall
* @param errorHandler = Funktions-Handle für Fehlerfall
*/
function doGETwithCache(controller, action, parameters, successHandler, errorHandler) {
    if(broker.isConnected) {
        doGET(controller, action, parameters, 
            function(data) {
                broker.cache.storeToCache(controller, action, parameters, data);
                successHandler(data);
            }, 
            function(error) {
                if(error.status === 404) {
                    doGETwithCache(controller, action, parameters, successHandler, errorHandler);
                } else {
                    errorHandler(error);
                }
            }
        );
    } else {
        broker.cache.getFromCache(controller, action, parameters, successHandler, errorHandler);
    }
}


var broker = {
    /*
    * Flag, das festhält, ob bei der letzten Operation eine Verbindung bestand.
    */
    isConnected:true,

    /*
    * Objekt, in dem die Handler für das Reconnected-Event registriert werden können
    */
    reconnectHandler: {
        // Array zur Aufnahme der Handler
        handlers: [],
        
        add:function(handler) {
            broker.reconnectHandler.handlers.push(handler);
        },

        removeAll:function() {
            broker.reconnectHandler.handlers = [];
        },

        call:function(eventSource) {
            for(var key in broker.reconnectHandler.handlers) {
                var handler = broker.reconnectHandler.handlers[key];
                handler(eventSource);
            }
        },
    },

    /*
    * Setzt das isConnected-Flag auf true
    */
    setConnected:function() {
        var isReconnected = !broker.isConnected;
        broker.isConnected = true;
        if(isReconnected) {
            // Reconnected-Event auslösen
            broker.reconnectHandler.call(broker);
        }
    },
        
    /*
    * Setzt das isConnected-Flag auf false und initiiert einen regelmäßigen
    * überprüfungslauf, der prüft, ob evtl. wieder eine Verbindung besteht.
    */
    setDisconnected:function() {
        broker.isConnected = false;
        // in 30 Sek. das erste mal pruefen, ob wieder eine Verbindung besteht
        window.setTimeout(broker.checkConnection, 30000);
    },

    /*
    * Pruefen, ob wieder eine Verbindung besteht. Wenn nein, dann eine
    * erneute Pruefung in 30 Sek. initiieren.
    * Die Datei ping.php muss in manifest.php explizit in der Section NETWORK stehen!
    */
    checkConnection:function() {
        $.get("./ping.php").done( 
            function() {
                broker.setConnected();
            }).fail(
            function(error) {
                window.setTimeout(broker.checkConnection, 30000);
            });
    },
    cache: {
        /**
        * Liest das angeforderte Element aus dem Cache aus, falls es sich bereits darin
        * befindet. Wenn nicht wird ein Fehler weitergegeben.
        */
        getFromCache: function(controller, action, parameters, successHandler, errorHandler) {
            var key = broker.cache.getKey(controller, action, parameters);
            var object = JSON.parse(localStorage.getItem(key));
            if(!!object) { 
                // Wenn das Objekt existiert
                successHandler(object);
            } else {
                // Wenn es nicht existiert
                errorHandler();
            }
        },

        /*
        * Speichert das angegebene Objekt für die ebenfalls angegebene Kombination
        * aus controller, action und parameters in die localStorage des Browsers
        */
        storeToCache: function(controller, action, parameters, object) {
            var key = broker.cache.getKey(controller, action, parameters);
            localStorage.setItem(key, JSON.stringify(object));
        }, 

        /*
        * Wandelt den Request in einen eindeutigen Key um...
        */
        getKey:function(controller, action, parameters) {
            var key = controller+"#"+action+"#";
            for(var pkey in parameters) {
                key += pkey+"="+parameters[pkey]+"&";
            }
            return key;
        }
    },

};
