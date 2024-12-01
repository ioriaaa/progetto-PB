<?php
    ini_set('session.gc_maxlifetime', 7200);
    session_set_cookie_params(7200);
    session_start();
    session_start();
    
    // Verifico se l'utente è loggato, altrimenti reindirizzo alla pagina di accesso
    if (!isset($_SESSION['user_email'])) {
        header("Location: login.php");
        exit;
    }
?>    
<!DOCTYPE html>
<html>
    <head>
        <title>Rendicontazione</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Orbitron">
        <link href='https://fonts.googleapis.com/css?family=Merriweather Sans' rel='stylesheet'>
        <link href="style.css" rel="stylesheet" type="text/css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
<body>
	<div id="box3">
        <img id="logo" src="img/logo.png" alt="Immagine non trovata">
        <h1><a id='pb-link' href='ins_visua_project.php'>PB</a></h1>
    </div>
    <?php
    require_once("db.php");
    
    if(isset($_POST['id']) || !empty($_POST['id'])) {
        $projectId = $_POST['id'];
        echo "<p style=display:none id='get-id2' data-id='".$projectId."'></p>";
    
        // Esegui la query per recuperare i dettagli del progetto utilizzando l'ID
        $stm = $conn->prepare("SELECT * FROM progetti WHERE id =". $projectId);
        $stm->execute();
        $project = $stm->fetchAll(); // Restituisce la riga corrente come array associativo
        
        $tabGenerale = "<table id='tbVisuaDetails'><tr>";
        $tabRiferimenti = "<table id='tbVisuaDetails'><tr>";
        $tabAspettiDid = "<table id='tbVisuaDetails'><tr>";
        $tabDestinatari = "<table id='tbVisuaDetails'><tr>";
        $tabCompetenze = "<table id='tbVisuaDetails'><tr>";
   		$tabRisorseInt = "<table id='tbVisuaDetails'><tbody><tr><td><b>Nominativo</b></td><td><b>Ore Curricolari</b></td><td><b>Ore Extra-Curricolari</b></td><td><b>Ore Sorveglianza</b></td><td><b>Ore Progettazione</b></td><td><b>Area Competenza</b></td><td><b>Ruolo</b></td><td></td><td></td></tr>";
   		$tabRisorseExt = "<table id='tbVisuaDetails'><tbody><tr><td><b>Nominativo</b></td><td><b>Ore Docenza</b></td><td><b>Costo Previsto</b></td><td>Costi eventuali aggiuntivi</td><td></td></tr>";
        
        // Stampiamo dinamicamente tutti gli attributi del progetto
        foreach($project as $value) {
        	echo "<p id='titVisua'><b>Progetto ". $value["titolo"]."</b></p>";
            
            
            
            $stmRef = $conn->prepare("SELECT nominativo FROM docenteReferente WHERE id =". $value["fk_docenteReferente"]);
        	$stmRef->execute();
        	$docRef = $stmRef->fetchAll();
            foreach($docRef as $doc){
            	$tabGenerale .= "<th>Docente referente:</th><td>".$doc["nominativo"]."</td></tr>";
            }
            
            $stmDip = $conn->prepare("SELECT nome FROM dipartimento WHERE id =". $value["fk_dipartimento"]);
        	$stmDip->execute();
        	$dipart = $stmDip->fetchAll();
            foreach($dipart as $dip){
            	$tabGenerale .= "<tr><th>Dipartimento:</th><td>".$dip["nome"]."</td>";
            }
            
            $tabRiferimenti .= "<th>Strutturale:</th><td>".$value["strutturale"]."</td></tr>";
            $tabRiferimenti .= "<th>Ore di orientamento:</th><td>".$value["orientamento"]."</td></tr>";
            $tabRiferimenti .= "<th>Percorso di PCTO:</th><td>".$value["PCTO"]."</td></tr>";
            
            $tabAspettiDid .= "<th>Analisi del contesto:</th><td>".$value["analisi_contesto"]."</td></tr>";
            $tabAspettiDid .= "<th>Obbiettivi attesi:</th><td>".$value["obbiettivi_attesi"]."</td></tr>";
            $tabAspettiDid .= "<th>Attività previste:</th><td>".$value["attivita_previste"]."</td></tr>";
            $tabAspettiDid .= "<th>Metodologia e strumenti:</th><td>".$value["metodologia_e_strumenti"]."</td></tr>";
			$tabAspettiDid .= "<th>Tempi di svolgimento:</th><td>".$value["tempi_svolgimento"]."</td></tr>";
            $tabAspettiDid .= "<th>Luoghi di svolgimento:</th><td>".$value["luoghi_svolgimento"]."</td></tr>";
            $tabAspettiDid .= "<th>Modalità di verifica in itinere e finale:</th><td>".$value["verifica_itinere_e_finale"]."</td></tr>";
            $tabAspettiDid .= "<th>Documentazione:</th><td>".$value["documentazione"]."</td></tr>";
          
            $stmClassi = $conn->prepare("SELECT c.anno_classe, c.sezione FROM progetti_classi p, classi c WHERE p.fk_progetto =".$value["id"]." AND p.fk_classe = c.id");
        	$stmClassi->execute();
        	$classi = $stmClassi->fetchAll();
            $tabDestinatari .= "<tr><th>Classi destinatarie del progetto:</th><td>";
            $cont = 0;
            foreach($classi as $cla){
              $cont++;
              $tabDestinatari .= $cla["anno_classe"].$cla["sezione"]." - ";	
              if($cont == 4){
                $tabDestinatari .= "<br>";
                $cont = 0;
              }
            }
            
            $tabDestinatari = rtrim($tabDestinatari, "<br>");
            $tabDestinatari = substr($tabDestinatari, 0, -2);
        	$tabDestinatari .= "</td></tr>";
            
            $stmOreClassi = $conn->prepare("SELECT p.ore_pomeriggio, p.ore_mattina FROM progetti_classi p WHERE p.fk_progetto =".$value["id"]);
        	$stmOreClassi->execute();
        	$oreClassi = $stmOreClassi->fetchAll();
            foreach($oreClassi as $cla){
            	$oreMatt = $cla["ore_mattina"];
                $orePom = $cla["ore_pomeriggio"];
            }
            $tabDestinatari .="<tr><th>Ore mattina:</th><td id='oreMatt'>".$oreMatt."</td></tr><tr><th>Ore pomeriggio:</th><td id='orePom'>".$orePom."</td></tr>";
            
            
            $stmComp = $conn->prepare("SELECT c.descrizione FROM progetti_competenze p, competenze c WHERE p.fk_progetti =".$value["id"]." AND p.fk_competenze = c.id");
        	$stmComp->execute();
        	$contaComp = 0;
              foreach($competenze as $cmp){
              	$contaComp++;
				$tabCompetenze .= "<tr><th>Competenza ".$contaComp.":</th><td>".$cmp["descrizione"]."</td>";
              }
              
            $stmRisInt= $conn->prepare("SELECT r.id, r.nominativo, r.oreCurricolari, r.oreExtraCurricolari, r.oreSorveglianza, r.oreProgettazione, r.OreCurricolariEffettive, r.OreExtraCurricolariEffettive, r.OreSorveglianzaEffettive, r.OreProgettazioneEffettive FROM risorseInterne r, progetti_risorse p WHERE p.fk_progetti =".$value["id"]." AND p.fk_risorsaInterna = r.id GROUP BY r.id, r.nominativo");
        	$stmRisInt->execute();
        	$risInt = $stmRisInt->fetchAll();
              foreach($risInt as $rsi){
				$tabRisorseInt .= "<tr><td>".$rsi["nominativo"]."</td><td id='oreCurr-".$rsi["nominativo"]."'>".$rsi["oreCurricolari"]." (".$rsi["OreCurricolariEffettive"].")</td><td id='oreExtraCurr-".$rsi["nominativo"]."'>".$rsi["oreExtraCurricolari"]." (".$rsi["OreExtraCurricolariEffettive"].")</td><td id='oreSorv-".$rsi["nominativo"]."'>".$rsi["oreSorveglianza"]." (".$rsi["OreSorveglianzaEffettive"].")</td><td id='oreProg-".$rsi["nominativo"]."'>".$rsi["oreProgettazione"]." (".$rsi["OreProgettazioneEffettive"].")</td>";
              	$stmAreaComp= $conn->prepare("SELECT DISTINCT p.areaCompetenza, p.ruolo FROM risorseInterne r, progetti_risorse p WHERE p.fk_risorsaInterna =".$rsi["id"]);
        		$stmAreaComp->execute();
        		$areaComp = $stmAreaComp->fetchAll();
                $tabRisorseInt.= "<td>";
                foreach($areaComp as $arc){
                	$tabRisorseInt .= $arc["areaCompetenza"].", ";
                    $ruolo = $arc["ruolo"];
                }
                $tabRisorseInt = rtrim($tabRisorseInt, ", ");
                $tabRisorseInt.="</td><td>".$ruolo."<td><td class='get-id' data-id='".$rsi["id"]."'><span id='insOreEff'>+</span></td></tr>";
              }
              
            $stmRisExt= $conn->prepare("SELECT r.id, r.nominativo, r.oreDocenza, r.costoPrevisto, r.oreDocenzaEffettive, r.costoEffettivo, r.costiEventuali FROM risorseEsterne r, progetti_risorse p WHERE p.fk_progetti =".$value["id"]." AND p.fk_risorsaEsterna = r.id");
        	$stmRisExt->execute();
        	$risExt = $stmRisExt->fetchAll();
              foreach($risExt as $rse){
				$tabRisorseExt .= "<tr><td>".$rse["nominativo"]."</td><td>".$rse["oreDocenza"]." (".$rse["oreDocenzaEffettive"].")</td><td>".$rse["costoPrevisto"]."(".$rse["costoEffettivo"].")</td><td>".$rse["costiEventuali"]."</td><td class='get-id-ext' data-id-ext='".$rse["id"]."'><span id='insOreDocEff'>+</span></td></tr>";
              }
        }
        echo "<p class='subTit'>Informazioni generali</p>";
        $tabGenerale .= "</tr></table>";
        echo $tabGenerale;
        
        echo "<p class='subTit'>Riferimenti al PTOF</p>";
        $tabRiferimenti .= "</tr></table>";
        echo $tabRiferimenti;
        
        echo "<p class='subTit'>Aspetti didattici</p>";
        $tabAspettiDid .= "</tr></table>";
        echo $tabAspettiDid;
        
        echo "<p class='subTit'>Destinatari del progetto</p>";
        
        $tabDestinatari .= "</table>";
        echo $tabDestinatari;
        
        if($contaComp > 1){
          echo "<p class='subTit'>Competenze progetto</p>";
          $tabCompetenze .= "</tr></table>";
          echo $tabCompetenze;
        }
        
        echo "<p class='subTit'>Risorse interne</p>";
        $tabRisorseInt .= "</table>";
        echo $tabRisorseInt;
        
        echo "<p class='subTit'>Risorse esterne</p>";
        $tabRisorseExt .= "</table>";
        echo $tabRisorseExt;
        
    } else {
        echo "<p>Progetto non trovato.</p>";
    }
?>        
    <div style=display:none id='boxOreEff'>
    		<div class="boxOreEff-content">
              <span id="closePopup">&times;</span>
              <p id="subTitPopup">Ore effettive</p>
              	<div id="contenuto"></div>
        	</div>
    </div>
    <p id='exit-link'><b><a id='exit-link' href='ins_visua_project.php'>Ritorna alla home</a></b></p>
</body>
  <script>
  	const openPopupButton = document.getElementById('insOreEff');
    const closePopupButton = document.getElementById('closePopup');
    const popup = document.getElementById('boxOreEff');
	    
    document.querySelectorAll('.get-id').forEach(span => {
      span.addEventListener('click', function() {
          const dataId = this.getAttribute('data-id');
		  var element = document.getElementById('get-id2');
		  var projectId = element.dataset.id;   
          var mess2="int";
          setTimeout(function() {
    		 popup.style.display="inline-block"; 
		  }, 150);
          jQuery.ajax({
                    type: 'POST',
                    url: "crea_ore_eff.php",
                    dataType: 'json',
                    data: {
                        'dato' : dataId,
                        'id' : projectId,
                        'mess' : mess2,
                    },
                    success: function(response) {                        
                       if (response.success) {
                          // Prendi solo la risposta e assegnala a una variabile
                          var risposta = response.risposta;

                          // Aggiorna il contenuto dell'elemento con ID 'contenuto'
                          document.getElementById("contenuto").innerHTML = risposta;
                      } else {
                          console.error("Errore: " + response.message);
                      }
                      
                    }
                });
      });
});

document.querySelectorAll('.get-id-ext').forEach(span => {
      span.addEventListener('click', function() {
          const dataIdExt = this.getAttribute('data-id-ext');
          var mess="ext";
          
		  var element = document.getElementById('get-id2');
		  var projectId = element.dataset.id; 
          setTimeout(function() {
    		 popup.style.display="inline-block"; 
		  }, 125);
         
          jQuery.ajax({
                    type: 'POST',
                    url: "crea_ore_eff.php",
                    dataType: 'json',
                    data: {
                        'dato-ext' : dataIdExt,
                        'id' : projectId,
                        'mess' : mess,
                    },
                    success: function(response) {                        
                       if (response.success) {
                          // Prendi solo la risposta e assegnala a una variabile
                          var risposta = response.risposta;

                          // Aggiorna il contenuto dell'elemento con ID 'contenuto'
                          document.getElementById("contenuto").innerHTML = risposta;
                      } else {
                          console.error("Errore: " + response.message);
                      }
                      
                    }
                });
      });
});

   
    closePopupButton.addEventListener('click', function() {
        popup.style.display="none";
        document.getElementById("contenuto").innerHTML = " ";
    });
    
function disableScroll() {
    // Calcola la larghezza della barra di scorrimento
    var scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;

    // Aggiungi una classe al body per bloccare lo scroll e compensare la larghezza della barra di scorrimento
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = scrollbarWidth + 'px';
}

function enableScroll() {
    // Rimuovi la classe che blocca lo scroll e ripristina il padding destro
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

openPopupButton.addEventListener('click', function() {
    popup.style.display='inline-block';
    disableScroll(); // Blocca lo scroll quando il popup viene aperto
});

closePopupButton.addEventListener('click', function() {
    popup.style.display='none';
    enableScroll(); // Consenti nuovamente lo scroll quando il popup viene chiuso
    document.getElementById("contenuto").innerHTML = " ";
});

function addPopupEventListeners() {
		  var element = document.getElementById('get-id');
		  var idRis = element.getAttribute('data-id');
          var oreCurrValue = document.getElementById('oreCurrForm').value;
          var oreExtraCurrValue = document.getElementById('oreExtrCurrForm').value;
          var oreSorvValue = document.getElementById('oreSorvForm').value;
          var oreProgValue = document.getElementById('oreProgForm').value;
          var mess="int";
          
          jQuery.ajax({
                    type: 'POST',
                    url: "upd_ore_eff.php",
                    dataType: 'json',
                    data: {
                    	'id': idRis,
                        'mess': mess,
                        'oreCurrValue' : oreCurrValue,
                        'oreExtraCurrValue' : oreExtraCurrValue,
                        'oreSorvValue' : oreSorvValue,
                        'oreProgValue' : oreProgValue,
                    }
                });
          
          
          
          location.reload();
          document.getElementById("contenuto").innerHTML = " ";
}

function addPopupEventListenersExt() {
		  var element = document.getElementById('get-id');
		  var idRis = element.getAttribute('data-id');
          var oreDocValue = document.getElementById('oreDocForm').value;
          var costoEffValue = document.getElementById('costoDocForm').value;
          var mess2="ext";
				jQuery.ajax({
                    type: 'POST',
                    url: "upd_ore_eff.php",
                    dataType: 'json',
                    data: {
                    	'id': idRis,
                        'mess': mess2,
                        'oreDocValue' : oreDocValue,
                        'costoDocValue' : costoEffValue,
                    }
                });          
          location.reload();
          document.getElementById("contenuto").innerHTML = " ";
}
  </script>
</html>