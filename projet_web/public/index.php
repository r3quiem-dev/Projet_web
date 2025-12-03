<?php
$route = $_GET['route'] ?? 'login';

switch ($route) {
    // AUTH
    case 'login':
        require_once __DIR__ . '/../src/controllers/AuthController.php';
        (new AuthController())->login();
        break;
    case 'logout':
        require_once __DIR__ . '/../src/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    // DASHBOARD
    case 'dashboard':
        require_once __DIR__ . '/../src/controllers/DashboardController.php';
        (new DashboardController())->index();
        break;

    // GESTION QUIZ (Create/Edit/Delete)
    case 'create_quiz':
        require_once __DIR__ . '/../src/controllers/QuizController.php';
        (new QuizController())->create();
        break;
    case 'store_quiz':
        require_once __DIR__ . '/../src/controllers/QuizController.php';
        (new QuizController())->store();
        break;
    case 'edit_quiz':
        require_once __DIR__ . '/../src/controllers/QuizController.php';
        (new QuizController())->edit();
        break;
    case 'update_quiz':
        require_once __DIR__ . '/../src/controllers/QuizController.php';
        (new QuizController())->update();
        break;
    case 'delete_quiz':
        require_once __DIR__ . '/../src/controllers/QuizController.php';
        (new QuizController())->delete();
        break;

    // ESPACE ÉLÈVE
    case 'play_quiz':
        require_once __DIR__ . '/../src/controllers/PlayerController.php';
        (new PlayerController())->play();
        break;

    case 'submit_quiz':
        require_once __DIR__ . '/../src/controllers/PlayerController.php';
        (new PlayerController())->submit();
        break;

    case 'publish_quiz':
        require_once __DIR__ . '/../src/controllers/QuizController.php';
        (new QuizController())->publish();
        break;

    case 'results_quiz':
        require_once __DIR__ . '/../src/controllers/QuizController.php';
        (new QuizController())->results();
        break;
    
    case 'profile':
        require_once __DIR__ . '/../src/controllers/ProfileController.php';
        (new ProfileController())->edit();
        break;

    // ERREUR
    default:
        echo "<h1>404 - Page non trouvée</h1><a href='index.php'>Retour</a>";
        break;
}
?>