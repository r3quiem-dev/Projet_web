<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quizzeo - Connexion</title>
    <style>
        /* ... COLLE ICI TOUT TON CSS UIVERSE ... */
        /* Je ne le remets pas pour gagner de la place, mais c'est ton CSS habituel */
        /* N'oublie pas le correctif de marge qu'on a fait tout à l'heure ! */
        
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f0f0; margin: 0; }
        .alert { position: absolute; top: 20px; padding: 10px 20px; border-radius: 5px; font-weight: bold; border: 2px solid #323232; box-shadow: 4px 4px #323232; z-index: 100; }
        .error { background-color: #ffcccc; color: #cc0000; }
        .success { background-color: #ccffcc; color: #006600; }
    </style>
</head>
<body>

    <?php if (!empty($message)): ?>
        <div class="alert <?= $msgType ?>"><?= $message ?></div>
    <?php endif; ?>

    </body>
</html>