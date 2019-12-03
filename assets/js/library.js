import Routing from "./router.min";

function changeNr(caseNumero) {
    var caseNumero  = $(caseNumero);
    let id = caseNumero.attr('id').substring(caseNumero.attr('id').indexOf('_') + 1, caseNumero.attr('id').length);

    caseNumero.replaceWith('<input type="text" id="inp_' + id + '" placeholder="' + caseNumero.text() + '" playerid="' + caseNumero.attr('playerid') + '" value="' + caseNumero.text() + '" data-toggle="tooltip" title="Appuyez sur enter pour valider">').focus();

    $('#inp_' + id).keypress(function (e) {
        if (e.which == 13) {
            $('#inp_' + id).after($('#loadingmessage'));
            $('#loadingmessage').show();
            $.post(Routing.generate('changeNr', {newnr: $(this).val(), playerid: $(this).attr('playerid')}),
                {},
                function () {
                    $('#inp_' + id).replaceWith('<div id="number_' + $('#inp_' + id).val() + '"  playerid="' + $('#inp_' + id).attr('playerid') + '">' + $('#inp_' + id).val() + '</div>');

                    window.location.reload();
                });
        }
    });
}

window.changeNr = changeNr;