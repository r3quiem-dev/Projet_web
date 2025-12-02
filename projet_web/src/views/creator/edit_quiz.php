<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Quiz</title>
    <style>
        :root {
            --input-focus: #2d8cf0;
            --font-color: #323232;
            --bg-color: #fff;
            --main-color: #323232;
            --shadow: 4px 4px var(--main-color);
        }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f0f0; color: var(--font-color); padding: 20px; display: flex; justify-content: center; }
        .container { max-width: 800px; width: 100%; background: var(--bg-color); padding: 30px; border-radius: 5px; border: 2px solid var(--main-color); box-shadow: var(--shadow); }
        h1, h3 { color: var(--main-color); margin-top: 0; }
        
        .btn { display: inline-block; padding: 10px 20px; font-weight: 600; text-decoration: none; color: var(--font-color); background: var(--bg-color); border: 2px solid var(--main-color); border-radius: 5px; box-shadow: var(--shadow); cursor: pointer; transition: all 0.1s; }
        .btn:active { box-shadow: 0 0 var(--main-color); transform: translate(2px, 2px); }
        .btn-green { background-color: #ccffcc; }
        .btn-red { background-color: #ffcccc; }
        .btn-blue { background-color: #cce5ff; }

        label { font-weight: bold; display: block; margin-bottom: 5px; margin-top: 15px; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 2px solid var(--main-color); border-radius: 5px; box-shadow: var(--shadow); box-sizing: border-box; font-family: inherit; outline: none; margin-bottom: 10px; }
        input:focus, textarea:focus { border-color: var(--input-focus); }

        .q-block { background: #f9f9f9; border: 2px solid var(--main-color); padding: 20px; margin: 20px 0; border-radius: 5px; position: relative; }
        .delete-q-btn { position: absolute; top: 10px; right: 10px; background: #ffcccc; border: 2px solid var(--main-color); cursor: pointer; font-weight: bold; font-size: 0.8em; padding: 5px 10px; }
        .choix-row { display: flex; align-items: center; gap: 10px; margin-bottom: 5px; }
        .choix-row input[type="checkbox"] { width: 20px; height: 20px; cursor: pointer; border: 2px solid var(--main-color); }
    </style>
</head>
<body>

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

                    <div style="margin-left: 20px;">
                        <label>Réponses (Cochez la bonne)</label>
                        <?php foreach($q['choix'] as $j => $c): ?>
                            <div class="choix-row">
                                <input type="checkbox" name="questions[<?= $i ?>][choix][<?= $j ?>][correct]" value="1" <?= $c['est_correct'] ? 'checked' : '' ?>>
                                <input type="text" name="questions[<?= $i ?>][choix][<?= $j ?>][texte]" value="<?= htmlspecialchars($c['texte']) ?>">
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
    let qCount = <?= count($quiz['questions']) ?> + 100; // +100 pour éviter conflits d'ID

    function addQ() {
        const div = document.createElement('div');
        div.className = 'q-block';
        div.innerHTML = `
            <button type="button" class="delete-q-btn" onclick="this.parentElement.remove()">X</button>
            <label>Nouvelle Question</label>
            <input type="text" name="questions[${qCount}][texte]" placeholder="Intitulé..." required>
            <div style="margin-left: 20px;">
                <label>Réponses</label>
                ${[0,1,2,3].map(j => `
                    <div class="choix-row">
                        <input type="checkbox" name="questions[${qCount}][choix][${j}][correct]" value="1">
                        <input type="text" name="questions[${qCount}][choix][${j}][texte]" placeholder="Réponse ${j+1}">
                    </div>
                `).join('')}
            </div>
        `;
        document.getElementById('questions').appendChild(div);
        qCount++;
    }
</script>

</body>
</html>