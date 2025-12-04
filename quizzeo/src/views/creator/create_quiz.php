<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Nouveau Quiz</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/quiz.css">
</head>
<body class="quiz-page">

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Nouveau Quiz</h1>
        <a href="index.php?route=dashboard" class="btn">Retour</a>
    </div>
    
    <hr style="border: 2px solid var(--main-color); margin: 20px 0;">

    <form action="index.php?route=store_quiz" method="POST">
        
        <label>Titre du Quiz</label>
        <input type="text" name="titre" placeholder="Ex: Géographie mondiale" required>

        <label>Description</label>
        <textarea name="description" rows="3" placeholder="Une courte description..."></textarea>

        <h3>Questions</h3>
        <div id="questions-container">
            </div>

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="button" class="btn btn-blue" onclick="addQuestion()">+ Ajouter une Question</button>
            <button type="submit" class="btn btn-green" style="flex: 1;">Enregistrer le Quiz</button>
        </div>
    </form>
</div>

<script>
    let qCount = 0;

    // Ajouter une Question
    function addQuestion() {
        const container = document.getElementById('questions-container');
        const div = document.createElement('div');
        div.className = 'q-block';
        div.id = 'q-block-' + qCount;
        
        // On initialise avec 2 choix vides par défaut pour ne pas partir de zéro
        let initialChoices = '';
        for(let i=0; i<2; i++) {
            initialChoices += generateChoiceHTML(qCount, i);
        }

        div.innerHTML = `
            <button type="button" class="delete-q-btn" onclick="this.parentElement.remove()">X</button>
            
            <div style="margin-bottom: 10px; display:flex; gap:10px; align-items:center;">
                <label style="margin:0;">Question ${qCount + 1}</label>
                <select name="questions[${qCount}][type]" onchange="toggleType(this, ${qCount})" style="padding:5px;">
                    <option value="qcm">QCM (Choix multiples)</option>
                    <option value="libre">Réponse Libre (Texte)</option>
                </select>
            </div>

            <input type="text" name="questions[${qCount}][texte]" placeholder="Intitulé de la question..." required>
            
            <div id="reponses-zone-${qCount}" style="margin-left: 20px; margin-top: 15px;">
                <label style="font-size: 0.9em; color: #555;">Réponses (Cochez la bonne réponse)</label>
                
                <div id="choices-container-${qCount}">
                    ${initialChoices}
                </div>

                <button type="button" class="btn-add-choice" onclick="addChoice(${qCount})">+ Ajouter une réponse</button>
            </div>
        `;
        container.appendChild(div);
        div.dataset.choiceCount = 2; // On stocke le compteur de choix
        qCount++;
    }

    // Générer le HTML d'un choix unique
    function generateChoiceHTML(qIndex, cIndex) {
        return `
            <div class="choix-row" id="choice-${qIndex}-${cIndex}">
                <input type="checkbox" name="questions[${qIndex}][choix][${cIndex}][correct]" value="1">
                <input type="text" name="questions[${qIndex}][choix][${cIndex}][texte]" placeholder="Réponse" style="margin-bottom:0; flex:1;">
                <button type="button" class="btn-del-choice" onclick="removeChoice('choice-${qIndex}-${cIndex}')">X</button>
            </div>
        `;
    }

    // Fonction pour ajouter un choix dynamiquement
    function addChoice(qIndex) {
        const container = document.getElementById(`choices-container-${qIndex}`);
        const qBlock = document.getElementById(`q-block-${qIndex}`);
        
        // On récupère le compteur actuel pour avoir un ID unique
        let cCount = parseInt(qBlock.dataset.choiceCount || 0);
        
        // On insère le nouveau HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = generateChoiceHTML(qIndex, cCount);
        container.appendChild(tempDiv.firstElementChild);
        
        // On incrémente
        qBlock.dataset.choiceCount = cCount + 1;
    }

    // Supprimer un choix
    function removeChoice(id) {
        document.getElementById(id).remove();
    }

    // Cacher/Afficher selon le type
    function toggleType(select, id) {
        const zone = document.getElementById('reponses-zone-' + id);
        zone.style.display = (select.value === 'libre') ? 'none' : 'block';
    }

    document.addEventListener('DOMContentLoaded', () => {
        addQuestion();
    });
</script>

</body>
</html>