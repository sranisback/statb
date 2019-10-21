
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
        },
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

    $('#classgen').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false

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
        "order": [[ 3, "desc" ]]
    });

    $('#TableElo').DataTable({
        "lengthChange": false,
        "pageLength": 20,
        "info": false,
        "order": [[ 4, "desc" ]]
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
        "order": [[ 7, "asc" ],[0,"asc"]]
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

	
	$('#showall_btn').click(function () {
				
		$("tr.danger").toggleClass("hidden");
		$("tr.info").toggleClass("hidden");
	});
	
	$("#selectedRace li a").click(function(){

	    let label = $('#dLabel');

        label.html($(this).text() + ' <span class="caret"></span>');
        label.val($(this).data('value'));

        label.attr('coachId',$(this).attr('coachId'));
        label.attr('raceId',$(this).attr('raceId'));
	  
	});
	
	
	
	
	$("[id^='selectedTeam']").change(function(){

		$("#valideteam"+$(this).attr("side")).attr("teamId",$(this).val())
	});


	
	$("[id^='valideteam']").click(function(){	
	
		let clicked = $(this);
		
		addLine(clicked,$(".form-group #action").length)
	
		
	});

    /**
     * ajout de ligne dans feuille de match
     * @param clicked
     * @param side
     */
	function addLine(clicked,side)
	{
		
		$.getJSON("/dropdownPlayer/"+clicked.attr('teamId')+"/"+side,
		{			
		},
		function(result)
		{
			
			result = JSON.parse(result);
			
			clicked.parent().after(result.html);
				
		})			
	}

	
	$("#selectedPos li a").click(function(){

	    let label = $('#dLabel');

        label.html($(this).text() + ' <span class="caret"></span>');
        label.val($(this).data('value'));
		$('#btn_addplayer').attr('posId',$(this).attr('posId'));
		
		$.post("/getposstat/"+$(this).attr('posId'),
		{			
		},
		function(result)
		{
			$("#pos_table").remove();
			$("#teamdrop").after(result);
		})		
	  
	});
	
	$("#btn_addplayer").click(function(){
			
		$.getJSON("/add_player/"+$(this).attr('posId')+"/"+$(this).attr('teamId'),
			{			
			},
			function(result)
			{
				result = JSON.parse(result);

                let totalltv = $("#totalPV");
                let res = $("#res");
                let modalfooter = $(".modal-footer");

				switch(result.reponse)
				{
					case "ok":


					
						$("#teamsheet").append(result.html);
						
						$("#caseTv").text(result.tv);
						$("#pTv").text(result.ptv);
                        totalltv.text(Number(totalltv.text()) + result.playercost);
						$("#tresor").text(result.tresor);
						
							$("[id^='remove_pl']").click(function(){
								
								let test = $(this).parent().parent();
							
								$.post("/remPlayer/"+$(this).attr("playerId"),
								{
								},
								function(result)
								{
									result = JSON.parse(result);
									
									console.log(result);
									
									switch(result.reponse)
									{
										case "rm":
											test.remove();
										break;
										
										case "sld":
											test.addClass("info hidden");
										break;
									}
									
									$("#caseTv").text(result.tv);
									$("#pTv").text(result.ptv);
                                    totalltv.text(Number(totalltv.text()) - result.playercost);
									$("#tresor").text(result.tresor)

								})	
							});
						
					break;
					
					case "pl":
						
						res.remove();
                        modalfooter.append("<div id=\"res\">" + result.html + "</div>");
					
					break;
					
					case "ar":
						
						res.remove();
                        modalfooter.append("<div id=\"res\">" + result.html + "</div>");
						
					break;
				}
				
			});
			
	});
	
	
	$("#addteam").click(function () {

	    let label = $("#dLabel");

		$.post("/createTeam/"+$('#nTeamName').val()+"/"+label.attr('coachId')+"/"+label.attr('raceId'),
		{			
		},
		function(result)
		{
			 window.location.href = "team/"+result+"/n"
		})

	});
	
	$("[id^='remove_pl']").click(function(){
		
		let test = $(this).parent().parent();
		let totalPV = $("#totalPV");
	
		$.post("/remPlayer/"+$(this).attr("playerId"),
		{
		},
		function(result)
		{
			result = JSON.parse(result);
			
			switch(result.reponse)
			{
				case "rm":
					test.remove();
				break;
				
				case "sld":
					test.addClass("info hidden");
				break;
			}
			
			$("#caseTv").text(result.tv);
			$("#pTv").text(result.ptv);
            totalPV.text(Number(totalPV.text()) - result.playercost);
			$("#tresor").text(result.tresor);

		});
		
	});
	
	$("[id^='add_']").click(function(){

		$.post("/add_inducement/" + $(this).attr("teamId") + '/' +$(this).attr("type"),
		{			
		},
		function(result)
		{
			result = JSON.parse(result);
            $("#"+result.type).text(result.nbr);
			
			$("#t"+result.type).text(result.inducost*result.nbr);
			$("#caseTv").text(result.tv);
			$("#pTv").text(result.ptv);
			$("#tresor").text(result.tresor)
		});
		
	});
	
	$("[id^='rem_']").click(function(){

		$.post("/rem_inducement/" + $(this).attr("teamId") + '/' +$(this).attr("type"),
		{			
		},
		function(result)
		{
			result = JSON.parse(result);
			
			$("#"+result.type).text(result.nbr);
			
			$("#t"+result.type).text(result.inducost*result.nbr);
			$("#caseTv").text(result.tv);
			$("#pTv").text(result.ptv);
			$("#tresor").text(result.tresor);
		});
		
	});
	
	$("[id^='retire_']").click(function(){
		
		let clicked = $(this).parent().parent();
		
		$.post("/ret_team/" + $(this).attr("teamId"),
		{			
		},
		function()
		{
			clicked.addClass("danger hidden")
		});
	});
	
	$("#recMatch").click(function(){
	
		
		//console.log(JSON.stringify($("#formMatch").serializeToJSON()))
		
		$.post("/addGame",
				
			JSON.stringify($("#formMatch").serializeToJSON())
		
		,
		function(result)
		{
			// console.log(result)
		},"json");
		
	});
	
	$("#comp").click(function(){

        $.post("/addComp/"+$('#skill option:selected').val()+"/"+ $(this).attr('playerid'),
        {
        },
        function()
        {
                window.location.reload();
        })

	});

    $("[id^='number_']").click(function(){

        let id = $(this).attr('id').substring($(this).attr('id').indexOf('_')+1,$(this).attr('id').length);

        $(this).replaceWith('<input type="text" id="inp_'+id+'" placeholder="'+$(this).text()+'" playerid="'+$(this).attr('playerid')+'" value="'+$(this).text()+'" data-toggle="tooltip" title="Appuyez sur enter pour valider">').focus();

        $('#inp_'+id).keypress(function (e) {

            if (e.which == 13) {

                $('#inp_'+id).after($('#loadingmessage'));
                $('#loadingmessage').show();

                $.post("/changeNr/"+$(this).val()+"/"+ $(this).attr('playerid'),
                {
                },
                function(result)
                {
                    $('#inp_'+id).replaceWith('<div id="number_'+$('#inp_'+id).val()+'" playerid="'+$('#inp_'+id).attr('playerid')+'">'+$('#inp_'+id).val()+'</div>');

                    window.location.reload();

                });



            }

        });

    });

    $("[id^='name_']").click(function(){


        let id = $(this).attr('id').substring($(this).attr('id').indexOf('_')+1,$(this).attr('id').length);

        $(this).replaceWith('<input type="text" id="inp_name_'+id+'" placeholder="'+$(this).text()+'" playerid="'+$(this).attr('playerid')+'" value="'+$(this).text()+'" data-toggle="tooltip" title="Appuyez sur enter pour valider" >').focus();

        $('#inp_name_'+id).keypress(function (e) {


            if (e.which == 13) {

                $('#inp_name_'+id).after($('#loadingmessage'));
                $('#loadingmessage').show();

                $.post("/changeName/"+$(this).val()+"/"+ $(this).attr('playerid'),
                    {
                    },
                    function(result)
                    {

                        $('#inp_name_'+id).replaceWith('<div id="name_'+$('#inp_name_'+id).val()+'" playerid="'+$('#inp_name_'+id).attr('playerid')+'">'+$('#inp_name_'+id).val()+'</div>')
                        window.location.reload();

                    });

            }

        });

    });
		
});

