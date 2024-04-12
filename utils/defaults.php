<?php
define('IMAGE_DEFAULT_PROMPT', "Genera un disegno da colorare per bambini su %TOPIC%. Il disegno deve essere minimale, con un solo soggetto e pochi dettagli, poco confusionario, semplice, moderno, tenero e in bianco e nero e con sole linee. Non deve contenere testo, lettere o numeri.");

$text_default_prompt = "Scrivi una descrizione ricca da 200 parole e un titolo per il disegno da colorare caricato come url. Il testo deve essere ottimizzato per il SEO, con parole come \"disegni da colorare\" incluse le parole chiave inerenti al disegno nello specifico. Anche il titolo deve essere ottimizzato per il SEO contenendo parole chiavi inerenti. Le parole chiave importanti racchiudile con il tag <strong></strong>. Il formato della risposta deve rigorosamente essere un json valido, senza backticks ne altra formattazione, e con i campi title e description che indicano rispettivamente il titolo e la descrizione del disegno.";
$text_default_prompt .= "Di seguito ti darò ulteriori istruzioni per le categorie e i tag da assegnare alla descrizione che generi. Mi raccomando, non farti influenzare da queste categorie e tag per generare la descrizione del disegno. Questo significa che devi usare le seguenti informazioni sulle categorie solo ed esclusivamente per assegnare una categoria e dei tag appropriati e non per generare la descrizione del disegno da colorare fornito.";
$text_default_prompt .= "Scegli una sola categoria tra quella più appropriata e uno o più tag tra quelli più appropriati. La categoria e i tag scelti scelti devi metterli nel campo categories e tags del json ritornato, mettendo solo gli id come array json. Se non trovi tag o categorie applicate, lascia il campo della categoria o del tag come array vuoto.";
$text_default_prompt .= "Di seguito le categorie disponibili tra cui scegliere quale categoria assegnare, elencate come json con id e nome di ogni categoria:\n %CATEGORIES%\n\n";
$text_default_prompt .= "Di seguito i tag disponibili tra cui scegliere quali assegnare, elencati come json con id e nome di ogni tag:\n %TAGS\n\n";

define('TEXT_DEFAULT_PROMPT', $text_default_prompt);
define('DEFAULT_AUTO_GENERATION_INTERVAL', 3963);

define('IMAGE_FIRST_DEFAULT', false);
define('AUTOMATIC_CATEGORIES_AND_TAGS_DEFAULT', true);

// The istructions for the text completion json return format
define('JSON_COMPLETION_FORMAT_INSTRUCTIONS', 'Istruzioni: Il formato della risposta deve rigorosamente essere un json valido, senza backticks ne altra formattazione, e con i campi title e description che indicano rispettivamente il titolo e la descrizione dell\'articolo.');

// The istructions for the text completion json return format
define('AUTO_CATEGORIES_INSTRUCTIONS', "Di seguito ti darò ulteriori istruzioni per le categorie e i tag da assegnare alla descrizione che generi. Mi raccomando, non farti influenzare da queste categorie e tag per generare la descrizione del disegno. Questo significa che devi usare le seguenti informazioni sulle categorie solo ed esclusivamente per assegnare una categoria e dei tag appropriati e non per generare la descrizione del disegno da colorare fornito.
Scegli una sola categoria tra quella più appropriata e fino a un massimo di due tag tra quelli più appropriati. La categoria e i tag scelti scelti devi metterli nel campo categories e tags del json ritornato, mettendo solo gli id come array json. Se non trovi tag o categorie applicate, lascia il campo della categoria o del tag come array vuoto.
Di seguito le categorie disponibili tra cui scegliere quale categoria assegnare, elencate come json con id e nome di ogni categoria:
%CATEGORIES%
Di seguito i tag disponibili tra cui scegliere quali assegnare, elencati come json con id e nome di ogni tag:
%TAGS%");