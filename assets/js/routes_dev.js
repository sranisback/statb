const routes_dev = {
    "base_url": "",
    "routes": {
        "getposstat": {
            "tokens": [["variable", "\/", "[^\/]++", "posId"], ["text", "\/getposstat"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "addPlayer": {
            "tokens": [["text", "\/addPlayer"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "remPlayer": {
            "tokens": [["variable", "\/", "[^\/]++", "playerId"], ["text", "\/remPlayer"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "gestionInducement": {
            "tokens": [["variable", "\/", "[^\/]++", "type"], ["variable", "\/", "[^\/]++", "teamId"], ["variable", "\/", "[^\/]++", "action"], ["text", "\/gestionInducement"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "retTeam": {
            "tokens": [["variable", "\/", "[^\/]++", "teamId"], ["text", "\/retTeam"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "dropdownPlayer": {
            "tokens": [["variable", "\/", "[^\/]++", "nbr"], ["variable", "\/", "[^\/]++", "teamId"], ["text", "\/dropdownPlayer"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "addGame": {
            "tokens": [["text", "\/addGame"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "changeNr": {
            "tokens": [["variable", "\/", "[^\/]++", "playerid"], ["variable", "\/", "[^\/]++", "newnr"], ["text", "\/changeNr"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "changeName": {
            "tokens": [["variable", "\/", "[^\/]++", "playerid"], ["variable", "\/", "[^\/]++", "newname"], ["text", "\/changeName"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "changeNomStade": {
            "tokens": [["variable", "\/", "[^\/]++", "nouveauNomStade"], ["variable", "\/", "[^\/]++", "equipeId"], ["text", "\/changeNomStade"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "ajoutStadeModal": {
            "tokens": [["variable", "\/", "[^\/]++", "teamId"], ["text", "\/ajoutStadeModal"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "supprimerPrime": {
            "tokens": [["variable", "\/", "[^\/]++", "primeId"], ["text", "\/supprimerPrime"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "supprimerDefis": {
            "tokens": [["variable", "\/", "[^\/]++", "defisId"], ["text", "\/supprimerDefis"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "genereNom": {
            "tokens": [["text", "\/genereNom"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "genereNumero": {
            "tokens": [["text", "\/genereNumero"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        }
    },
    "prefix": "",
    "host": "localhost",
    "port": "",
    "scheme": "http"
}

export default routes_dev;