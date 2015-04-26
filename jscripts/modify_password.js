// Wysłanie formularza o odzyskanie hasła
$(document).delegate("#form_forgotten_password", "submit", function (e) {
    e.preventDefault();
    loader.show();
    $.ajax({
        type: "POST",
        url: "jsonhttp.php",
        data: $(this).serialize() + "&action=forgotten_password",
        complete: function () {
            loader.hide();
        },
        success: function (content) {
            $(".form_warning").remove(); // Usuniecie komuniaktow o blednym wypelnieniu formualarza

            if (!(jsonObj = json_parse(content)))
                return;

            // Wyświetlenie błędów w formularzu
            if (jsonObj.return_id == "warnings") {
                $.each(jsonObj.warnings, function (name, text) {
                    var id = $("#form_forgotten_password [name=\"" + name + "\"]");
                    id.parent("td").append(text);
                    id.effect("highlight", 1000);
                });
            }
            else if (jsonObj.return_id == "sent") {
                // Wyświetl informacje o wysłaniu maila
                getnset_template($("#content"), "forgotten_password_sent", false, {username: jsonObj.username});
            }
            else if (!jsonObj.return_id) {
                show_info(lang['sth_went_wrong'], false);
            }

            // Wyświetlenie zwróconego info
            show_info(jsonObj.text, jsonObj.positive);
        },
        error: function (error) {
            show_info("Wystąpił błąd podczas wysyłania formualarza o odzyskanie hasła.", false);
        }
    });
});

//Wysłanie formularza o reset hasła
$(document).delegate("#form_reset_password", "submit", function (e) {
    e.preventDefault();
    loader.show();
    $.ajax({
        type: "POST",
        url: "jsonhttp.php",
        data: $(this).serialize() + "&action=reset_password",
        complete: function () {
            loader.hide();
        },
        success: function (content) {
            $(".form_warning").remove(); // Usuniecie komuniaktow o blednym wypelnieniu formualarza

            if (!(jsonObj = json_parse(content)))
                return;

            // Wyświetlenie błędów w formularzu
            if (jsonObj.return_id == "warnings") {
                $.each(jsonObj.warnings, function (name, text) {
                    var id = $("#form_reset_password [name=\"" + name + "\"]");
                    id.parent("td").append(text);
                    id.effect("highlight", 1000);
                });
            }
            else if (jsonObj.return_id == "password_changed") {
                // Wyświetl informacje o zmianie hasła
                getnset_template($("#content"), "reset_password_changed", false);
            }
            else if (!jsonObj.return_id) {
                show_info(lang['sth_went_wrong'], false);
            }

            // Wyświetlenie zwróconego info
            show_info(jsonObj.text, jsonObj.positive);
        },
        error: function (error) {
            show_info("Wystąpił błąd podczas wysyłania formualarza resetu hasła.", false);
        }
    });
});

//Wysłanie formularza o zmianę hasła
$(document).delegate("#form_change_password", "submit", function (e) {
    e.preventDefault();
    loader.show();
    $.ajax({
        type: "POST",
        url: "jsonhttp.php",
        data: $(this).serialize() + "&action=change_password",
        complete: function () {
            loader.hide();
        },
        success: function (content) {
            $(".form_warning").remove(); // Usuniecie komuniaktow o blednym wypelnieniu formualarza

            if (!(jsonObj = json_parse(content)))
                return;

            // Wyświetlenie błędów w formularzu
            if (jsonObj.return_id == "warnings") {
                $.each(jsonObj.warnings, function (name, text) {
                    var id = $("#form_change_password [name=\"" + name + "\"]");
                    id.parent("td").append(text);
                    id.effect("highlight", 1000);
                });
            }
            else if (jsonObj.return_id == "password_changed") {
                // Wyświetl informacje o zmianie hasła
                getnset_template($("#content"), "reset_password_changed", false);
            }
            else if (!jsonObj.return_id) {
                show_info(lang['sth_went_wrong'], false);
            }

            // Wyświetlenie zwróconego info
            show_info(jsonObj.text, jsonObj.positive);
        },
        error: function (error) {
            show_info("Wystąpił błąd podczas wysyłania formualarza zmiany hasła.", false);
        }
    });
});