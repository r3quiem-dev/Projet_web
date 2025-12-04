<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <style>
        :root { --main-color: #323232; --bg-color: #fff; --shadow: 4px 4px var(--main-color); --focus: #2d8cf0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f0f0; color: var(--main-color); padding: 20px; display: flex; justify-content: center; }
        
        .container { max-width: 500px; width: 100%; background: var(--bg-color); padding: 30px; border: 2px solid var(--main-color); box-shadow: var(--shadow); border-radius: 8px; }
        
        h1 { margin-top: 0; text-align: center; }
        
        label { display: block; margin-top: 15px; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-top: 5px; border: 2px solid var(--main-color); border-radius: 5px; box-sizing: border-box; font-family: inherit; }
        input:focus { border-color: var(--focus); outline: none; }
        
        .btn { display: block; width: 100%; margin-top: 25px; padding: 12px; background: #28a745; color: white; border: 2px solid var(--main-color); font-weight: bold; cursor: pointer; box-shadow: 2px 2px 0 var(--main-color); }
        .btn:active { box-shadow: 0 0 0; transform: translate(2px, 2px); }
        
        .btn-back { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #555; }
        
        .alert { background: #d4edda; color: #155724; padding: 10px; border: 2px solid #155724; margin-bottom: 20px; font-weight: bold; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <h1>Mon Profil</h1>
    
    <?php if ($message): ?>
        <div class="alert"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Nom complet</label>
        <input type="text" name="nom" value="<?= htmlspecialchars(is_array($user) ? ($user['nom'] ?? '') : ($user->nom ?? '')) ?>" required>

        <label>Adresse Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars(is_array($user) ? ($user['email'] ?? '') : ($user->email ?? '')) ?>" required>

        <label>Nouveau mot de passe (laisser vide pour ne pas changer)</label>
        <input type="password" name="password" placeholder="********">

        <button type="submit" class="btn">Enregistrer les modifications</button>
    </form>

    <a href="index.php?route=dashboard" class="btn-back">Retour au Dashboard</a>
</div>

</body>
</html>