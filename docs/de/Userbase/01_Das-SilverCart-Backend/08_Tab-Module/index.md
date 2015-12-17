# Tab Module

## Preisportal Exporte

Preisportale sind ein wichtiger Faktor beim Marketing Ihres Webshops. Zwar sollten Sie nicht jeden Preiskampf mitmachen, aber gezielte Angebote und die erhöhte Sichtbarkeit bei der Listung in einem Preisportal können Ihnen deutliche Umsatzzuwächse bescheren. 

Es gibt viele Preisportale und regelmässig kommen neue hinzu. Dabei erwartet jedes Portal, dass die Artikelliste in einer besonderen Form aufbereitet ist. In der Regel unterscheiden sich die Feldnamen oder auch die Feldinformationen selbst.

Deshalb haben Sie mit SilverCart die Möglichkeit, verschiedene Exportformate zu pflegen. Somit können Sie für jedes Preisportal eine eigene Exportdatei definieren und sogar einstellen, dass diese regelmässig erneuert wird. Durch Abverkäufe oder Preisänderungen ändert sich Ihr Sortiment dauernd und Sie wollen vermeiden, dass veraltete Informationen an ein Preisportal übermittelt werden.


### Preisportal-Exporte Übersicht

In der Übersicht finden Sie alle bereits angelegten Exporte für Preisportale. Wenn Sie ein sehr engagierter Webshop-Betreiber sind und Sie ihr Sortiment auf vielen Preisportalen anbieten, dann können Sie in der Übersicht die angezeigten Exporte filtern.

Hierfür haben Sie die folgenden Filtermöglichkeiten:

* Aktualisierungsintervall (Periode)
* Push aktivieren

### Preisportal-Export Detailansicht

Sie gelangen in die Detailansicht eines Preisportal-Exports, wenn Sie einen einzelnen Eintrag in der Übersicht auswählen (anklicken).


#### Grundeinstellungen

Ist aktiviert
Mit dieser Checkbox wird dieser Export aktiviert oder deaktiviert.


Name
Sie können dem Export einen beliebigen Namen geben. Der Name des Preisportals, für das dieser Export bestimmt ist bietet sich an.

CSV Trennzeichen
Die Artikelliste wird als CSV-Datei erzeugt. Es gibt unterschiedliche Möglichkeiten, die einzelnen Felder eines Artikels voneinander zu trennen. Normalerweise wird hier ein Komma verwendet. Daher kommt auch der Name „Comma Separated Values“ (CSV). In vielen Fällen wird hier aber auch ein Semikolon oder das Pipe-Symbol „|“ gefordert. Sie erfahren das benötigte Trennzeichen von Ihrem Preisportalbetreiber.

Trennzeichen für Breadcrumbs

Wird für die Trennung der Einzelkomponenten aller Breadcrumb Felder verwendet

Protokoll für Links

Basis-URL für Links

Land

Kontext-Land für Angaben wie Versandkosten

Timestamp Datei erzeugen

Aktualisierungsrhythmus

Aktualisierungszeitraum

Push aktivieren

Pushen an URL

Letzter Export: ---
Gibt an, wann der automatische Export zuletzt erfolgt ist. Wurde noch kein Export durchgeführt, sehen Sie hier die ---

URL:
http://handbuch.trysilvercart.com/silvercart/product_exports/Test-Export.csv

#### Artikelauswahl

Sie können die Auswahl der in diesem Export enthalten Artikel eingrenzen. Es gibt gute Gründe, weshalb Sie nicht Ihr vollständiges Sortiment anbieten möchten, z.B. Artikel mit einem niedrigen Warenbestand.

Folgende Filterkriterien können kombiniert werden:

* nur Artikel einer bestimmen Warengruppe
* nur Artikel mit Artikelbild
* nur Artikel eines bestimmten Herstellers
* nur Artikel mit einem Mindestbestand
* nur Artikel einer bestimmten Spiegel-Warengruppe

#### CSV-Felddefinitionen

Über diese Maske wählen Sie die zum Export bestimmten Felder und die Reihenfolge aus.

![backend_artikel_preisportal_exportfelder.png](backend_artikel_preisportal_exportfelder.png)
Bitte vergessen Sie nicht, die zugewiesenen Felder auch zu speichern.

Callback-Feld

Über ein Callback-Feld kann Programmcode automatisiert beim Export ausgeführt werden. Dadurch lässt sich der Export individuell erweitern und es lassen sich auch komplexe Exportfelder erzeugen bzw. spezielle Werte errechnen. 

Hierfür sind fortgeschrittene Programmierkenntnisse notwendig, weshalb die Callback-Felder hier nicht näher beschrieben werden.





#### CSV-Kopfbereich

Hier können Sie die unter dem Punkt „CSV-Felddefinitionen“ ausgewählten Exportfelder mit eigenen Spaltennamen versehen. Diese erscheinen dann in der CSV-Datei in der ersten Zeile (Kopfzeile).

Es gibt Preisportale, die in der CSV-Datei eine festen Reihenfolge und eine  vorgegebene Spaltennamen in der Kopfzeile erwarten. Die notwendigen Angaben erfahren Sie beim Betreiber des Preisportals.

Speichern Sie ihre Arbeit, nachdem Sie die Spaltennamen für alle Exportfelder hinterlegt haben.

# Externe Warenkorbbefüllung

Die externe Warenkorbbefüllung wird auch im Zusammenhang mit Preisportalen benutzt. Ein Besucher eines Preisportals kann sich mehrere Artikel in einen Warenkorb auf der Preisportalseite legen. Das Portal ermittelt den günstigsten Anbieter für die Summe der Artikel. Will der Kunde nun diesen Warenkorb kaufen, kommt die externe Warenkorbbefüllung ins Spiel. Das Preisportal überträgt den Inhalt auf einen Warenkorb in Ihrem Shop. Der Kunde muss also nicht erneut alle Artikel in Ihrem Shop zusammensuchen.

Damit dieser Mechanismus funktioniert, müssen Sie ein Objekt "Externe Warenkorbbefüllung" erstellen und konfigurieren. Zusätzlich müssen Sie bestimmte Variablen definieren und mit Werten versehen. Welche Variablen und Werte das sind, erfahren Sie vom Betreiber des Preisportals das Sie einbinden möchten.

Bezeichnung	
Name des externen Warenkorbes; dient der Unterscheidung, falls mehrere externe Warenkörbe existieren

Kurzname des externen Partners
Name des Preisportals

Checkbox "shared secret aktivieren“
Das "shared secret" ist ein frei definierbares Passwort, dass natürlich auch beim Preisportal hinterlegt sein muss. Dieser Sicherheitsmechanismus ist nicht bei allen Preisportalen vorhanden und kann deshalb auch deaktiviert werden.

shared secred
das Passwort

Name der shared secret Variable
Das shared secret wird in einer Variable übertragen, deren Namen hier angegeben werden muss.

Übertragungsmethode
Abhängig vom Preisportal werden die Daten entweder als keyValue oder als combinedString übertragen.

Name der Variable, in der die Zeichenkette gespeichert ist
Wenn sie als Übertragungmethode combinedString gewählt haben, müssen sie hier den Name der Zeichenkette angeben, in der die Daten übertragen werden.

Entitätentrennzeichen der Zeichenkette
Zeichen das die Variablen voneinander trennt; nur notwendig wenn Übertragungsmethode combinedString gewählt wurde

Mengentrennzeichen in der Zeichenkette
Zeichen das die Werte voneinander trennt; nur notwendig wenn Übertragungsmethode combinedString gewählt wurde

Name der Artikelvariablen
Name der Variablen, die den Artikel identifiziert

Artikel-Bezugsfeld
Feld anhand dessen das Produkt bestimmt werden soll. (Zum Beispiel ID oder Artikelnummer)

# Google Taxonomie

SilverCart bietet mit "Google Taxonomie" eine Funktion, um Ihre Produkte in der Google Produktsuche zu kategorisieren. Hier können Sie die Kategorien Ihrer Waren festlegen und diese mit den Warengruppen Ihres Shops verknüpfen.