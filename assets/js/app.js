let $ = require('jquery');

require('bootstrap-sass');


import './jquery-ui.js'
import './jquery.serializeToJSON.js'
import './jquery.dataTables.min.js'


import Routing from './router.min.js';

//json route prod

const routes = {
    "base_url": "http://statbrutedebowl.url.ph/statb/public",
    "routes": {
        "montreLesEquipes": {
            "tokens": [["text", "\/montreLesEquipes"]],
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
            "tokens": [["variable", "\/", "[^\/]++", "type"], ["variable", "\/", "[^\/]++", "teamid"], ["text", "\/team"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "Player": {
            "tokens": [["variable", "\/", "[^\/]++", "type"], ["variable", "\/", "[^\/]++", "playerid"], ["text", "\/player"]],
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
        "admin": {
            "tokens": [["text", "\/admin"]],
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
        "citation": {
            "tokens": [["text", "\/citation"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "classementgen": {
            "tokens": [["variable", "\/", "[^\/]++", "limit"], ["text", "\/classement\/general"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "classement": {
            "tokens": [["variable", "\/", "[^\/]++", "limit"], ["variable", "\/", "[^\/]++", "teamorplayer"], ["variable", "\/", "[^\/]++", "type"], ["text", "\/classement"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "totalcas": {
            "tokens": [["text", "\/totalcas"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "lastfive": {
            "tokens": [["text", "\/lastfive"]],
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
        "getposstat": {
            "tokens": [["variable", "\/", "[^\/]++", "posId"], ["text", "\/getposstat"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "raceselector": {
            "tokens": [["text", "\/choixRace"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "tk": {
            "tokens": [["text", "\/tk"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "player_adder": {
            "tokens": [["variable", "\/", "[^\/]++", "teamId"], ["variable", "\/", "[^\/]++", "raceId"], ["text", "\/player_adder"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "addPlayer": {
            "tokens": [["variable", "\/", "[^\/]++", "teamId"], ["variable", "\/", "[^\/]++", "posId"], ["text", "\/addPlayer"]],
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
        "dropdownteams": {
            "tokens": [["variable", "\/", "[^\/]++", "nbr"], ["text", "\/dropdownTeams"]],
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
        "Chkteam": {
            "tokens": [["variable", "\/", "[^\/]++", "teamId"], ["text", "\/chkteam"]],
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
        "addComp": {
            "tokens": [["variable", "\/", "[^\/]++", "playerid"], ["variable", "\/", "[^\/]++", "skillid"], ["text", "\/addComp"]],
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
            "tokens": [["variable", "\/", "[^\/]++", "defisId"],["text", "\/supprimerDefis"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        }
        "prefix": "",
        "host": "localhost",
        "port": "",
        "scheme": "http"
    }
}
//json route dev
/*
const routes = {
    "base_url": "",
    "routes": {
        "montreLesEquipes": {
            "tokens": [["text", "\/montreLesEquipes"]],
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
            "tokens": [["variable", "\/", "[^\/]++", "type"], ["variable", "\/", "[^\/]++", "teamid"], ["text", "\/team"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "Player": {
            "tokens": [["variable", "\/", "[^\/]++", "type"], ["variable", "\/", "[^\/]++", "playerid"], ["text", "\/player"]],
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
        "admin": {
            "tokens": [["text", "\/admin"]],
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
        "citation": {
            "tokens": [["text", "\/citation"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "classementgen": {
            "tokens": [["variable", "\/", "[^\/]++", "limit"], ["text", "\/classement\/general"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "classement": {
            "tokens": [["variable", "\/", "[^\/]++", "limit"], ["variable", "\/", "[^\/]++", "teamorplayer"], ["variable", "\/", "[^\/]++", "type"], ["text", "\/classement"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "totalcas": {
            "tokens": [["text", "\/totalcas"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "lastfive": {
            "tokens": [["text", "\/lastfive"]],
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
        "getposstat": {
            "tokens": [["variable", "\/", "[^\/]++", "posId"], ["text", "\/getposstat"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "raceselector": {
            "tokens": [["text", "\/choixRace"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "tk": {
            "tokens": [["text", "\/tk"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "player_adder": {
            "tokens": [["variable", "\/", "[^\/]++", "teamId"], ["variable", "\/", "[^\/]++", "raceId"], ["text", "\/player_adder"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "addPlayer": {
            "tokens": [["variable", "\/", "[^\/]++", "teamId"], ["variable", "\/", "[^\/]++", "posId"], ["text", "\/addPlayer"]],
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
        "dropdownteams": {
            "tokens": [["variable", "\/", "[^\/]++", "nbr"], ["text", "\/dropdownTeams"]],
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
        "Chkteam": {
            "tokens": [["variable", "\/", "[^\/]++", "teamId"], ["text", "\/chkteam"]],
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
        "addComp": {
            "tokens": [["variable", "\/", "[^\/]++", "playerid"], ["variable", "\/", "[^\/]++", "skillid"], ["text", "\/addComp"]],
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
            "tokens": [["variable", "\/", "[^\/]++", "teamId"],["text", "\/ajoutStadeModal"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "supprimerPrime": {
            "tokens": [["variable", "\/", "[^\/]++", "primeId"],["text", "\/supprimerPrime"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "supprimerDefis": {
            "tokens": [["variable", "\/", "[^\/]++", "defisId"],["text", "\/supprimerDefis"]],
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
*/
$(document).ready(function () {

    Routing.setRoutingData(routes);

    /**
     * table classement général
     */
    $('#classgen').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false

    });

    /*
     * table cimetierre
     */
    $('#TableCimetierre').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false,
        "order": [[ 3, "desc" ]]
    });

    /*
     * table ELO
     */
    $('#TableElo').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false,
        "order": [[ 4, "desc" ]]
    });

    /*
     * table Primes
     */
    $('#TablePrimes').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false
    });

    /*
     * table Defis
     */
    $('#TableDefis').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false
    });

    /*
     * retirer prime
     */
    $("[id^='enleve_prime_']").click(function () {
        $(this).after($('#loadingmessage'));
        $.post(Routing.generate('supprimerPrime', {primeId: $(this).attr("primeId")}),
            {},
            function (result) {
                window.location.reload();
            });
    });

    /*
     * retirer defis
     */
    $("[id^='enleve_defis_']").click(function () {
        $(this).after($('#loadingmessage'));
        $.post(Routing.generate('supprimerDefis', {defisId: $(this).attr("defisId")}),
            {},
            function (result) {
                window.location.reload();
            });
    });

    /**
     * bouton qui montre tout les joueurs/équipes
     */

    $('#showall_btn').click(function () {
        $("tr.danger").toggleClass("hidden");
        $("tr.info").toggleClass("hidden");
    });

    /**
     * selection de race/création d'équipe
     */
    $("#selectedPos li a").click(function () {
        $("#pos_table").remove();
        let label = $('#dLabel');
        $("#res").remove();
        label.html($(this).text() + ' <span class="caret"></span>');
        label.val($(this).data('value'));
        $('#btn_addplayer').attr('posId', $(this).attr('posId'));
        $("#teamdrop").after($('#loadingmessage'));
        $.post(Routing.generate('getposstat', {posId: $(this).attr('posId')}),
            {},
            function (result) {
                $("#loadingmessage").hide();
                $("#pos_table").remove();
                $("#teamdrop").after(result);
            })

    });

    /**
     * Ajout d'un joueur
     */
    $("#btn_addplayer").click(function () {
        $("#teamsheet").after($('#loadingmessage'));
        $('#loadingmessage').show();
        $.getJSON(Routing.generate('addPlayer', {posId: $(this).attr('posId'), teamId: $(this).attr('teamId')}),
            {},
            function (result) {
                $("#loadingmessage").hide();
                result = JSON.parse(result);

                let totalltv = $("#totalPV");
                let res = $("#res");
                let modalfooter = $(".modal-footer");

                res.remove();

                if (result.reponse == "ok") {
                    $("#teamsheet").append(result.html);

                    $("#caseTv").text(result.tv);
                    $("#pTv").text(result.ptv);
                    totalltv.text(Number(totalltv.text()) + result.playercost);
                    $("#tresor").text(result.tresor);

                    /**
                     * remove player en cas de besoin
                     */
                    $("[id^='remove_pl']").click(function () {
                        removePlayer($(this));
                    });

                } else {
                    res.remove();
                    $("#teamdrop").before('<div id="res" class="alert alert-danger" role="alert">' + result.html + '</div>');

                }
            });
    });

    /**
     * suppression/renvois d'un joueur
     */
    $("[id^='remove_pl']").click(function () {
        removePlayer($(this));
    });

    /*
    * Fonction pour enlever un joueur d'une équipe
     */
        function removePlayer(origin) {

        let line = origin.parent().parent();
        let totalPV = $("#totalPV");

        $.post(Routing.generate('remPlayer', {playerId: origin.attr("playerId")}),
            {},
            function (result) {
                result = JSON.parse(result);

                switch (result.reponse) {
                    case "rm":
                        line.remove();
                        break;
                    case "sld":
                        line.addClass("info hidden");
                        break;
                }

                $("#caseTv").text(result.tv);
                $("#pTv").text(result.ptv);
                totalPV.text(Number(totalPV.text()) - result.playercost);
                $("#tresor").text(result.tresor);
            });
    }

    /**
     * Ajout d'inducement
     */
    $("[id^='add_']").click(function () {
        actionInducement($(this),'add');
    });

    /**
     * suppr d'inducement
     */
    $("[id^='rem_']").click(function () {
        actionInducement($(this),'rem');
    });

    /**
     * Function gestion des inducements
     * @param origin
     * @param mvt
     */
    function actionInducement(origin,mvt){
        $("#" + origin.attr("type")).before($('#loadingmessage'));
        $('#loadingmessage').show();
        $.post(Routing.generate('gestionInducement', {
                teamId: origin.attr("teamId"),
                type: origin.attr("type"),
                action: mvt
            }),
            {},
            function (result) {
                $("#loadingmessage").hide();
                result = JSON.parse(result);

                $("#" + result.type).text(result.nbr);
                if(result.inducost>0){
                    $("#t" + result.type).text(result.inducost * result.nbr);
                }
                $("#caseTv").text(result.tv);
                $("#pTv").text(result.ptv);

                $("#tresor").text(result.tresor)

                /**
                 * check pour paiement stade
                 */
                if($("#pay").text() == 150000 && result.type == "pay"){
                    $.post(Routing.generate('ajoutStadeModal',{teamId: origin.attr("teamId")}),{},function(result){$('#rem_pay').after(' ' + result)});
                }
            });
    }

    /**
     * cpt modal ajout de joueur
     */
    $("#addplayer").on('hide.bs.modal', function () {

        window.location.reload();

    });

    $("#addplayer").on('show.bs.modal', function () {
        $(this).draggable();
        $('.modal-backdrop').remove();

    });

    $("[id^='retire_']").click(function () {
        let clicked = $(this).parent().parent();
        $.post(Routing.generate('retTeam', {teamId: $(this).attr("teamId")}),
            {},
            function () {
                clicked.addClass("danger hidden")
            });
    });

    /*
    * gestion du form match dynamique
     */

    $("[id^='selectedTeam']").change(function () {
        let boutonAmodifier = $("#valideteam" + $(this).attr("side"))

        boutonAmodifier.attr("teamId", $(this).val())
        boutonAmodifier.attr("side", $(this).attr("side"))
    });


    $("[id^='valideteam']").click(function () {
        let clicked = $(this);

        addLine(clicked, $(".form-group #action").length)
    });

    /**
     * ajout de ligne dans feuille de match
     */
    function addLine(clicked, number) {
        $.getJSON(Routing.generate('dropdownPlayer', {teamId: clicked.attr('teamId'), nbr: number}),
            {},
            function (result) {

                result = JSON.parse(result);

                $("#team"+clicked.attr('side')+"_select_container").after(result.html);

            })
    }

    /*
    * Ajouter le match
     */
    $("#recMatch").click(function () {
        //alert(JSON.stringify($("#formMatch").serializeToJSON()));
        $.post(Routing.generate('addGame'), JSON.stringify($("#formMatch").serializeToJSON()),
            function () {
                window.location.reload();
            }, "json");
    });
    
    /**
     * renommer le stade
     */

    $("#stade_name").click(function () {
        let id = $(this).attr("teamId");

        $(this).replaceWith('<input type="text" id="teamId_' + id + '" placeholder="' + $(this).text() + '" teamId="' + id + '" value="' + $(this).text() + '" data-toggle="tooltip" title="Appuyez sur enter pour valider">').focus();

        $('#teamId_'+id).keypress(function(e){
            if (e.which == 13) {
                $('#teamId_' + id).after($('#loadingmessage'));
                $('#loadingmessage').show();
                $.post(Routing.generate('changeNomStade', {nouveauNomStade: $(this).val(), equipeId: $(this).attr('teamId')}),
                    {},
                    function () {
                        $('#teamId_'+id).replaceWith('<div id="#stade_name" playerid="' + $('#teamId_'+ id).attr('teamId') + '">' + $('#teamId_'+ id).val() + '</div>');

                        window.location.reload();
                    });
            }

        });
    })

    $("[id^='number_']").click(function () {
        let id = $(this).attr('id').substring($(this).attr('id').indexOf('_') + 1, $(this).attr('id').length);

        $(this).replaceWith('<input type="text" id="inp_' + id + '" placeholder="' + $(this).text() + '" playerid="' + $(this).attr('playerid') + '" value="' + $(this).text() + '" data-toggle="tooltip" title="Appuyez sur enter pour valider">').focus();

        $('#inp_' + id).keypress(function (e) {
            if (e.which == 13) {
                $('#inp_' + id).after($('#loadingmessage'));
                $('#loadingmessage').show();
                $.post(Routing.generate('changeNr', {newnr: $(this).val(), playerid: $(this).attr('playerid')}),
                    {},
                    function () {
                        $('#inp_' + id).replaceWith('<div id="number_' + $('#inp_' + id).val() + '" playerid="' + $('#inp_' + id).attr('playerid') + '">' + $('#inp_' + id).val() + '</div>');

                        window.location.reload();
                    });
            }
        });
    });

    $("[id^='name_']").click(function () {
        let id = $(this).attr('id').substring($(this).attr('id').indexOf('_') + 1, $(this).attr('id').length);

        $(this).replaceWith('<input type="text" id="inp_name_' + id + '" placeholder="' + $(this).text() + '" playerid="' + $(this).attr('playerid') + '" value="' + $(this).text() + '" data-toggle="tooltip" title="Appuyez sur enter pour valider" >').focus();

        $('#inp_name_' + id).keypress(function (e) {
            if (e.which == 13) {

                $('#inp_name_' + id).after($('#loadingmessage'));
                $('#loadingmessage').show();

                //$.post("./changeName/"+$(this).val()+"/"+ $(this).attr('playerid'),
                $.post(Routing.generate('changeName', {newname: $(this).val(), playerid: $(this).attr('playerid')}),
                    {},
                    function (result) {

                        $('#inp_name_' + id).replaceWith('<div id="name_' + $('#inp_name_' + id).val() + '" playerid="' + $('#inp_name_' + id).attr('playerid') + '">' + $('#inp_name_' + id).val() + '</div>')
                        window.location.reload();
                });
            }
        });
    });

});

