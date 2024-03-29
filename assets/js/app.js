import './jquery-ui.js';
import './jquery.serializeToJSON.js';
import './jquery.dataTables.min.js';
import './library.js';
import './feuilleTournois.js';
import './jquery-editable.js'
import './admin.js'

import routes_dev from './routes_dev.js';
import routes_prod from './routes_production.js';

let $ = require('jquery');

require('bootstrap');
require('popper.js');

import Routing from './router.min.js';

$(document).ready(function () {
    $(document).on('change', '.custom-file-input', function () {
        let fileName = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
        $(this).parent('.custom-file').find('.custom-file-label').text(fileName);
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    console.log(process.env.ENV);
    //switch (process.env.ENV) {
    /*  case 'dev':
          */
    Routing.setRoutingData(routes_dev);
      //      break;
       // case 'prod':*/
    //Routing.setRoutingData(routes_prod);
    /* break;
}*/

    $("#ajax").text(Routing.generate('changeNomStade'));

    $('#classgen').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false,
        "responsive": true
    });

    $('#classgenDet').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false,
        "responsive": true
    });

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
            null,
            null,
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

    $('#TablePenalite').DataTable({
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
    $("#player_position").change(function () {
        $("#pos_table").remove();
        if ($(this).val() !== '') {
            $.post(Routing.generate('getposstat',
                {
                    posId: $(this).val()
                }),
                {
                    ruleset : $("#ruleset").val()
                },
                function (result) {
                    $("#loadingmessage").hide();
                    $("#pos_table").remove();
                    $("#player_position").after(result);
                    $('btn_addplayer').prop('disabled', true);
                })
            $("#pos_table").remove();
            $("#player_position").after($('#loadingmessage'));
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

            $.post(Routing.generate('addPlayer'),
                {
                    idPosition: $("#player_position").val(),
                    teamId: $("#equipe_Id").val(),
                    nom: $("#player_futur_nom").val(),
                    nr: $("#player_futur_numero").val(),
                    ruleset : $("#ruleset").val()
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
                        $.post(Routing.generate('genereNumero'), {equipeId: $("#equipe_Id").val()}, function (num) {
                            $("#loadingmessage").hide();
                            $("#player_futur_numero").attr('value', num);
                        });

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
                }
            );
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
        origin.append($('#loadingmessage'));
        $('#loadingmessage').show();
        $.post(Routing.generate('remPlayer', {playerId: origin.attr("playerId")}),
            {},
            function (result) {
                result = JSON.parse(result);

                switch (result.reponse) {
                    case "rm":
                        line.remove();
                        break;
                    case "sld":
                        line.addClass("table-info");
                        $('#loadingmessage').hide();
                        line.toggle();
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

                $("#tresor").text(result.tresor);
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

        if ($('#selectedTeam_' + clicked.attr('side')).val() !== '') {
            $('#selectedTeam_' + clicked.attr('side')).css('border', '');
            addLine(clicked, $(".form-group #action").length);
        } else {
            $('#selectedTeam_' + clicked.attr('side')).css('border', 'solid red');
        }
    });

    /**
     * ajout de ligne dans feuille de match
     */
    function addLine(clicked, number) {
        $("#team" + clicked.attr('side') + "_flex_sl_container").after($('#loadingmessage'));

        $('#loadingmessage').show();
        $.getJSON(Routing.generate('dropdownPlayer', {teamId: clicked.attr('teamId'), nbr: number}),
            {},
            function (result) {
                $('#loadingmessage').hide();
                result = JSON.parse(result);

                //$("#team" + clicked.attr('side') + "_flex_sl_container").after(result.html);
                $("#liste" + clicked.attr('side')).append(result.html);
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

    $('#supprimePhoto').click(function () {
        $.post(Routing.generate('supprimePhoto', {joueurId: $(this).attr('joueurId')}),
            {},
            function () {
                window.location.reload();
            });
    });

    $('#supprimeLogo').click(function () {
        $.post(Routing.generate('supprimeLogo', {equipeId: $(this).attr('teamId')}),
            {},
            function () {
                window.location.reload();
            });
    });

    $('[id^=editable_joueur]').editable({
        mode: 'inline',
        url: Routing.generate('changeNomEtNumero')
    });

    $('#editable_stade').editable({
        mode: 'inline',
        url: Routing.generate('changeNomStade')
    });

    /////admin

    if ($('#Admin').attr('entity')) {
        let routeName = 'updateEditable' + $('#Admin').attr('entity')

        $('[id^=admin_]').editable({
            mode: 'inline',
            url: Routing.generate(routeName)
        });

        $('#Admin').DataTable({
            "lengthChange": false,
            "pageLength": 20,
            "info": false,
            "responsive": true
        });
    }
});



