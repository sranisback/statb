import './jquery-ui.js'
import './jquery.serializeToJSON.js'
import './jquery.dataTables.min.js'
import './library.js'

let $ = require('jquery');

require('bootstrap');
require('popper.js');

import Routing from './router.min.js';
import html2canvas from './html2canvas.js';

//json route prod

const routes = {
    "base_url": "http://statbrutedebowl.url.ph/statb/public",
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
            "tokens": [["variable", "\/", "[^\/]++", "defisId"],["text", "\/supprimerDefis"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        ,
        "genereNom" : {
            "tokens": [["text", "\/genereNom"]],
            "defaults": [],
            "requirements": [],
            "hosttokens": [],
            "methods": [],
            "schemes": []
        },
        "genereNumero" : {
            "tokens": [["text", "\/genereNumero"]],
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

$(document).ready(function () {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    Routing.setRoutingData(routes);

    $('#classgen').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false,
        "responsive": true

    });


    if (window.location.href.indexOf('?capture') > -1 ){
        var element = document.getElementById("card_classgen");

        html2canvas(element).then(function (canvas) {
            // Export the canvas to its data URI representation
            var base64image = canvas.toDataURL("image/png");

            // Open the image in a new window
            window.open(base64image , "_blank");
        });
    }

    $('#capture_classementgen').click(function () {
        window.location.href = window.location.href+'?capture';
    })

    $('#equipesEnCours').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false,
        "searching": false,
        "paging": false,
        "columns": [
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            {
                "orderable": false
            }
        ]
    });

    $('#listeMatchs').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false,
        "ordering": false
    });

    $('#TableCimetierre').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false,
        "order": [[3, "desc"]]
    });

    $('#TableElo').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false,
        "order": [[4, "desc"]]
    });

    $('#TablePrimes').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false
    });

    $('#TableDefis').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false
    });

    $('#TableAnciennesEquipes').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false,
        "order": [[7, "asc"], [0, "asc"]]
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
        $("tr.table-danger").toggle();
        $("tr.table-info").toggle();
    });

    /**
     * selection de joueur
     */
    $("#ajout_joueur_fPos").change(function () {
        $("#pos_table").remove();
        if ($(this).val() !== '') {
            $.post(Routing.generate('getposstat', {posId: $(this).val()}),
                {},
                function (result) {
                    $("#loadingmessage").hide();
                    $("#pos_table").remove();
                    $("#ajout_joueur_fPos").after(result);
                    $('btn_addplayer').prop('disabled', true);
                })

            $("#pos_table").remove();
            $("#ajout_joueur_fPos").after($('#loadingmessage'));
            $('#loadingmessage').show();
        }

    });

    /**
     * Ajout d'un joueur
     */
    $("#btn_addplayer").click(function () {
        if ($("#player_futur_nom").val() !== '' && $("#player_futur_numero").val() !== '' && $("#ajout_joueur_fPos").val() !== '') {
            $("#teamsheet").after($('#loadingmessage'));
            $('#loadingmessage').show();
            var teamId = $(this).attr('teamId')

            $.post(Routing.generate('addPlayer'),
                {
                    idPosition: $("#ajout_joueur_fPos").val(),
                    teamId: teamId,
                    nom: $("#player_futur_nom").val(),
                    nr: $("#player_futur_numero").val()
                },
                function (result) {
                    $("#loadingmessage").hide();
                    result = JSON.parse(result);
                    let totalltv = $("#totalPV");
                    $('#res').remove();

                    if (result.reponse == "ok") {

                        $("#player_futur_nom").after($('#loadingmessage'));
                        $('#loadingmessage').show();
                        $.post(Routing.generate('genereNom'), {}, function (nom) {
                            $("#loadingmessage").hide();
                            $("#player_futur_nom").attr('value', nom);
                        });

                        $("#player_futur_numero").after($('#loadingmessage'));
                        $('#loadingmessage').show();
                        $.post(Routing.generate('genereNumero'), {equipeId: teamId}, function (num) {
                            $("#loadingmessage").hide();
                            $("#player_futur_numero").attr('value', num);
                        });

                        //$('#liste_joueur_adder').append('<tr><td>'+result.NrJoueur+'</td><td>'+result.NomJoueur+'</td><td>'+result.PositionJoueur +'</td><td>'+result.playercost+'</td></tr>');

                        $("#teamsheet").append(result.html);

                        $("#caseTv").text(result.tv);
                        $("#pTv").text(result.ptv);
                        totalltv.text(Number(totalltv.text()) + result.playercost);
                        $("#tresor").text(result.tresor);
                        $("#tresor2").text(result.tresor);

                    } else {
                        $('#res').remove();
                        $(".modal-body").prepend('<div id="res" class="alert alert-danger">' + result.html + '</div>');
                    }
                });
        } else {
            alert('Merci d\'entrer les infos manquantes !');
        }

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
        actionInducement($(this), 'add');
    });

    /**
     * suppr d'inducement
     */
    $("[id^='rem_']").click(function () {
        actionInducement($(this), 'rem');
    });

    /**
     * Function gestion des inducements
     * @param origin
     * @param mvt
     */
    function actionInducement(origin, mvt) {
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
                if (result.inducost > 0) {
                    $("#t" + result.type).text(result.inducost * result.nbr);
                }
                $("#caseTv").text(result.tv);
                $("#pTv").text(result.ptv);

                $("#tresor").text(result.tresor)

                /**
                 * check pour paiement stade
                 */
                if ($("#pay").text() == 150000 && result.type == "pay") {
                    $.post(Routing.generate('ajoutStadeModal', {teamId: origin.attr("teamId")}), {}, function (result) {
                        $('#rem_pay').after(' ' + result)
                    });
                }
            });
    }

    /**
     * cpt modal ajout de joueur
     */
    $("#addplayer").on('show.bs.modal', function () {
        $(this).draggable();
    });

    $("#addplayer").on('hide.bs.modal', function () {
        window.location.reload();
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

                $("#team" + clicked.attr('side') + "_flex_sl_container").after(result.html);

            })
    }

    /*
    * Ajouter le match
     */
    $("#recMatch").click(function () {
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

        $('#teamId_' + id).keypress(function (e) {
            if (e.which == 13) {
                $('#teamId_' + id).after($('#loadingmessage'));
                $('#loadingmessage').show();
                $.post(Routing.generate('changeNomStade', {
                        nouveauNomStade: $(this).val(),
                        equipeId: $(this).attr('teamId')
                    }),
                    {},
                    function () {
                        $('#teamId_' + id).replaceWith('<div id="#stade_name" playerid="' + $('#teamId_' + id).attr('teamId') + '">' + $('#teamId_' + id).val() + '</div>');

                        window.location.reload();
                    });
            }

        });
    })

    $("[id^='name_']").click(function () {
        let id = $(this).attr('id').substring($(this).attr('id').indexOf('_') + 1, $(this).attr('id').length);

        $(this).replaceWith('<input type="text" id="inp_name_' + id + '" placeholder="' + $(this).text() + '" playerid="' + $(this).attr('playerid') + '" value="' + $(this).text() + '" data-toggle="tooltip" title="Appuyez sur enter pour valider" >').focus();

        $('#inp_name_' + id).keypress(function (e) {
            if (e.which == 13) {

                $('#inp_name_' + id).after($('#loadingmessage'));
                $('#loadingmessage').show();

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

