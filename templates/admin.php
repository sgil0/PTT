<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../libs/maLibUtils.php';
require_once __DIR__ . '/../libs/maLibSQL.pdo.php';
require_once __DIR__ . '/../libs/maLibSecurisation.php';
require_once __DIR__ . '/../libs/modele.php';

if (!isset($_SESSION['idUser']) || !isUserAdmin($_SESSION['idUser'])) {
    header("Location: ../index.php");
    exit();
}

// Initialisation sécurisée de $pdo
if (!isset($pdo)) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=baseclients;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        die("Erreur PDO : " . $e->getMessage());
    }
}

// Changement de rôle si demande post
if (isset($_POST['toggle_role'], $_POST['id_utilisateur'])) {
    $id = intval($_POST['id_utilisateur']);

    $stmt = $pdo->prepare("SELECT role FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->execute([$id]);
    $role = $stmt->fetchColumn();

    $newRole = ($role === 'administrateur') ? 'utilisateur' : 'administrateur';
    $update = $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id_utilisateur = ?");
    $update->execute([$newRole, $id]);
}

// Récupérer les utilisateurs
$users = $pdo->query("SELECT id_utilisateur, prenom, nom, email, role FROM utilisateurs")->fetchAll();
?>

<div class="container mt-5">
    <h2 class="mb-4">Gestion des utilisateurs</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['prenom'] . " " . $user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <span class="badge <?= $user['role'] === 'administrateur' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($user['id_utilisateur'] != $_SESSION['idUser']): ?>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="id_utilisateur" value="<?= $user['id_utilisateur'] ?>">
                                <button type="submit" name="toggle_role"
                                        class="btn btn-sm <?= $user['role'] === 'administrateur' ? 'btn-danger' : 'btn-primary' ?>">
                                    <?= $user['role'] === 'administrateur' ? 'Rétrograder' : 'Promouvoir' ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">(Vous)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    h1, h2 {
  text-align: center;
  color: #d96c2c;      /* Couleur orange soutenue */
  font-weight: bold;
  margin-top: 30px;
  margin-bottom: 20px;
}
</style>