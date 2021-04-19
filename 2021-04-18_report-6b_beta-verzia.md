# Report no.6,5
Martin Stolárik\
Evidencia (športového) klubu\
https://github.com/bacigala/club-manager\
https://davinci.fmph.uniba.sk/~stolarik6/club-manager/\
Klient: user1, heslo1\
Lektor/účtovník/administrátor: lector1, heslo1\

Tento report obsahuje kumulatívne údaje, v súvislosti s prezentáciou beta verzie aplikácie.

## Implementované časti
- návrh rozloženia GUI
- návrh tabuliek databázy
- sprevádkovanie bežiacej inštancie komunikujúcej s databázou
	- používateľ - rola klient
		- vidí kurzy a udalosti na ktoré je prihlásený
		- podáva žiadosti o prihásenie na udalosti
		- prijíma / odmieta pozvánky na udalosti
	-  používateľ - rola účtovník
		- vidí, upravuje, pridáva a odstraňuje položky a platby
	- používateľ - rola lektor
		- vidí a upravuje svoje udalosti
		- akceptuje / zamieta žiadosti o prihlásenie k udalosti
		- odstraňuje klientov z udalostí

## Neimplementované časti, plán
- klient
	- kreditné konto - stav, možnosť z neho platiť
	- generovanie platobných detailov k zaúčtovaným položkám
- účtovník
	- parser platieb z výpisu z internet bankingu
- lektor
	- vytváranie udalostí _(prioritne)_
	- pridávanie klientov a lektorov k udalostiam, posielanie pozvánok _(prioritne)_
	- evidencia dochádzky _(prioritne)_
- admin
	- správa používateľských účtov _(prioritne)_
- iné
	- rozposielanie mailov (upomienky k platbe, notifikácie)

## Časové nároky
- cca 50h

## Problémy a výzvy
- AJAX bol novinkou
- zatiaľ bez väčších komplikácií
