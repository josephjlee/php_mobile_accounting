<?php 
/*
 * Copyright (c) 2013 by Wolfgang Wiedermann
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; version 3 of the
 * License.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 */
?>
<?php define("MAIN_PAGE", 1); ?>
<!DOCTYPE html>
<html lang="de" manifest="manifest.php">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/jquery.mobile-1.3.1.css" />
    <link rel="stylesheet" href="./css/hhb.css" />
    <!-- Standard-Bibliotheken -->
    <script src="./js/jquery-2.0.2.min.js"></script>
    <script src="./js/jquery.mobile-1.3.1.js"></script>
    <script src="./js/knockout-2.2.1.js"></script>
    <!-- App-spezifische Code-Dateien -->
    <script src="./js/knockout-ext.js"></script>
    <script src="./js/helper.js"></script>
    <script src="./js/model.js"></script>
    <script src="./js/diagramm.js"></script>
    <script src="./js/update_app.js"></script>
    <script src="./js/util.js"></script>
</head>
<body>
<div data-role="page" id="main_menu" class="page">
    <div data-role="header" data-position="fixed" data-theme="b" class="clickable_header">
        <a href="#" id="header_home_button" data-icon="home">Men&uuml;</a>
	<h1 id="header_text">Buchhaltung</h1>
        <a href="#" id="header_new_button" data-icon="new">Neu</a>
    </div>
    <div data-role="content">
        <?php include("./parts/navigation.php"); ?>
        <?php include("./parts/account_form.php"); ?>
        <?php include("./parts/buchung_form.php"); ?>
        <?php include("./parts/ergebnis_form.php"); ?>
        <?php include("./parts/admin_form.php"); ?>
    </div>
    <div data-role="footer" data-position="fixed" data-theme="b"><center>&copy; 2014 by Wolfgang Wiedermann</center></div>
</div>
<script type="text/javascript">
// Updates sichtbar machen und sofort anwenden
de.ww.updater.handlers.onUpdateReady(function() { alert('Die Buchhaltung wurde auf eine neue Version aktualisiert'); });

// Model initialisieren
var model = new AppViewModel();

/*
* Konstruktor-Funktion, initialisiert nach dem Laden der Seite
* die Ansicht und registriert die Event-Handler der Haupt-View
*/
$(document).ready(function() {
    ko.applyBindings(model);
    gotoMainPage();
    $("#header_home_button").click(gotoMainPage);
    $("#menu_konten").click(gotoKonten);
    $("#menu_buchen").click(gotoBuchen);
    $("#menu_auswerten").click(gotoAuswerten);
    $("#menu_admin_quick").click(gotoQuickAdmin);
    $("#menu_schnellbuchungen_divider").click(menu.loadQuickMenuItems);
});

/*
* Handler fuer das Klick-Event des Home-Buttons
*/
function gotoMainPage() {
    $("#header_text").text("Buchhaltung");
    $(".content_form").hide();
    $("#navigation").show();
    $("#header_new_button").hide();
    $("#header_home_button").hide();
}

/*
* Handler fuer das Klick-Event des Konten-Buttons
*/
function gotoKonten() {
    $(".content_form").hide();
    $("#account_form").show();
    $("#header_new_button").show();
    $("#header_home_button").show();
    $("#header_new_button").unbind("click");
    $("#header_new_button").click(kontenForm.newKontoHandler);
    handlers.refreshKonten();
}

/*
* Handler fuer das Klick-Event des Buchen-Buttons
*/
function gotoBuchen() {
    $(".content_form").hide();
    $("#header_home_button").show();
    $("#buchung_form").show();
    handlers.refreshKonten();
    buchungenForm.registerBuchungFormEvents();
}

/*
* Handler fuer das Klick-Event des Auswerten-Buttons
*/
function gotoAuswerten() {
    $(".content_form").hide();
    $("#header_home_button").show();
    $("#ergebnis_form").show();
    ergebnisForm.registerErgebnisFormEvents();
}

/*
* Handler fuer das ...
*/
function gotoQuickAdmin() {
    $(".content_form").hide();
    $("#header_home_button").show();
    $("#admin_quick_select_view").show();

// in diesem Fall erstmal unnötig, da die Events für Quick 
// via knockout.js im Model verbaut sind
//    adminForm.registerQuickAdminFormEvents();
}
</script>
</body>
</html>
