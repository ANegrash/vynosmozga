$(document).ready(function() {
            	
    $('.count-people').click(function() {
        var people = $(this).data('players');
        $('input[name="players"]').val(people);
        $('.count-people').each(function() {
            $(this).removeClass('selected');
        });
        $(this).addClass('selected');
    });
                
    $('.btn-bar').click(function() {
        var barId = $(this).data('barid');
        $('input[name="barSelect"]').val(barId);
        $('.btn-bar').each(function() {
            $(this).removeClass('selected');
        });
        $(this).addClass('selected');
    });
    
    $('input[name=teamName], input[name=capName]').change(function () {
        var value = $(this).val();
        if (isNotEmpty(value)) {
            $(this).addClass('is-valid');
        } else {
            $(this).addClass('is-invalid');
        }
    });
    
    $('input[name=phone]').change(function () {
        var value = $(this).val();
        if (value.length == 16) {
            $(this).addClass('is-valid');
        } else {
            $(this).addClass('is-invalid');
        }
    });
            
    $('#reg-submit').click(function () {
        $(this).addClass('hidden');
        $('.alert-success').addClass('hidden');
        $('.alert-danger').addClass('hidden');
        $('#loading').removeClass('hidden');
		var gameBar = $('input[name="barSelect"]').val();
		var teamName = $('input[name="teamName"]').val();
		var players = $('input[name="players"]').val();
		var capName = $('input[name="capName"]').val();
		var phone = $('input[name="phone"]').val();
		var comment = $('textarea[name="comment"]').val();
		var policyAgree = $('input[name="policyAgree"]').is(':checked');
		var gameNum = $(this).data("game");
		if (policyAgree && isNotEmpty(teamName) && isNotEmpty(capName) && isNotEmpty(phone) && phone.length == 16 && gameBar != 0 && players != 0){
		    $.ajax({
                url: 'https://vynosmozga.ru/ajax/registration.php',
                method: 'post',
                dataType: 'html',
                data: {gameNum: gameNum, bar: gameBar, team: teamName, players: players, cap: capName, phone: phone, comment: comment},
                success: function(data){
                    var returnedData = JSON.parse(data);
                    if (returnedData.code == 200) {
                        $('.alert-success-text').html(returnedData.text);
                        $('.alert-success').removeClass('hidden');
                    } else {
                        var alertError = $('.alert-danger');
                        var alertErrorText = $('.alert-danger-text');
                        alertErrorText.html(returnedData.text);
                        alertError.removeClass('hidden')
                    }
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $("#form").offset().top
                    }, 200);
                }
            });
		} else {
            var alertError = $('.alert-danger');
            var alertErrorText = $('.alert-danger-text');
            var error = '';
            var fieldsName = '';
            var fieldsWrong = 0;
            if (gameBar == 0) {
                fieldsName += '<li>Бар игры</li>';
                fieldsWrong++;
                $('#validation_error_barSelect').removeClass('hidden');
            }
            if (!isNotEmpty(teamName)) {
                fieldsName += '<li>Название команды</li>';
                fieldsWrong++;
                $('#validation_error_teamName').removeClass('hidden');
            }
            if (players == 0) {
                fieldsName += '<li>Количество игроков</li>';
                fieldsWrong++;
                $('#validation_error_players').removeClass('hidden');
            }
            if (!isNotEmpty(capName)) {
                fieldsName += '<li>Имя капитана</li>';
                fieldsWrong++;
                $('#validation_error_capName').removeClass('hidden');
            }
            if (phone.length !== 16) {
                fieldsName += '<li>Номер телефона</li>';
                fieldsWrong++;
                $('#validation_error_phone').removeClass('hidden');
            }
            if (!policyAgree) {
                fieldsName += '<li>Согласие на обработку персональных данных</li>';
                fieldsWrong++;
                $('#validation_error_policyAgree').removeClass('hidden');
            }
            
            if (fieldsWrong == 1) {
                error = 'Неверно заполнено поле:<br><ul class="mb-0">' + fieldsName + '</ul>';
            } else {
                error = 'Неверно заполнены поля:<br><ul class="mb-0">' + fieldsName + '</ul>';
            }
            alertErrorText.html(error);
            alertError.removeClass('hidden')
            $([document.documentElement, document.body]).animate({
                scrollTop: $("#form").offset().top
            }, 200);
		}
		$(this).removeClass('hidden');
        $('#loading').addClass('hidden');
	});
            
	
	function isNotEmpty(str) {
	    return str.trim().length > 0;
	}
	
    function setCursorPosition(pos, elem) {
        elem.focus();
        if (elem.setSelectionRange) elem.setSelectionRange(pos, pos);
        else if (elem.createTextRange) {
            var range = elem.createTextRange();
            range.collapse(true);
            range.moveEnd("character", pos);
            range.moveStart("character", pos);
            range.select()
        }
    }

    function mask(event) {
        var matrix = "+7(9__)___-__-__",
        i = 0,
        def = matrix.replace(/\D/g, ""),
        val = this.value.replace(/\D/g, "");
        if (def.length >= val.length) val = def;
        this.value = matrix.replace(/./g, function(a) {
            return /[_\d]/.test(a) && i < val.length ? val.charAt(i++) : i >= val.length ? "" : a
        });
        if (event.type == "blur") {
            if (this.value.length == 2) this.value = ""
        } else setCursorPosition(this.value.length, this)
    };

    var input = document.querySelector("#phone");
    input.addEventListener("input", mask, false);
    input.addEventListener("focus", mask, false);
    input.addEventListener("blur", mask, false);

});