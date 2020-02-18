import Routing from "./router.min";

var listePosition = '';

function replaceWithInput(selector) {
    $(selector).replaceWith('<input type="text" id="' + $(selector).attr('id') + '_text" placeholder="' + $(selector).text() + '" value="' + $(selector).text() + '" data-toggle="tooltip" title="Appuyez sur enter pour valider">').focus();

    $('#' + $(selector).attr('id') + '_text').keydown(function (e) {
        if (e.which == 13) {
            $('#' + $(selector).attr('id') + '_text').replaceWith('<span id="' + $(selector).attr('id') + '" class="' + $(selector).attr('class') + '" onclick="replaceWithInput(this)">' + $('#' + $(selector).attr('id') + '_text').val() + '</span>');
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
    listePosition.forEach(function (value, index) {
        if (value.posId == id) {
            if (checkIfPossible(value.cost) && nbrDuPositionel(id) <= value.qty) {
                $.post(
                    Routing.generate('nombreVersComp', {positionId: value.posId}),
                    {},
                    function (listeComp) {
                        listeComp = listeComp.substr(0, listeComp.length - 2);
                        $('#teamsheet').append('<tr><td><span onclick="supprimerJoueur(this)">X</span></td><td><span onclick="replaceWithInput(this)">' + $('#teamsheet tr').length + '</span></td><td><span onclick="replaceWithInput(this)">Inconnu</span></td><td class="positionJoueur" pos="' + id + '">' + value.pos + '</td>' +
                            '<td>' + value.ma + '</td><td>' + value.st + '</td><td>' + value.ag + '</td><td>' + value.av + '</td><td>' + listeComp + '</td><td class="text-right coutJoueur">' + value.cost + '</td></tr>');

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
    $('#tresor').html(1000000);
    $('#tresor2').html(1000000);
}