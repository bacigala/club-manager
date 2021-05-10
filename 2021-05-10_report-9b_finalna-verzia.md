# Report no.9,5
Martin Stolárik\
Evidencia (športového) klubu\
https://github.com/bacigala/club-manager \
https://davinci.fmph.uniba.sk/~stolarik6/club-manager/ \
Klient: user1, heslo1 \
Lektor/účtovník/administrátor: admin, admin 

Tento report obsahuje kumulatívne údaje, v súvislosti s prezentáciou finálnej verzie aplikácie.

## Implementované časti
- anonymný "okoloidúci"
	- registrácia, vytvoreie používateľského účtu
- používateľ - rola klient (po prihlásení)
	- vidí skupiny, kurzy a udalosti (ďalej ako "SKU"), na ktoré je prihlásený
	- podáva žiadosti o prihásenie na SKU
	- prijíma alebo odmieta pozvánky na SKU
	- vidí svoju dochádzku
	- vidí mu účtované platby, vytvára pre ne transakcie (skupiny s priradenými platobnými detailami)
	- uhrádza platby kreditom
	- upravuje detaily svojho účtu (email, login, heslo)
-  používateľ - rola účtovník (po prihlásení)
	- vidí, upravuje, pridáva a odstraňuje položky a platby
	- môže označiť prijatie transakcie
- používateľ - rola lektor (po prihlásení)
	- vidí a upravuje SKU
	- akceptuje / zamieta žiadosti o prihlásenie k SKU
	- odstraňuje klientov a lektorov z SKU
- používateľ - rola administrátor (po prihlásení)
	- vidí a upravuje používateľské účty a práva

## Chyby, výzvy, komplikácie, neimplementované časti, záver
I keď je aplikácia implementovaná v pôvodne plánovanom rozsahu, je (stále) čo vylepšovať i pridávať. Napríklad kromadné účtovanie položiek klientom alebo možnosť pridať hromadne na kurz len podmnožinu zo skupiny klientov.

V aplikácii som zo začiatku zvolil "štandardny PHP prístup" k manipulácii s databázou, teda vždy ku každej upravovanej časti vznikol formulár, ktorý sa vyhodnocuje niekoľkými refresh-mi stránky (najprv je načítaný s prednastavenými hodnotami, potom odoslaný cez POST, potom sú hodnoty v POST vyhodnotené a POST odstránený a následne je znovu načítaný formulár, napríklad ak niečo nevyšlo). Toto som oľutoval neskôr, keď som pri implementácii častí umožňujúcich lektorom manipuláciu s udalosťami zvolil AJAX prístup, čo bolo pohodlnejšie na implementáciu, pre komunikáciu efektívnejšie a tiež je to krajšie v UI. Kebyže vytváram projek od začiatku, s použitím PHP, jeho účel by som obmedzil len na prvotné načítanie stránky a tvorbu odpovedí AJAX request-ov.

Ukázalo sa, že plánovanie projektu, odhad času či návrh databázy sú celkom náročnými operáciami, ktoré majú (momentálne u mňa) vážne len orientačný charakter. Zmien za behu bolo treba robiť viac než som pôvodne myslel. Snáď sa ten môj odhad časom zlepší... :)

## Časové nároky
- cca 84,5h