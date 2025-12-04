<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Quiz</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/quiz.css">
</head>
<body class="quiz-page">

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Modifier le Quiz</h1>
        <a href="index.php?route=dashboard" class="btn">Retour</a>
    </div>
    
    <hr style="border: 2px solid var(--main-color); margin: 20px 0;">

    <form action="index.php?route=update_quiz" method="POST">
        <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">

        <label>Titre</label>
        <input type="text" name="titre" value="<?= htmlspecialchars($quiz['titre']) ?>" required>

        <label>Description</label>
        <textarea name="description" rows="3"><?= htmlspecialchars($quiz['description']) ?></textarea>

        <h3>Questions</h3>
        <div id="questions">
            <?php foreach($quiz['questions'] as $i => $q): ?>
                <div class="q-block">
                    <button type="button" class="delete-q-btn" onclick="this.parentElement.remove()">X</button>
                    <label>Question <?= $i + 1 ?></label>
                    <input type="text" name="questions[<?= $i ?>][texte]" value="<?= htmlspecialchars($q['texte']) ?>" required>

                    <div style="margin-left: 20px;" id="reponses-zone-<?= $i ?>">
                    <label>Réponses (Cochez la bonne)</label>
                    
                    <div id="choices-container-<?= $i ?>">
                        <?php foreach($q['choix'] as $j => $c): ?>
                            <div class="choix-row" id="choice-<?= $i ?>-<?= $j ?>">
                                <input type="checkbox" name="questions[<?= $i ?>][choix][<?= $j ?>][correct]" value="1" <?= $c['est_correct'] ? 'checked' : '' ?>>
                                <input type="text" name="questions[<?= $i ?>][choix][<?= $j ?>][texte]" value="<?= htmlspecialchars($c['texte']) ?>" style="margin-bottom:0; flex:1;">
                                <button type="button" class="btn-del-choice" onclick="this.parentElement.remove()">X</button>
                            </div>
                        <?php endforeach; ?>
                            </div>
                                <button type="button" class="btn-add-choice" onclick="addChoice(<?= $i ?>)">+ Ajouter une réponse</button>
                            </div>
                        <?php endforeach; ?>
                        <?php for($k = count($q['choix']); $k < 2; $k++): ?>
                            <div class="choix-row">
                                <input type="checkbox" name="questions[<?= $i ?>][choix][<?= $k ?>][correct]" value="1">
                                <input type="text" name="questions[<?= $i ?>][choix][<?= $k ?>][texte]" placeholder="Nouveau choix">
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="button" class="btn btn-blue" onclick="addQ()">+ Ajouter une Question</button>
            <button type="submit" class="btn btn-green" style="flex: 1;">Enregistrer les modifications</button>
        </div>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="index.php?route=delete_quiz&id=<?= $quiz['id'] ?>" class="btn btn-red" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce quiz ?')">Supprimer le Quiz</a>
        </div>
    </form>
</div>

<script>
    // On initialise le compteur de questions pour éviter les conflits d'ID
    let qCount = <?= count($quiz['questions']) ?> + 50; 

    // Fonction pour ajouter un choix (Compatible PHP et JS)
    function addChoice(qIndex) {
        const container = document.getElementById(`choices-container-${qIndex}`);
        
        // Astuce : On utilise Date.now() pour générer un ID unique pour le nouveau choix
        // afin de ne pas entrer en conflit avec les index PHP existants [0], [1]...
        const uniqueId = Date.now(); 

        const div = document.createElement('div');
        div.className = 'choix-row';
        div.innerHTML = `
            <input type="checkbox" name="questions[${qIndex}][choix][${uniqueId}][correct]" value="1">
            <input type="text" name="questions[${qIndex}][choix][${uniqueId}][texte]" placeholder="Nouvelle réponse" style="margin-bottom:0; flex:1;">
            <button type="button" class="btn-del-choice" onclick="this.parentElement.remove()">X</button>
        `;
        container.appendChild(div);
    }

    // Ajouter une toute nouvelle question
    function addQ() {
        const div = document.createElement('div');
        div.className = 'q-block';
        div.id = 'q-block-' + qCount;
        
        div.innerHTML = `
            <button type="button" class="delete-q-btn" onclick="this.parentElement.remove()">X</button>
            <label>Nouvelle Question</label>
            <input type="text" name="questions[${qCount}][texte]" placeholder="Intitulé..." required>
            
            <div style="margin-left: 20px;">
                <label>Réponses</label>
                <div id="choices-container-${qCount}">
                    ${generateNewChoiceHTML(qCount, 0)}
                    ${generateNewChoiceHTML(qCount, 1)}
                </div>
                <button type="button" class="btn-add-choice" onclick="addChoice(${qCount})">+ Ajouter une réponse</button>
            </div>
        `;
        document.getElementById('questions').appendChild(div);
        qCount++;
    }

    function generateNewChoiceHTML(qIdx, cIdx) {
        return `
            <div class="choix-row">
                <input type="checkbox" name="questions[${qIdx}][choix][${cIdx}][correct]" value="1">
                <input type="text" name="questions[${qIdx}][choix][${cIdx}][texte]" placeholder="Réponse" style="margin-bottom:0; flex:1;">
                <button type="button" class="btn-del-choice" onclick="this.parentElement.remove()">X</button>
            </div>
        `;
    }
</script>

</body>
</html>