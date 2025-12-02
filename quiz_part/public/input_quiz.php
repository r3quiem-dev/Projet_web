<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Nouveau QCM</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .quiz-form { max-width: 800px; margin: auto; background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .question-block { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background: white; border-radius: 6px; }
        .choix-item { display: flex; align-items: center; margin-bottom: 10px; }
        input[type="text"], textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        .choix-item input[type="text"] { flex-grow: 1; margin-right: 10px; }
        .choix-item input[type="checkbox"] { width: auto; margin-right: 15px; }
        .button-add { background-color: #4CAF50; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        .button-remove { background-color: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-left: 10px; }
    </style>
</head>
<body>

    <div class="quiz-form">
        <h1>Créer un Nouveau QCM</h1>
        
        <form id="quizForm" action="add_quiz.php" method="POST"> 
            
            <h2>Informations Générales</h2>
            <label for="titre">Titre du Quiz:</label>
            <input type="text" name="titre" id="titre" required>
            
            <label for="description">Description:</label>
            <textarea name="description" id="description"></textarea>
            
            <hr>

            <h2>Questions</h2>
            <div id="questions-container">
                </div>
            
            <button type="button" class="button-add" onclick="ajouterQuestion()">+ Ajouter une Question</button>
            
            <hr>
            
            <input type="submit" class="button-add" value="Sauvegarder le Quiz">
        </form>
    </div>

    <script>
        let questionCounter = 0;

        function getChoixHTML(qIndex, cIndex) {
            return `
                <div class="choix-item" id="choix-${qIndex}-${cIndex}">
                    <input type="checkbox" name="questions[${qIndex}][choix][${cIndex}][correct]" value="1">
                    <label>Bonne Réponse ?</label>
                    <input type="text" name="questions[${qIndex}][choix][${cIndex}][texte]" placeholder="Texte du choix" required>
                    <button type="button" class="button-remove" onclick="document.getElementById('choix-${qIndex}-${cIndex}').remove()">Supprimer Choix</button>
                </div>
            `;
        }

        function ajouterChoix(qIndex) {
            const choixContainer = document.getElementById(`choix-container-${qIndex}`);
            const choixIndex = choixContainer.children.length;
            choixContainer.insertAdjacentHTML('beforeend', getChoixHTML(qIndex, choixIndex));
        }

        function ajouterQuestion() {
            const qIndex = questionCounter++;
            const container = document.getElementById('questions-container');
            
            const questionHTML = `
                <div class="question-block" id="question-${qIndex}">
                    <h3>Question #${qIndex + 1}
                        <button type="button" class="button-remove" onclick="document.getElementById('question-${qIndex}').remove()">Supprimer Question</button>
                    </h3>
                    <label for="q-text-${qIndex}">Texte de la Question:</label>
                    <textarea name="questions[${qIndex}][texte]" id="q-text-${qIndex}" required></textarea>
                    
                    <h4>Choix de Réponse</h4>
                    <div id="choix-container-${qIndex}">
                        ${getChoixHTML(qIndex, 0)}
                        ${getChoixHTML(qIndex, 1)}
                    </div>

                    <button type="button" class="button-add" onclick="ajouterChoix(${qIndex})">+ Ajouter un Choix</button>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', questionHTML);
        }

        document.addEventListener('DOMContentLoaded', () => {
            ajouterQuestion();
        });
    </script>
</body>
</html>