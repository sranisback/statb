
let $ = require('jquery');

require('bootstrap-sass');

import './jquery-ui.js'
import './jquery.nameBadges.js'
import './jquery.tablesorter.js'
import './jquery.paginate.js'
import './jquery.serializeToJSON.js'

$(document).ready(function() {
//	$('.name').nameBadge({size:60,border:{width:0}});
	
/*	$("#class_gen").tablesorter({
		headers: {0: { sorter: false}}
	});*/


	$("span[att^='test']").click(function() {
		
		let clicked = $(this);
	
		$.post("/testajax",
		{
		},
		function(result)
		{
			if(result !== 0)
			{
				
				clicked.after("<h1>test</h1>")
					
			}
			else
			{
				
			}		
		
		})
	
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

