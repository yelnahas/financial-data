# Documentazione del Progetto

## **Maatwebsite Excel (Versione 3.1)**

### **Motivazione**

Per la gestione dell'importazione dei dati finanziari da file Excel e CSV, è stato scelto il pacchetto **Maatwebsite Excel 3.1**, una delle dipendenze a mio avviso più affidabili e ben integrate con Laravel. Le principali motivazioni sono:

1. **Integrazione con Laravel**:  
   Maatwebsite Excel è progettato per lavorare perfettamente con le funzionalità di Laravel, come i controller, le code e l'architettura MVC.

2. **Supporto per diversi formati**:  
   Una delle caratteristiche distintive di Maatwebsite Excel è il suo supporto per una vasta gamma di formati di file, tra cui **CSV**, **XLSX**, **XLS**, **ODS**, e altri. Questa capacità rende il progetto "financial-data" altamente scalabile, poiché può gestire vari formati di file senza la necessità di scrivere codice personalizzato per ogni tipo di importazione.

3. **Caricamento dei dati**:  
   E' stato scelto anche per la sua capacità di caricare grandi quantità di dati in modo efficiente. Le sue funzionalità, come il caricamento in **chunk** e la gestione delle righe di dati, sono fondamentali per garantire alte prestazioni durante l'importazione di file di grandi dimensioni.

4. **Facilita di estensione**:  
   La libreria è facilmente estendibile, permettendo di definire la logica di importazione personalizzata tramite la classe `ToModel` o `WithHeadingRow`. Questo consente di mappare i dati importati su modelli Eloquent con una struttura ben definita.

### **Scalabilità del progetto**

Il progetto è stato realizzato per essere altamente scalabile, grazie alla capacità di Maatwebsite Excel di gestire più formati di file. Questo significa che, in futuro, sarà possibile supportare altri formati di file, come JSON o XML, senza la necessità di riscrivere la logica di importazione. Questo approccio offre una grande flessibilità, sia per l'importazione di dati provenienti da diverse fonti che per l'evoluzione futura del sistema.

## **Gestione dei log con Monolog**

Per tracciare e monitorare il processo di importazione, è stato implementato l'uso di **Monolog**, un potente package di logging che fornisce una gestione avanzata dei log. La configurazione di Monolog è stata personalizzata per generare log dinamici, dove ogni **filiale** ha un file di log separato. Ciò consente di isolare facilmente i problemi legati a una specifica filiale e di monitorare i processi di importazione in modo più chiaro.

Il percorso del file di log è determinato dinamicamente in base all'ID della filiale. Se il file di log non esiste, viene creato automaticamente. Inoltre, viene effettuato un controllo per garantire che il percorso del file sia valido, evitando errori durante l'operazione.


## **Ottimizzazione delle performance con i Job**

Per garantire che l'importazione dei file avvenga in modo asincrono e non blocchi il processo principale, è stato adottato il sistema di **job** di Laravel. Per ogni file da importare, viene creato un job separato, che viene poi eseguito in background, garantendo così che il sistema possa gestire più file contemporaneamente senza compromettere le performance.

### **Considerazioni**

Per migliorare ulteriormente le performance in caso di file Excel di grandi dimensioni, si potrebbe considerare l'uso di **chunking**. Questo approccio prevede la suddivisione dei dati in **blocchi** più piccoli (ad esempio, 1000 righe alla volta), che vengono poi trattati separatamente tramite job. In questo modo, l'importazione diventa ancora più gestibile e meno soggetta a errori di memoria, migliorando le prestazioni complessive.

Inoltre, per garantire che i job vengano processati in modo continuo e che i worker siano sempre attivi, è stata presa in considerazione l'implementazione di **Supervisor**. 

#### **Vantaggi di utilizzare Supervisor:**

- **Monitoraggio continuo dei processi**: Supervisor garantisce che il worker venga riavviato automaticamente in caso di errore o se il processo termina inaspettatamente.
- **Gestione centralizzata dei processi**: È possibile configurare e monitorare più worker facilmente, centralizzando la gestione dei processi di background.
- **Affidabilità**: In caso di fallimento del worker, Supervisor lo riavvierà automaticamente, garantendo che il sistema rimanga operativo senza interruzioni.

## **Conclusioni**

- **Maatwebsite Excel 3.1** semplifica l'importazione di file Excel e CSV e supporta vari formati di file.
- **Monolog** per una gestione avanzata e dinamica dei log, specifica per ogni filiale.
- **Jobs** per eseguire l'importazione dei file in modo asincrono e garantire performance elevate, con la possibilità di ottimizzare ulteriormente tramite chunking.