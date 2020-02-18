import Routing from "./router.min";


var listePosition = '';
var cibleComp = '';

function replaceWithInput(selector) {
    $(selector).replaceWith('<input type="text" id="' + $(selector).attr('id') + '_text" placeholder="' + $(selector).text() + '" value="' + $(selector).text() + '" data-toggle="tooltip" title="Appuyez sur enter pour valider">').focus();

    $('#' + $(selector).attr('id') + '_text').keydown(function (e) {
        if (e.which == 13) {
            $('#' + $(selector).attr('id') + '_text').replaceWith('<span id="' + $(selector).attr('id') + '" class="' + $(selector).attr('class') + '" onclick="replaceWithInput(this)">' + $('#' + $(selector).attr('id') + '_text').val() + '</span>');
            if ($(selector).attr('id') == 'tresorAuto') {
                $('#tresor').html($('#' + $(selector).attr('id')).html());
                $('#tresor2').html($('#' + $(selector).attr('id')).html());
            }
        }
    });
}

window.replaceWithInput = replaceWithInput;

function choisirRace(selector) {
    $.post(
        Routing.generate('listePosition', {raceId: $(selector).val()}),
        {},
        function (data) {
            data = $.parseJSON(data);
            reset();
            $('#selectPosition').append(new Option('Choisir une position', 999));
            data.forEach(function (value) {
                var o = new Option(value.pos, value.posId);
                $(o).html(value.pos);
                $("#selectPosition").append(o);
                $('#coutrr').html(value.fRace.costRr);
            });
            listePosition = data;
        }
    );
}

window.choisirRace = choisirRace;

function positionStat(selector) {
    $('#pos_table').remove();
    $.post(Routing.generate('getposstat', {posId: $(selector).val()}),
        {},
        function (data) {
            $('#selectPosition').after(data);
            $('#btnAjoutJoueur').attr('onclick', 'ajoutJoueur(' + $(selector).val() + ');')
        });
}

window.positionStat = positionStat;

function ajoutJoueur(id) {
    listePosition.forEach(function (value) {
        if (value.posId == id) {
            if (checkIfPossible(value.cost) && nbrDuPositionel(id) <= value.qty) {
                $.post(
                    Routing.generate('nombreVersComp', {positionId: value.posId}),
                    {},
                    function (listeComp) {
                        listeComp = listeComp.substr(0, listeComp.length - 2);
                        $('#teamsheet').append('<tr><td><span onclick="supprimerJoueur(this)"  class="fas fa-times text-danger"></span></td><td><span onclick="replaceWithInput(this)">' + $('#teamsheet tr').length + '</span></td><td><span onclick="replaceWithInput(this)">Inconnu</span></td><td class="positionJoueur" pos="' + id + '">' + value.pos + '</td>' +
                            '<td>' + value.ma + '</td><td>' + value.st + '</td><td>' + value.ag + '</td><td>' + value.av + '</td><td>' + listeComp + ' <span class="fas fa-plus-square text-success" onclick="ajoutComp(\'' + value.norm + '\', \'' + value.doub + ' \', this)" ></span> </td><td class="text-right coutJoueur">' + value.cost + '</td></tr>');
                        addToTotal(value.cost, true);
                    }
                );
            }
        }
    });
}

window.ajoutJoueur = ajoutJoueur;

function supprimerJoueur(selector) {
    substrToTotal(parseInt($(selector).parents().children('.coutJoueur').html()), true);
    $(selector).parent().parent().remove();
}

window.supprimerJoueur = supprimerJoueur;

function ajoutIndu(type) {
    let cout = parseInt($('#cout' + type).html());
    let nbr = parseInt($('#' + type).html());
    if (checkIfPossible(cout)) {
        if (type == 'apo' && nbr < 1) {
            $('#' + type).html(nbr + 1);
            addToTotal(cout);
            addToValue($('#total' + type), cout);
        } else if (type != 'apo') {
            $('#' + type).html(nbr + 1);
            addToTotal(cout);
            addToValue($('#total' + type), cout);
        }
    }
}

window.ajoutIndu = ajoutIndu;

function supprIndu(type) {
    let cout = parseInt($('#cout' + type).html());
    let nbr = parseInt($('#' + type).html());
    if (nbr > 0) {
        $('#' + type).html(nbr - 1);
        substrToTotal(cout);
        SubstoValue($('#total' + type), cout);
    }
}

window.supprIndu = supprIndu;

function ajoutComp(norm, doub, selector) {
    cibleComp = selector;
    $.post(Routing.generate('classeLesComp', {norm: doub, doub: norm}),
        {},
        function (data) {
            data = $.parseJSON(data);
            $('#selectCompSimple').children('option').remove();
            $('#selectCompDouble').children('option').remove();
            $('#selectCompSimple').append(new Option('Choisir une comp. simple', 999));
            $('#selectCompDouble').append(new Option('Choisir une comp. double', 999));
            data['norm'].forEach(function (value) {
                var o = new Option(value.name, value.SkillId);
                $(o).html(value.name);
                $("#selectCompSimple").append(o);
            });
            data['double'].forEach(function (value) {
                var o = new Option(value.name, value.SkillId);
                $(o).html(value.name);
                $("#selectCompDouble").append(o);
            });
            $('#btnAjoutSimple').attr('onclick', 'AjoutCompSimple()');
            $('#btnAjoutDouble').attr('onclick', 'AjoutCompDouble()');
            $('#ajoutCompModal').modal('show');

            $('#btnFermeAjoutComp').click(function(){
                $('#ajoutCompModal').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });
        });
}

window.ajoutComp = ajoutComp;

function AjoutCompSimple() {
    if ( checkIfPossible(20000)) {
        $(cibleComp).before('<span class="text-success" onclick="supprCompSimple(this)">+' + $('#selectCompSimple option:selected').text() + '</span> ');
        $(cibleComp).parents().children('.coutJoueur').html(parseInt($(cibleComp).parents().children('.coutJoueur').html()) + 20000);
        addToTotal(20000, true);
    }
}

window.AjoutCompSimple = AjoutCompSimple;

function supprCompSimple(selector) {
    $(selector).remove();
    $(cibleComp).parents().children('.coutJoueur').html(parseInt($(cibleComp).parents().children('.coutJoueur').html())-20000);
    substrToTotal(20000)
}

window.supprCompSimple = supprCompSimple;

function AjoutCompDouble() {
    if ( checkIfPossible(30000)){
        $(cibleComp).before('<span class="text-danger" onclick="supprCompDouble(this)">+' + $('#selectCompDouble option:selected').text() + '</span> ');
        $(cibleComp).parents().children('.coutJoueur').html(parseInt($(cibleComp).parents().children('.coutJoueur').html())+30000);
        addToTotal(30000, true);
    }
}

window.AjoutCompDouble = AjoutCompDouble;

function supprCompDouble(selector) {
    $(selector).remove();
    $(cibleComp).parents().children('.coutJoueur').html(parseInt($(cibleComp).parents().children('.coutJoueur').html())-30000);
    substrToTotal(30000)
}

window.supprCompDouble = supprCompDouble;

function exportToPdf() {
    let doc = new jsPDF();

    doc.fromHTML($('#content').html(), 15, 15, {
        'width': 170,
        'elementHandlers': specialElementHandlers
    });
    doc.save('sample-file.pdf');
}

window.exportToPdf = exportToPdf;

function addToValue(target, value) {
    $(target).html(parseInt($(target).html()) + value);
}

function SubstoValue(target, value) {
    $(target).html(parseInt($(target).html()) - value);
    if (parseInt($(target).html()) < 0) {
        $(target.html(0));
    }
}

function checkIfPossible(value) {
    if (parseInt(value) <= parseInt($('#tresor').html())) {
        return true;
    }
    return false;
}

function nbrDuPositionel(posId) {
    let nbr = 1;
    $.each($('.positionJoueur'), function (index, val) {
        if ($(val).attr('pos') == posId) {
            nbr++;
        }
    });

    return nbr;
}

function addToTotal(value, joueur = null) {
    if (joueur != null) {
        addToValue($('#totaljoueur'), value);
    }
    addToValue($('#depenses'), value);
    addToValue($('#total'), value);
    addToValue($('#tv'), value / 1000);
    SubstoValue($('#tresor'), value);
    SubstoValue($('#tresor2'), value);
}

function substrToTotal(value, joueur = null) {
    if (joueur != null) {
        SubstoValue($('#totaljoueur'), value);
    }
    SubstoValue($('#depenses'), value);
    SubstoValue($('#total'), value);
    SubstoValue($('#tv'), value / 1000);
    addToValue($('#tresor'), value);
    addToValue($('#tresor2'), value);
}

function reset() {
    $('#teamsheet').children('tbody').empty();
    $('#selectPosition').children('option').remove();
    $('#totaljoueur').html(0);
    $('#depenses').html(0);
    $('#total').html(0);
    $('#tv').html(0);
    $('#tresor').html($('#tresorAuto').html());
    $('#tresor2').html($('#tresorAuto').html());
}