<div data-role="page" id="konten_menue">
  <div data-role="header" data-position="fixed">
    <a href="#konten_liste" data-role="button" data-icon="home" data-bind="text:i18n.general.back"></a>
    <h1>Konto: <span data-bind="text: konten().selectedKonto().kontonummer"></span></h1>
  </div><!-- /header -->
  <div data-role="content">
    <ul data-role="listview">
      <li><a href="#konto_monatssalden" data-bind="click: $root.konten().openMonatssalden">Monatssalden</a></li>
      <li><a href="#konto_buchungen" data-bind="click: $root.konten().openBuchungen">Buchungen anzeigen</a></li>
      <li><a href="#konto_bearbeiten" data-bind="click: $root.konten().openKontenBearbeiten">Konto bearbeiten</a></li>
    </ul>
  </div><!-- /content -->
  <div data-role="footer" data-position="fixed">
    <h4 data-bind="text:i18n.general.copy"></h4>
  </div><!-- /footer --> 
</div><!-- /page -->