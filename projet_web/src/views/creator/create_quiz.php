<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Nouveau Quiz</title>
    <style>
        /* DESIGN NEUBRUTALISM (Même charte que Login) */
        :root {
            --input-focus: #2d8cf0;
            --font-color: #323232;
            --bg-color: #fff;
            --main-color: #323232;
            --shadow: 4px 4px var(--main-color);
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f0f0;
            color: var(--font-color);
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: var(--bg-color);
            padding: 30px;
            border-radius: 5px;
            border: 2px solid var(--main-color);
            box-shadow: var(--shadow);
        }

        h1, h3 { color: var(--main-color); margin-top: 0; }
        
        /* BOUTONS */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-weight: 600;
            text-decoration: none;
            color: var(--font-color);
            background: var(--bg-color);
            border: 2px solid var(--main-color);
            border-radius: 5px;
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: all 0.1s;
        }
        .btn:active { box-shadow: 0 0 var(--main-color); transform: translate(2px, 2px); }
        .btn-green { background-color: #ccffcc; }
        .btn-blue { background-color: #cce5ff; }

        /* INPUTS */
        label { font-weight: bold; display: block; margin-bottom: 5px; margin-top: 15px; }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--main-color);
            border-radius: 5px;
            box-shadow: var(--shadow);
            box-sizing: border-box;
            font-family: inherit;
            outline: none;
            margin-bottom: 5px;
        }
        input:focus, textarea:focus { border-color: var(--input-focus); }

        /* BLOC QUESTION */
        .q-block {
            background: #f9f9f9;
            border: 2px solid var(--main-color);
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            position: relative;
        }
        
        .delete-q-btn {
            position: absolute;
            top: 10px; right: 10px;
            background: #ffcccc;
            border: 2px solid var(--main-color);
            cursor: pointer;
            font-weight: bold;
            font-size: 0.8em;
            padding: 5px 10px;
        }

        .choix-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .choix-row input[type="checkbox"] { width: 25px; height: 25px; cursor: pointer; border: 2px solid var(--main-color); }
    </style>
</head>
<body>

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
        div.innerHTML = `
            <button type="button" class="delete-q-btn" onclick="this.parentElement.remove()">X</button>
            <label>Question ${qCount + 1}</label>
            <input type="text" name="questions[${qCount}][texte]" placeholder="Intitulé de la question..." required>
            
            <div style="margin-left: 20px; margin-top: 15px;">
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

    // Ajouter une question par défaut au chargement
    document.addEventListener('DOMContentLoaded', () => {
        addQuestion();
    });
</script>

</body>
</html>