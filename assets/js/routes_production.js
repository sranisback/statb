const routes_prod = {
    "base_url": "http://statbrutedebowl.url.ph/statb/public",
    "routes": {
        "classementgen": {
            "tokens": [["variable", "\/", "[^\/]++", "etiquette"], ["variable", "\/", "[^\/]++", "annee"], ["text", "\/classement\/general"]],
            "defaults": {"etiquette": null},
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "classGenDetail": {
            "tokens": [["variable", "\/", "[^\/]++", "annee"], ["text", "\/classement\/detail"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "classementEquipe": {
            "tokens": [["variable", "\/", "[^\/]++", "annee"], ["variable", "\/", "[^\/]++", "limit"], ["variable", "\/", "[^\/]++", "type"], ["text", "\/classementEquipe"]],
            "defaults": {"limit": 0},
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "classementJoueur": {
            "tokens": [["variable", "\/", "[^\/]++", "annee"], ["variable", "\/", "[^\/]++", "limit"], ["variable", "\/", "[^\/]++", "type"], ["text", "\/classementJoueur"]],
            "defaults": {"limit": 0},
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "app_classement_affichetotalcas": {
            "tokens": [["variable", "\/", "[^\/]++", "annee"], ["text", "\/totalcas"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "app_classement_cinqderniermatch": {
            "tokens": [["text", "\/cinqDernierMatch\/"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "app_classement_cinqderniermatchpourequipe": {
            "tokens": [["variable", "\/", "[^\/]++", "equipeId"], ["text", "\/cinqDernierMatchPourEquipe"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "app_classement_touslesmatchespourequipe": {
            "tokens": [["variable", "\/", "[^\/]++", "equipeId"], ["text", "\/tousLesMatchesPourEquipe"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "montreLeCimetierre": {
            "tokens": [["text", "\/montreLeCimetierre"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "montreClassementELO": {
            "tokens": [["text", "\/montreClassementELO"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "montreConfrontation": {
            "tokens": [["text", "\/montreConfrontation"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "ancienClassement": {
            "tokens": [["variable", "\/", "[^\/]++", "annee"], ["text", "\/ancienClassement"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "listeAncienneAnnnee": {
            "tokens": [["text", "\/listeAnciennesAnnees"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "matchesContreCoach": {
            "tokens": [["variable", "\/", "[^\/]++", "coachId"], ["text", "\/matchesContreCoach"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "app_classement_calculclassementgen": {
            "tokens": [["variable", "\/", "[^\/]++", "annee"], ["text", "\/calculClassementGen"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "ajoutDefisForm": {
            "tokens": [["variable", "\/", "[^\/]++", "coachId"], ["text", "\/ajoutDefisForm"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "ajoutDefis": {
            "tokens": [["text", "\/ajoutDefis"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "afficherDefis": {
            "tokens": [["text", "\/afficherDefis"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "afficherPeriodeDefisActuelle": {
            "tokens": [["text", "\/afficherPeriodeDefisActuelle"]],
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
        "showteams": {
            "tokens": [["text", "\/montreLesEquipes"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "showOldTeams": {
            "tokens": [["variable", "\/", "[^\/]++", "coachActif"], ["text", "\/montreLesAnciennesEquipes"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "showuserteams": {
            "tokens": [["text", "\/showuserteams"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "team": {
            "tokens": [["variable", "\/", "\\d+", "teamid"], ["text", "\/team"]],
            "defaults": [],
            "requirements": {"teamid": "\\d+"},
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "montreEquipe": {
            "tokens": [["variable", "\/", "[^\/]++", "nomEquipe"], ["text", "\/team"]],
            "defaults": [],
            "requirements": {"nommEquipe": "\\D+"},
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "uploadLogo": {
            "tokens": [["variable", "\/", "[^\/]++", "equipeId"], ["text", "\/uploadLogo"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "createTeam": {
            "tokens": [["text", "\/createTeam"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "app_equipe_choixrace": {
            "tokens": [["text", "\/choixRace"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "app_equipe_retteam": {
            "tokens": [["variable", "\/", "[^\/]++", "teamId"], ["text", "\/retTeam"]],
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
        "Chkteam": {
            "tokens": [["variable", "\/", "[^\/]++", "teamId"], ["text", "\/chkteam"]],
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
            "tokens": [["variable", "\/", "[^\/]++", "equipeId"], ["text", "\/ajoutStadeModal"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "ajoutStade": {
            "tokens": [["variable", "\/", "[^\/]++", "equipeId"], ["text", "\/ajoutStade"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "recalculerTV": {
            "tokens": [["text", "\/recalculerTV"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "listeDesJoueurs": {
            "tokens": [["variable", "\/", "[^\/]++", "equipe"], ["text", "\/listeDesJoueurs"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "supprimeLogo": {
            "tokens": [["variable", "\/", "[^\/]++", "equipeId"], ["text", "\/supprimeLogo"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "mettreEnFranchise": {
            "tokens": [["variable", "\/", "[^\/]++", "equipeId"], ["text", "\/mettreEnFranchise"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "pdfTeam": {
            "tokens": [["variable", "\/", "[^\/]++", "id"], ["text", "\/pdfTeam"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "pdfTournois": {
            "tokens": [["text", "\/pdfTournois"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "Player": {
            "tokens": [["variable", "\/", "[^\/]++", "playerid"], ["text", "\/player"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "getposstat": {
            "tokens": [["variable", "\/", "[^\/]++", "posId"], ["text", "\/getposstat"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "playerAdder": {
            "tokens": [["variable", "\/", "[^\/]++", "equipe"], ["text", "\/playerAdder"]],
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
        "skillmodal": {
            "tokens": [["variable", "\/", "[^\/]++", "playerid"], ["text", "\/skillmodal"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "ajoutComp": {
            "tokens": [["variable", "\/", "[^\/]++", "playerid"], ["text", "\/ajoutComp"]],
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
        },
        "uploadPhoto": {
            "tokens": [["variable", "\/", "[^\/]++", "joueurId"], ["text", "\/uploadPhoto"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "supprimePhoto": {
            "tokens": [["variable", "\/", "[^\/]++", "joueurId"], ["text", "\/supprimePhoto"]],
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
        "ajoutMatch": {
            "tokens": [["text", "\/ajoutMatch"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "match": {
            "tokens": [["variable", "\/", "[^\/]++", "matchId"], ["text", "\/match"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "anciensMatchs": {
            "tokens": [["variable", "\/", "[^\/]++", "coachActif"], ["text", "\/anciensMatchs"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "matchsAnnee": {
            "tokens": [["text", "\/matchsAnnee"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "ajoutPenaliteForm": {
            "tokens": [["text", "\/ajoutPenaliteForm"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "ajoutPenalite": {
            "tokens": [["text", "\/ajoutPenalite"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "afficherPenalite": {
            "tokens": [["text", "\/afficherPenalite"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "ajoutPrimeForm": {
            "tokens": [["variable", "\/", "[^\/]++", "primeId"], ["variable", "\/", "[^\/]++", "coachId"], ["text", "\/ajoutPrimeForm"]],
            "defaults": {"primeId": null},
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "ajoutPrime": {
            "tokens": [["variable", "\/", "[^\/]++", "coachId"], ["text", "\/ajoutPrime"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "montrePrimesEnCours": {
            "tokens": [["text", "\/montrePrimesEnCours"]],
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
        "realiserPrimeForm": {
            "tokens": [["text", "\/realiserPrimeForm"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "realiserPrime": {
            "tokens": [["text", "\/realiserPrime"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "index": {
            "tokens": [["text", "\/"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "login": {
            "tokens": [["text", "\/login"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "logout": {
            "tokens": [["text", "\/logout"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "app_statbb_citation": {
            "tokens": [["text", "\/citation"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "dyk": {
            "tokens": [["text", "\/dyk"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "frontUser": {
            "tokens": [["text", "\/frontUser"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "tabCoach": {
            "tokens": [["text", "\/tabCoach"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "tabLigue": {
            "tokens": [["text", "\/tabLigue"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "app_statbb_testicon": {
            "tokens": [["text", "\/testIcons"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "app_statbb_attributiconmanquante": {
            "tokens": [["text", "\/attributIconManquante"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "app_statbb_generenommanquant": {
            "tokens": [["text", "\/genereNomManquant"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "tournois": {
            "tokens": [["text", "\/tournois"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "listePosition": {
            "tokens": [["variable", "\/", "[^\/]++", "raceId"], ["text", "\/listePosition"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "nombreVersComp": {
            "tokens": [["variable", "\/", "[^\/]++", "positionId"], ["text", "\/nombreVersComp"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "classeLesComp": {
            "tokens": [["variable", "\/", "[^\/]++", "doub"], ["variable", "\/", "[^\/]++", "norm"], ["text", "\/classeLesComp"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "usercontrol": {
            "tokens": [["text", "\/usercontrol\/"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "ajoutCitation": {
            "tokens": [["text", "\/ajoutCitation"]],
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

export default routes_prod;