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

    function addQuestion() {
        const div = document.createElement('div');
        div.className = 'q-block';
        div.id = 'q-block-' + qCount;
        
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
                ${[0, 1, 2, 3].map(i => `
                    <div class="choix-row">
                        <input type="checkbox" name="questions[${qCount}][choix][${i}][correct]" value="1">
                        <input type="text" name="questions[${qCount}][choix][${i}][texte]" placeholder="Réponse ${i+1}">
                    </div>
                `).join('')}
            </div>
        `;
        document.getElementById('questions-container').appendChild(div);
        qCount++;
    }

    // Fonction pour cacher/afficher les réponses selon le type
    function toggleType(select, id) {
        const zone = document.getElementById('reponses-zone-' + id);
        if (select.value === 'libre') {
            zone.style.display = 'none';
        } else {
            zone.style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        addQuestion();
    });
</script>

</body>
</html>