# Tab Abwicklung

## Zahlarten

Die hier angezeigten Zahlarten entsprechen den installierten Zahlungsmodulen. In diesem Fall sind die Module iPayment, Paypal und Vorkasse installiert. Die Zahlart `Rechnung` ist schon ohne installierte Zahlungsmodule verfügbar.

Alle Zahlungsmodule haben gemeinsame Konfigurationsoptionen, die im Folgenden aufgeführt sind.

### Grundeinstellungen

**aktiviert**

Mit dieser Option lässt sich jede Zahlart komfortabel ein- und ausschalten.

**Mindestbetrag für Modul**

Der Bestellwert muss zur Zulassung dieser Zahlart erreicht werden.

**Höchstbetrag für Modul**

Der Bestellwert darf diesen Betrag nicht überschreiten, sonst ist die Zahlart nicht mehr verfügbar.

**Name**

Ein frei konfigurierbarer Name, der dem Kunden im Checkout als Name der Zahlart angezeigt wird.

**Beschreibung**

Die Beschreibung der Zahlart wird dem Kunden im Checkout angezeigt. Sie dient der Erläuterung der Zahlart.

**Modus**

Die Zahlart kann im Entwicklungsmodus getestet werden (dev) oder für den Livebetrieb freigeschaltet werden (live).

**Standard Bestellstatus für diese Zahlart**

Bestellungen bekommen diesen Status, wenn sie mit dieser Zahlart abgeschlossen werden.

**Logos**

Zahlarten können Logos haben, die im Checkout angezeigt werden. Dazu müssen Sie die Checkbox Logos anzeigen` setzen.

**Zugriffsverwaltung**

Die Verfügbarkeit der Zahlart kann an verschiedenen Bedingungen geknüpft werden. So kann eine Mindestanzahl von Bestellungen definiert werden, die ein Kunde abgeschlossen haben muss, bevor er die Zahlart nutzen kann. Dazu muss die Checkbox `die folgende Regel anwenden` gesetzt sein. Außerdem kann unter dem Reiter `Gruppen` die Zahlart für bestimmte Gruppen aktiviert oder ausdrücklich deaktiviert werden. Unter dem Reiter `Kunden` können Sie eine Zahlart sogar für einzelne Kunden aktivieren und deaktivieren.
1-2  Stichwörter bearbeiten

**Länder**

Eine Zahlart kann an ein oder mehrere Länder gebunden werden. Das bedeutet, dass diese Zahlart nur für Kunden aus dem jeweiligen Land gültig ist. Ausschlaggebend ist hier das Land der Rechnungsadresse.

**Versandarten**

Eine Zahlart kann an eine oder mehrere Versandarten gebunden werden. Das bedeutet, dass die Zahlart nur in Kombination mit dieser Versandart verwendet werden kann. Dies ist wichtig, wenn beispielsweise die Zahlart "Nachnahme" gewählt wurde, die ja nur im Zusammenhang mit einer bestimmten Versandoption gewäh​lt werden kann.

## Versandarten
Wenn Sie die Beispielkonfiguration angelegt haben, dann existiert bereits die Versandart `Paket`. Andernfalls können Sie eine Versandart erstellen, in dem Sie auf die Schaltfläche `Erstelle Versandart` klicken. Ein Versandart hat immer einen Namen und muss einem Frachtführer zugeordnet sein. 

### Hauptteil
**Aktiv**
Mit dieser Option lässt sich die Versandart ein- und ausschalten.

**Ist Selbstabholung (kein tatsächlicher Versand)**
Aktivieren Sie diese Option, wenn es sich bei der Versandart um Selbstabholung handelt.

**Priorität (Je höher desto wichtiger)**
Tragen Sie hier einen ganzzahligen Wert ein. Unter allen Versandarten wird die Versandart mit der höchsten Priorität ermittelt.

**Nicht auf Versandgebührenseite anzeigen**
Aktivieren Sie diese Option, wenn Sie diese Versandart nicht auf der automatisch erzeugten Versandgebührenseite anzeigen möchten.

**Minimale Lieferzeit**
Minimale Lieferzeit in Werktagen

**Maximale Lieferzeit**
Maximale Lieferzeit in Werktagen

**Eigener Text für die Angabe der Lieferzeit**
Wird anstelle von "Minimale Lieferzeit" und "Maximale Lieferzeit" angezeigt.

Bei Zahlart Vorkasse werden keine Kalenderdaten als Lieferdatum angezeigt, sondern eine Zeitspanne nach Zahlungseingang. 
Dies ist notwendig, da das Datum des Zahlungseingangs nicht bekannt sein kann.

Bei der Angabe von tatsächlichen Datumszeiträumen werden Sonntage berücksichtigt. Nicht berücksichtigt werden Feiertage.

Dieses Verhalten kann über eine Konfigurationseinstellung geändert werden, so dass **immer** eine Zeitspanne in Tagen angezeigt wird. Fügen Sie hierfür in der
config.yml folgende Zeilen hinzu:

    Name: Always show days
    ---
    SilverCart\Model\Shipment\ShippingMethod:
      always_force_display_in_days: true

**Name**
Hier können Sie der Versandart einen Namen geben

**Beschreibung**
Beschreibung für Versandgebührenseite (wird anstelle von "Beschreibung" verwendet)

**Frachtführer**
Hier weisen Sie der Versandart einen Frachtführer zu.


### Versandgebühren

Unter dem Reiter `Versandgebühren` (Erst sichtbar nach dem Hinzufügen) werden alle Versandgebühren gezeigt, die zu dieser Versandart gehören. 

## Übersetzung

## Zonen

Der Reiter `Zonen` zeigt alle Zonen, die gepflegt wurden. Die Zonen werden per Checkbox dieser Versandart zugeordnet. Eine Versandart kann mehreren Zonen zugeordnet werden.

## Gruppen
Eine Versandart kann an eine oder mehrere Kundengruppen gebunden werden. Das bedeutet, dass die Versandart nur von Kunden, die der jeweiligen Kundengruppe angehören ausgewählt werden werden kann.

## Zonen

Frachtführer wie zum Beispiel DHL und UPS gruppieren Länder oft in Zonen. So müssen Versandarten nicht für jedes Land einzeln definiert werden, sondern für eine Zone, die mehrere Länder beinhaltet. Eine Zone gehört immer zu einem Frachtführer. Selbst wenn zwei Frachtführer identische Zonen benutzen, müssen Sie die Zone für jeden Frachtführer neu anlegen.

## Länder

SilverCart hat in seiner Grundinstallation schon alle Länder angelegt. Bitte aktivieren Sie jedes Land, in das Sie Ihre Ware versenden wollen.
Weitere Angaben müssen Sie hier nicht machen. 

Bitte beachten Sie, dass neu hinzugefügte Länder nicht automatisch den verfügbaren Zahlarten und Zonen zugewiesen wurden. Sie können das betreffende Land wie folgt einer Zahlart zuweisen:
Die Detailansicht eines Landes zeigt einen Reiter `Zahlart`. Hier werden alle existierenden Zahlkarten aufgeführt. Falls Sie eine Zahlart für ein Land aktivieren möchten, setzen Sie bitte das Häkchen in der ersten Spalte. Im Bild sind global die Zahlarten `Rechnung` und `Vorkasse` aktiviert (Spalte `aktiviert`), für das aktuell bearbeitete Land allerdings nur die Zahlart `Vorkasse`.

Um ein Land einer Zone zuzuordnen, nutzen Sie den Menüpunkt „Zonen“.

## Frachtführer

Wenn Sie die Beispieldaten konfiguriert haben, dann besitzen Sie bereits den Frachtführer DHL. Einen neuen Frachtführer können Sie über die linke Schaltfläche `Erstelle Frachtführer` erstellen. Im Reiter Versandarten (Erst nach Hinzufügen des Frachtführers sichtbar) werden die zum Frachtführer gehörenden Versandarten angezeigt. Sollten Sie hier neue Versandarten anlegen, werden diese automatisch dem Frachtführer zugeordnet. Das gleiche gilt für die Zonen.
