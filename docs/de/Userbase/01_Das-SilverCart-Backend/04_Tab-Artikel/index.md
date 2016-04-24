# Tab Artikel

Unter dem Tab Artikel haben Sie die Möglichkeit Ihr Webshop-Sortiment zu pflegen. 

Neben den Artikeln können Sie auch 
* Hersteller,
* Artikelzustände,
* Verfügbarkeiten und
* Steuersätze verwalten.

## Artikel

Die Verwaltung von Artikeln ist neben der Bestell- und Kundenverwaltung einer der wichtigsten Bereiche in SilverCart. Wenn Sie ein pflegeintensives Sortiment haben, also beispielsweise regelmässige Preisänderungen durchführen oder neue Artikel einpflegen, werden Sie diesen Bereich entsprechend häufig aufsuchen.

Hinweis:
Artikel und Produkt werden synonym verwendet und betriebswirtschaftliche Unterscheidungsdetails ausser acht gelassen.

### Artikelübersicht

In der Artikelübersicht werden alle Artikel Ihres SilverCart Webhops in einer Tabelle dargestellt.

![backend_artikel_uebersicht.png](_images/backend_artikel_uebersicht.png)

Über die Filter im Inhaltsbereich können Sie die angezeigten Artikel einschränken. Mit SilverCart sind Online-Shops mit mehr als 400.000 Artikeln möglich - ein mächtiger Filter erleichtert Ihnen dabei die tägliche Arbeit wesentlich.

** Artikelnummer **

Geben Sie hier die Artikelnummer oder einen Teil der Artikelnummer ein. Dabei werden alle Artikel angezeigt, bei denen die angegebenen Ziffern- bzw. Buchstabenfolge innerhalb der Bestellnummer vorkommt. Hier zahlt es sich aus, wenn Sie Ihr Sortiment durch sprechenden Artikelnummern strukturiert haben. Eine sprechende Artikelnummer kann anhand des Aufbaus schon Informationen über Warengruppe, Lieferant, Modelljahr, Material oder Farbe liefern.

** Name **

In dieses Feld tragen Sie den Namen (Artikelbezeichnung) ein. Es werden alle Artikel aufgeführt, deren Namen ganz oder teilweise mit dem Begriff übereinstimmen, den Sie hier eingetragen haben.

** Listenbeschreibung **

Wenn Sie die Artikel anhand der Listenbeschreibung (Kurzbeschreibung) filtern möchten, dann tragen Sie hier den gewünschten Suchbegriff ein.

** Artikelbeschreibung **

Bei der Suche nach Artikeln anhand der Artikelbeschreibung berücksichtigen Sie bitte, dass die Artikelbeschreibung oftmals in HTML erfolgt. Dadurch lässt sich die Artikelbeschreibung ansprechender darstellen, z.B. Durch Fettdruck oder Kursivschrift. Diese Auszeichnungen können dazu führen, dass  längere Phrasen oder Teilsätze nicht gefunden werden, da Sie in der Datenbank mit diesem zusätzlichen Markup versehen sind.

** Hersteller **

Über dieses Feld können Sie Ihr Sortiment nach den Artikeln eines bestimmten Herstellers Filtern.

** Artikelnummer (Hersteller) **

Wenn Sie die Herstellerartikelnummer verwenden, dann können Sie diesen Filter nutzen um Artikel anhand der Artikelnummer des Herstellers zu finden.

** ist aktiv **

Über diese Checkbox können Sie steuern, ob nur aktive oder inaktiv Artikel angezeigt werden sollen.

** Warengruppe **

Für die Einschränkung Suche auf eine bestimmte Warengruppe wählen Sie aus der Dropdown-Liste einfach die gewünschte Warengruppe aus.

** Spiegel-Warengruppen **

Mit der Auswahl einer Spiegel-Warengruppe finden Sie die Produkte, die der ausgewählten Spiegel-Warengruppe zugeordnet sind.

** Verfügbarkeit **

Für die Einschränkung nach verfügbaren oder nicht verfügbaren Produkten wählen Sie hier die gewünschte Einstellung aus.

** Spalten in Suchergebnissen **

Sie können die in der Übersicht angezeigten Produktinformationen ganz einfach selbst bestimmen. 

Die folgenden Felder stehen Ihnen zur Auswahl zur Verfügung:

* ID
* Währung (Netto)
* Name
* Gewicht
* Listenbeschreibung
* EAN
* Artikelbeschreibung
* ist aktiv 
* Meta Beschreibung für Suchmaschinen
* Min. Bezugsdauer
* Meta Titel für Suchmaschinen
* Max. Bezugsdauer
* Meta Schlagworte für Suchmaschinen
* Einheit (WBZ)
* Artikelnummer
* Lagerbestand
* Artikelnummer (Hersteller)
* Ist der Lagerbestand dieses Artikels überbuchbar?
* Einkaufspreis
* Silvercart Product Group ID
* Währung
* Einkaufspreis
* Silvercart Manufacturer ID
* UVP Silvercart
* Availability Status ID
* Währung
* UVP
* Warengruppe
* Preis (Brutto)
* Hersteller
* Währung (Brutto)
* Verfügbarkeit
* Preis (Netto)
* ist aktiv

Sie haben auch die Möglichkeit, über `Alle Spalten` oder `Keine Spalten` mit einem Klick alles aus- bzw. abzuwählen.

** Importieren **

Die SilverCart Shopsoftware bietet Ihnen die Möglichkeit, Ihre Produktdaten über eine CSV-Datei zu importieren. Welche Werte die Importfunktion erwartet, sehen Sie wenn Sie Spezifikation für SilvercartProduct zeigen wählen.

Mit der Checkbox `Clear Database before import` bestimmen Sie, ob alle Produkte vor dem Import gelöscht werden sollen. Wählen Sie diese Option nur, wenn Sie sich absolut sicher sind.

Zur Sicherheit sollten Sie vorher eine Datensicherung durchführen oder den Import auf einem Stagingsystem probeweise durchführen.

** Bilder nachträglich importieren **

Über diese Funktion können Sie Produktbilder nachträglich importieren. Die Zuordnung zu den Produkten erfolgt dabei automatisch. Dafür muss der Dateiname einer bestimmten Konvention entsprechen. 

Um den Import durchzuführen, müssen Sie die Bilder in einem Verzeichnis auf dem Server liegen auf dem auch Ihr SilverCart Webshop läuft. Den Pfad tragen Sie bitte in dem Format 
/var/www/silvercart/images/ 
ein.

Starten Sie den Import mit dem Button Bilder importieren.

### Artikeldetail

Sie gelangen in die Artikeldetailansicht, wenn Sie einen einzelnen Artikel in der Artikelübersicht auswählen (anklicken).

#### Hauptteil

![backend_artikel_hauptteil.png](_images/backend_artikel_hauptteil.png)

** Ist aktiv **

Über diese Checkbox steuern Sie, ob ein Artikel grundsätzlich im Front-End angezeigt werden soll oder nicht.

Bestimmte Regeln und Einstellungen (z.B. Lagerbestand) können jedoch dafür sorgen, dass auch aktive Artikel zumindest zeitweise nicht angezeigt werden.

** Artikelnummer **

Hier können Sie die Artikelnummer des Artikels in Ihrem Sortiment eintragen. Sie können Buchstaben, Zahlen und bestimmte Sonderzeichen wie `-`oder `_` verwenden.

** Artikelnummer (Hersteller) **

Wenn Ihnen die Artikelnummer des Herstellers bekannt ist und Sie diese im Shop pflegen möchten, dann können Sie dieses Feld dafür nutzen. In manchen Fällen helfen Sie damit Ihren Kunden einen Artikel eindeutig zu identifizieren.

** EAN **

Die EAN (European Article Number) ist eine europaweit eindeutige Artikelnummer und wird in der Regel als Barcode auf dem Artikel abgebildet. Wenn Ihnen die EAN Ihrer Artikel bekannt sind, dann empfiehlt sich auch die konsequente Pflege der EAN. Dadurch lassen sich Artikel beispielsweise bequem mit einem Handscanner erfassen.

** Name **

In diesem Feld pflegen Sie den Namen oder die Bezeichnung des Artikels. Bitte verwenden Sie hier kein HTML-Markup.

** Listenbeschreibung **
Die Listenbeschreibung wird oft auch als Kurzbeschreibung bezeichnet. Sie wird häufig bei der Darstellung von Produkten in einer Listenform (Übersicht der Produkte einer Warengruppe) und zusätzlich für die Meta-Information im Rahmen der Suchmaschinenoptimierung verwendet. Auch hier verwenden Sie bitte kein HTML-Markup.

** Artikelbeschreibung **

Die Artikelbeschreibung können Sie mit dem WYSIWYG-Editor beliebig aufwendig mit Kursiv- und Fettdruck, Listen und Überschriften gestalten.

#### Verfügbarkeit

In der Sektion `Verfügbarkeit` können Sie verschiedene Aspekte der Bestandsverwaltung eines Artikels pflegen.

** Verfügbarkeit **

Über die möglichen Werte der Dropdown-Liste `verfügbar` und `nicht verfügbar` können Sie die grundsätzliche Verfügbarkeit einstellen. 

** Min. Bezugsdauer, Max. Bezugsdauer und Einheit (WBZ) **

Wenn Sie einen Artikel bei Ihren Lieferanten nachbestellen, dann müssen Sie mit einer bestimmten Lieferzeit rechnen. Diese kann höchst unterschiedlich sein, wird sich meistens jedoch im Bereich von wenigen Tagen befinden. Manche Branchen rechnen hier jedoch in Stunden, andere in Wochen, Monaten oder sogar Jahren. 

Über die 3 Felder `Min. Bezugsdauer`, `Max. Bezugsdauer` und `Einheit (WBZ)` können Sie die übliche Lieferzeit für diesen Artikel hinterlegen.

Möchten Sie Ihren Kunden anzeigen, dass die Lieferzeit eines Artikels ca. 3-5 Tage beträgt, dann würden Sie die folgenden Werte eintragen:

`Min. Bezugsdauer = 3`
`Max. Bezugsdauer = 5`
`Einheit (WBZ) = Tage`

** Lagerbestand **

Diese Feld zeigt den aktuellen Lagerbestand - also die verfügbare Menge - des Artikels an. Sie können diesen Wert verändern, wenn Sie eine neue Lieferung erhalten.


** Ist überbuchbar? **

Wenn der Bestand aufgebraucht ist, kann der Artikel in Ihrem SilverCart Webshop nicht mehr bestellt werden.

Unter bestimmten Umständen möchten Sie einen Artikel aber vielleicht auch dann verkaufen, wenn kein Lagerbestand mehr vorhanden ist. Vielleicht befindet sich die Ware im Zulauf oder kann sehr kurzfristig beschafft werden.

Mit der Checkbox `Ist überbuchbar?` können Sie Artikel auch dann zum Verkauf freigeben, wenn eigentlich keine mehr vorhanden sind.


** Datum, ab welchem Lagerbestand nicht mehr überbuchbar ist **

Hiermit können Sie steuern, dass ein überbuchbarer Artikel nur bis zu einem bestimmten Datum überbuchbar ist. Dies betrifft beispielsweise einen Artikel, der Ihnen zwar innerhalb eines Tages geliefert werden kann, jedoch bei Ihrem Lieferant nur noch bis zu einem bestimmten Datum produziert wird.


### Die Bestandsverwaltung in SilverCart

Die Bestandsverwaltung von SilverCart ist sehr umfangreich und kann auch fortgeschrittene Anforderungen abdecken.

Unter `Einstellungen -> Allgemeine Konfiguration -> Lager ->  aktivieren` können Sie die Bestandsverwaltung global ein oder ausschalten.

Mit aktivierter Lagerbestandsverwaltung wird die Menge der jeweiligen Artikel beim Kauf entsprechend der gekauften Menge reduziert. Warenzugänge können direkt in der Artikelpflegemaske in das Textfeld eingetragen werden. Dabei wird stets der gesamte Bestand eingetragen. Um den Bestand korrekt zu erhöhen müssen also Bestand + Zugang addiert und eingetragen werden.

Sollen Artikel verkauft werden auch wenn kein Bestand vorhanden ist, dann aktivieren Sie die Checkbox „Ist der Lagerbestand generell überbuchbar“. Damit werden auch negative Bestandsmengen möglich, beispielsweise für Produkte die sich im Zulauf befinden oder generell sehr kurzfristig geliefert werden können.

Ist diese Option nicht aktiviert, dann können Produkte ohne Bestand nicht mehr verkauft werden. Für Einzelstücke und Restposten kann diese Einstellung direkt am Artikel verändert werden. Wird für einen Artikel eine abweichende Einstellung angegeben, dann hat diese Priorität vor der globalen Einstellung.  


Mit der Einstellung „Ist der Lagerbestand generell überbuchbar“ können Sie Restposten und Einzelstücke sicher verkaufen. Die Shopsoftware sorgt beim Checkout dafür, dass nicht mehr als der tatsächlich verfügbare Bestand verkauft werden kann. Sollten mehrere Kunden das selbe Einzelstück im Warenkorb haben, dann kommt der Kauf mit dem Kunden zustande, der am schnellsten die Bestellung abschliesst. Alle anderen Kunden bekommen die Meldung angezeigt dass der gewünschte Artikel zwischenzeitlich nicht mehr verfügbar ist.


Über die Liste „Verfügbarkeit“ wird lediglich die Anzeige der Verfügbarkeitsinformation bei der Artikeldetailansicht gesteuert. Der hier eingestellte Wert hat keinen Einfluss auf die Bestellbarkeit eines Artikels.

Ob ein Artikel aufgerufen und verkauft werden kann, wird einzig über die Felder „ist aktiv“ und - je nach eingestellter Lagerbestandsverwaltung - über den tatsächlichen Lagerbestand gesteuert.


### Sonstiges

** Hersteller **

Über dieses Dropdown-Feld können Sie dem Artikel einen Hersteller zuweisen. Der Hersteller muss zuvor unter `Artikel -> Hersteller` angelegt werden.

** Verkaufsmenge und Verkaufsmengeneinheit **

Die übliche Verkaufsmenge ist `1` bei einer Verkaufsmengeneinheit von `Stück`.

Sie können unter `Artikel -> Verkaufsmengeneinheiten` weitere Verkaufsmengeneinheiten wie Flasche, Kiste, Kartusche, Eimer, Fass, Rolle angeben. Damit können Sie im SilverCart Webshop auch aussergewöhnlichen Produktsortimente kundenfreundlich abbilden.


** Gewicht **

Angabe des Gewichts inkl. Umverpackung in Gramm (ganzzahlig). Das Gewicht kann bei der Berechnung der Versandkosten eine wichtige Rolle spielen.

** Artikelzustand **

Sie können unter `Artikel -> Artikelzustände` beliebige Artikelzustände wie `neu`, `gebraucht` oder `defekt` definieren. Der Artikelzustand sollte beim Verkauf auf eBay oder Amazon angegeben werden.

#### Preise

Im SilverCart-Webshop haben Sie die Möglichkeit für B2B- und B2C-Kunden jeweils getrennte Preise zu pflegen. Dies ist ein Vorteil, wenn Sie jedem Kunden gerundete („geschönte“) Verkaufspreise anbieten wollen, unabhängig davon ob es sich um einen Brutto- oder einen Nettopreis handelt. Hierfür haben Sie in SilverCart die Möglichkeit, an jedem Artikel einen Preis (Brutto) und einen Preis (Netto) zu hinterlegen.

Der Preis (Brutto) wird dabei mit dem Hinweis „inkl. MwSt“ den Kunden angezeigt, die als B2C-Kunden angelegt sind . Der Preis (Netto) wird analog hierzu mit dem Hinweis „exkl. MwSt“ den Kunden angezeigt, die als B2C-Kunden angelegt sind.

Im Warenkorb und in der Bestellübersicht wird dabei bei B2C-Kunden der Nettopreis aus dem Preis (Brutto) errechnet und bei B2B-Kunden der Bruttopreis aus dem Preis (Netto), falls die Preise gepflegt sind.

Ist nur einer der beiden Preise gepflegt, wird der zugehörige Netto- oder Bruttopreis anhand der am Artikel hinterlegten Mehrwertsteuer berechnet.

Wird keiner der beiden Preise hinterlegt, wird der Artikel aus Sicherheitsgründen in der Webshop-Storefront nicht angezeigt. Handelt es sich tatsächlich um einen kostenlosen Artikel, können Sie das durch anwählen der Option „kostenfreier Artikel“ umgehen.

* Cent-Beträge: Punkt oder Komma?*
Intern verwendet SilverCart ein Dezimalkomma. Da dies je nach Region aber ganz unterschiedlich gehandhabt wird, wandelt SilverCart einen Dezimalpunkt automatisch um. Es spielt also keine Rolle,
ob Sie die Cent-Beträge mit einem Punkt oder einem Komma abgrenzen. 

Preise

Preis (Brutto)
Preis (Netto)
UVP
Einkaufspreis
Steuersatz

#### SEO

SEO steht für `Search Engine Optimization`, auf deutsch bedeutet das `Suchmaschinenoptimierung`.

Ein wichtiger, aber leider oft vernachlässigter Faktor für SEO ist die richtige Nutzung der Meta-Informationen. Es gibt keine vergleichbare Massnahme, die sich so direkt und so offensichtlich bemerkbar macht wie die richtige Pflege der Metadaten. Sie erreichen die Pflegemaske für die Metadaten über den Reiter `SEO`.

Hier können Sie den Meta Titel, die Meta-Beschreibung und die Meta-Keywords des Produkts pflegen.

Insbesondere über die Meta-Beschreibung können Sie häufig den Teil bestimmen, den Google bei der Anzeige der Suchergebnisse als Teil des Auszugs einblendet.

Mehr Informationen finden Sie in der Google Webmaster-Tools-Hilfe (Englisch): http://www.google.com/support/webmasters/bin/answer.py?answer=35624

Denken Sie auch immer an ausführliche und hochwertige Produktbeschreibungen und aussagekräftige Bilder. Eine hervorragende Produktbeschreibung bringt gleich doppelten Nutzen: zum einen hilft er bei der besseren Listung Ihres SilverCart Webshops bei Google und anderen Suchmaschinen, zum anderen nutzt die Artikelbeschreibung auch Ihren Besuchern. Und damit steigt die Chance, dass aus einem Besucher auch ein Kunde wird.

#### Warengruppen

Unter einer Warengruppe versteht man im Handel die Zusammenfassung einzelner Artikel anhand eines gemeinsamen Merkmals zu einer Gruppe. Als verbindende Merkmale kommen in Frage:
* Herkunft (z. B. Weine aus Spanien)
* Verwendungszweck (z. B. Getränke)
* Eigenschaften des Herstellungsmaterials (z. B. Holz)
* komplementäre Eigenschaften hinsichtlich eines Verwendungszweckes (z. B. alle Produkte für ein Frühstück)
* Sachbereiche (z. B. bei Büchern eine Warengruppensystematik im Zwischenbuchhandel)

Die Warengruppen können auch weiter in Unterwarengruppen bzw. Artikelgruppen (z. B. alkoholische und alkoholfreie Getränke) differenziert werden oder aber auch in übergeordneten Warenarten zusammengefasst werden (z. B. Food und Nonfood). Auch ist eine verschmelzende Zuordnung einzelner Warengruppen zu ebenfalls übergeordneten Hauptwarengruppen üblich: So werden vielfach die inhaltlich verwandten Warengruppen Glas, Porzellan, Keramik zur Hauptwarengruppe GPK (oder vergleichbar: Papier-, Büro- und Schreibwaren zu PBS) zusammengefasst. Die Gesamtheit aller geführten Artikel eines Handelsgeschäftes bezeichnet man dagegen als Sortiment.
Zuordnung zu Warengruppen

Grundsätzlich gehört ein Artikel zu genau einer Warengruppe. Diese Warengruppe ist die Stammgrupe des Artikels. Viele Warenwirtschaftssysteme arbeiten ebenfalls nach diesem Prinzip. Dadurch ist es einfach, Artikel aus verschiedenen Warenwirtschaftssystemen zu importieren. Die Zuordnung zu einer Warengruppe erfolgt über den Reiter `Links` und dann Warengruppe`. Hier können Sie einem Artikel genau eine Warengruppe als Stammgruppe zuordnen.
`
Quelle: wikipedia (http://de.wikipedia.org/wiki/Warengruppe)

** Zuordnung zu Spiegel-Warengruppen **

Nicht immer lässt sich ein Produktsortiment so geradlinig durch eine Zuordnung von einem Produkt zu genau einer Warengruppe abbilden. Es gibt gute Gründe, dass ein Produkt in mehreren Warengruppen oder auch Kategorien vertreten sein kann.

Über die Zuordnung zu Spiegel-Warengruppen können Sie jedes Produkt zusätzlich zu seiner Stammgruppe beliebig vielen weiteren Warengruppen zuordnen. Über den Reiter `Spiegel-Warengruppen` erreichen Sie die Zuordnungsmaske. Setzen Sie ein Häkchen neben einer Warengruppe wenn der Artikel dieser Warengruppe zusätzlich zu seiner Stammgruppe zugeordnet werden soll. Der Artikel wird dann in diese Warengruppe gespiegelt. Ebenso wie bei einem Spiegelbild wirken sich Veränderungen am Original direkt auf das Spiegelbild aus. Dadurch wird deutlich, dass wir keine Kopie des Artikels angelegt haben, sondern immer nur mit dem Original arbeiten. Eine Änderung am Artikel wirkt sich somit unmittelbar auf den Artikel aus, auch wenn er in eine Warengruppe gespiegelt wurde.

Anwendungsbeispiel: Spiegel-Warengruppen für Preisportal-Exporte

Ein Anwendungsfall für Spiegel-Warengruppen ist die Aufteilung des Sortiments für verschiedene Preisportale. Für den Fall, dass Sie nur bestimmte Teile Ihres Sortiments auf einem Preisportal anbieten möchten, können Sie alle gewünschten Produkte einer eigens hierfür angelegte Spiegel-Warengruppe zuordnen. In der Konfiguration des Exports können Sie dann angeben, dass nur Artikel dieser Spiegel-Warengruppe verwendet werden sollen.

Ein Modul für Preisportal-Exporte ist separat erhältlich.

#### Übersetzungen

Für einen mehrsprachigen Webshop können Sie hier die Übersetzungen des Artikels pflegen. SilverCart ist vollständig auf UTF-8 aufgebaut und erlaubt dadurch auch die Übersetzung in andere Zeichensätze wie beispielsweise kyrillisch.

Über `Übersetzung hinzufügen` können Sie eine neue Sprache eintragen. Eine bestehende Übersetzung können Sie mit dem Notizblock-Symbol bearbeiten. Es stehen Ihnen die folgenden Felder für Übersetzungen zur Verfügung:

* Name
* Listenbeschreibung
* Artikelbeschreibung
* Meta-Beschreibung für Suchmaschinen
* Meta-Schlagworte für Suchmaschinen
* Meta-Titel für Suchmaschinen


#### Bilder

SilverCart ermöglicht Ihnen die Zuordnung mehrerer Bilder zu einem Artikel. Da Produktbilder durchaus auch sprachabhängig sein können (denken Sie an Verpackungen in anderen Sprachen), können Sie zudem eigene Bilder für verschiedene Sprachen hinterlegen.

Sie öffnen die Bildverwaltung über den Reiter `Bilder`. Bereits vorhandene Bilder werden in einer übersichtlichen Tabelle dargestellt.

Ein neues Bild können Sie über `Bild hinzufügen` anlegen. Es öffnet sich ein Fenster, bei dem Sie ein neues Bild hochladen oder ein bereits hochgeladenes Bild auswählen können. Wählen Sie `Bild anhängen` um das Bild dem Artikel zuzuordnen.

Die Angabe der `Sortierreihenfolge` bestimmt die Reihenfolge wenn mehrere Artikelbilder hinterlegt sind.

Über den Reiter `Übersetzungen` können Sie das Bild für eine andere Sprache anlegen.

Vergessen Sie nicht, Ihre Änderungen mit `Speichern` zu bestätigen.

Mit dem roten `X` können Sie ein Bild löschen. Bestätigen Sie hierfür die Sicherheitsabfrage mit `OK`

#### Dateien

Neben Bildern können Sie dem Artikel auch Dateien zuordnen. Ein häufiger Anwendungsfall sind Bedienungsanleitungen oder die Packungsbeilage bei Medikamenten. Sie können hier aber auch Treiber oder ergänzende Programmdateien anbieten.

Wählen Sie hierfür `Datei hinzufügen` und wählen Sie eine Datei von Ihrem Computer oder eine bereits hochgeladene Datei aus. Mit `Datei anhängen` weisen Sie die Datei zu. Sie können einen Namen und eine Beschreibung angeben um Ihren Kunden den Inhalt der Datei zu beschreiben.

Soll in anderen Sprachen eine andere Datei, ein anderer Name oder eine andere Beschreibung verwendet werden, dann können Sie hierfür über den Reiter `Übersetzungen` eine Übersetzung anlegen.

Am Ende können Sie Ihre Änderungen `speichern`.

## Hersteller

Einem Artikel können Sie genau einen Hersteller zuordnen. Darüber lassen sich dann alle Artikel eines bestimmten Herstellers im Shop gruppieren. 

Zudem können Sie Ihren Kunden weiterführende Informationen zum dem Hersteller anbieten.	

### Hauptteil

Zu jedem Hersteller können Sie einen `Titel` (der Name) und eine `URL` (Link zur Homepage des Herstellers) pflegen. 

Ergänzend können Sie noch ein Logo als Bild anhängen. 



### Artikel

Der Reiter `Artikel` zeigt alle Produkte, die mit diesem Hersteller verknüpft sind. Wenn Sie einen Hersteller neu anlegen, dann wird dieser Reiter erst nach dem Speichern sichtbar.

### Übersetzungen

Hier können Sie eine sprachabhängige Übersetzung zu der Herstellerbeschreibung speichern.

Titel und das Logo sind in der Regel sprachunabhängig, deshalb können Sie für diese Angaben keine Übersetzung hinterlegen.





## Verfügbarkeit

Sie können Ihren Artikeln im SilverCart Webshop beliebige Verfügbarkeiten anlegen.

Zur Standardinstallation gehören zwei Verfügbarkeitsstatus: `Verfügbar` und nicht verfügbar`. 

### Hauptteil

** Verfügbarkeit **

Hier tragen Sie den Text ein, den der Verfügbarkeitsstatus haben soll.

** Zusatztext **

Bei Bedarf können Sie hier eine weitere Beschreibung des Verfügbarkeitsstatus eintragen.

** Code **

Der Code ist nur für Programmierer von Interesse und sollte nicht verändert werden. Wenn Sie einen neuen Verfügbarkeitsstatus anlegen, können Sie auf den Code verzichten.

### Übersetzungen

Im Tab `Übersetzungen` können Sie die Verfügbarkeitsstatus in andere Sprachen übersetzen.

Mit `Übersetzung hinzufügen` legen Sie eine neue Übersetzung an. Dabei können Sie je Sprache den Text für `Verfügbarkeit` und für den `Zusatztext` eintragen.

Bestehende Übersetzungen können Sie über den Notizblock bearbeiten oder löschen.


## Mengeneinheiten

Sie haben die Mengeneinheiten bereits im Hauptteil der Artikeldetailansicht kennen gelernt.

Mit den Mengeneinheiten können Sie auch Produktsortimente jenseits von `Stück` kundenfreundlich beschreiben. Wenn Sie Ihre Waren in Flaschen, Kisten, Beuteln, Fässern, Ampullen, Dosen oder Ballen anbieten, können Sie diese Einheiten hier anlegen.

### Hauptteil

Im Hauptteil können Sie 3 Werte je Verkaufsmengeneinheit pflegen:

* Anzahl Dezimalstellen
* Name
* Abkürzung

`Anzahl Dezimalstellen` gibt an, in welchen Mengen das Produkt gekauft werden kann. Sind nur ganzzahlige Mengen möglich, tragen Sie `0` ein oder lassen das Feld leer.

Sind auch andere Werte möglich (z.B. 1,5 Meter Stoff), dann geben Sie die Anzahl der gewünschten Nachkommastellen an.

`Name` ist die Bezeichnung der Verkaufsmengeneinheit. Beispiel: Flasche.

Unter `Abkürzung` tragen Sie die geläufige Abkürzung ein. Im Beispiel der Flasche wäre das `Fl.`.

### Übersetzungen

Im Tab `Übersetzungen` können Sie die Verkaufsmengeneinheiten in andere Sprachen übersetzen.

Mit `Übersetzung hinzufügen` legen Sie eine neue Übersetzung an. Dabei können Sie je Sprache den Text für `Name` und für die `Abkürzung` eintragen.

Bestehende Übersetzungen können Sie über den Notizblock bearbeiten oder löschen.

## Steuersätze

In der Standardinstallation gib es zwei Steuersätze: 7% und 19%.

Weitere Steuersätze können Sie bei Bedarf selbst anlegen.

### Hauptteil

** Steuersatz in % **

Wird zur Berechnung benutzt und muss eine Zahl sein. Soll ein Steuersatz mit Nachkommastellen angelegt werden, dann muss hierfür ein Dezimalpunkt verwendet werden, also `14.5`

** Bezeichner **

Wird zur Darstellung des Steuersatzes im Front-End benutzt

** Ist Standard **

Mit dieser Checkbox wird der Steuersatz als Standard für neu angelegte Artikel bestimmt. 

### Artikel

Unter dem Reiter `Artikel` werden alle Artikel gezeigt, die diesen Steuersatz haben. Der Reiter wird erst sichtbar, nachdem der Steuersatz angelegt und erfolgreich gespeichert wurde. 

### Übersetzungen

Unter dem Reiter `Übersetzungen können Sie die Übersetzungstexte für das Feld Bezeichner in den gewünschten Sprachen hinterlegen.

## Artikelzustände

Bestimmte Marktplätze, wie z.B. Amazon oder ebay erfordern die korrekte Pflege des Artikelzustands. Dies ist notwendig, da auf diesen Marktplätzen neben Neuware auch gebrauchte Artikel mit konkreten Zustandsangaben angeboten werden.

Dem Artikelzustand können deshalb Werte wie `gebraucht, `neu oder `neuwertig` hinterlegt werden. Ein Artikelzustand hat nur ein einziges pflegbares Feld. 

Unter dem Reiter `Artikel` (wird erst nach dem Hinzufügen des Zustandes angezeigt) werden alle Artikel angezeigt, die diesen Zustand besitzen. Der Artikelzustand kann am Artikel dann unter Hauptteil->Inhalt per Dropdown gepflegt werden.

## Artikelmerkmale