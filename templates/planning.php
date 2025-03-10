<?php
// ----------------------
// 1) PARAMÈTRES DE BASE
// ----------------------

// Récupération des paramètres GET pour la navigation
$startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
$selectedDate = isset($_GET['day']) ? $_GET['day'] : $startDate;

// Génération d'un tableau de 7 jours à partir de $startDate
$days = [];
for ($i = 0; $i < 7; $i++) {
    $days[] = date('Y-m-d', strtotime("$startDate + $i days"));
}

// Calcul des dates de la semaine précédente / suivante
$prevStart = date('Y-m-d', strtotime("$startDate - 7 days"));
$nextStart = date('Y-m-d', strtotime("$startDate + 7 days"));

// -------------------------
// 2) CRÉNEAUX HORAIRES
// -------------------------
// On génère toutes les 15 minutes entre 08:00 et 19:00
// puis on répartit dans 3 groupes : Matin, Après-midi, Soir.
$timeSlots = [
    'Matin'       => [],
    'Après-midi'  => [],
    'Soir'        => []
];

$startTime = new DateTime('09:00');
$endTime   = new DateTime('19:00');
$interval  = new DateInterval('PT15M');  // incrément de 15 minutes
$period    = new DatePeriod($startTime, $interval, $endTime);

foreach ($period as $time) {
    $hour = (int)$time->format('H');
    $formatted = $time->format('H:i');
    if ($hour < 12) {
        $timeSlots['Matin'][] = $formatted;
    } elseif ($hour < 16) {
        $timeSlots['Après-midi'][] = $formatted;
    } else {
        $timeSlots['Soir'][] = $formatted;
    }
}

// -------------------------
// 3) GESTION DE LA SÉLECTION
// -------------------------
$selectedTime = isset($_POST['selectedTime']) ? $_POST['selectedTime'] : null;

// -------------------------
// 4) FORMATAGE DES DATES (Intl)
// -------------------------
$formatterDays = new IntlDateFormatter(
    'fr_FR',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'Europe/Paris',
    IntlDateFormatter::GREGORIAN,
    'EEE d MMM' // ex. "lun. 10 mars"
);

$formatterSelected = new IntlDateFormatter(
    'fr_FR',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'Europe/Paris',
    IntlDateFormatter::GREGORIAN,
    'EEEE d MMMM yyyy' // ex. "lundi 10 mars 2025"
);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Planification d'appel</title>
    <!-- Import TailwindCSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
<div class="container mx-auto p-6 bg-white shadow-md mt-6 rounded">

    <!-- Titre et description -->
    <h1 class="text-2xl font-bold mb-2">Prenez rendez-vous</h1>
    <p class="mb-4 text-gray-700">
        Planifiez un appel personnalisé avec notre équipe d'experts pour discuter de vos besoins 
        et obtenir des conseils sur mesure. Gratuit • 15 minutes
    </p>

    <!-- Navigation semaine précédente / suivante -->
    <div class="flex justify-between items-center mb-4">
        <!-- Lien pour la semaine précédente -->
        <a href="?view=planning&start=<?= $prevStart ?>"
           class="px-4 py-2 bg-gray-200 rounded-md shadow-md hover:bg-gray-300">
           &lt;
        </a>

        <!-- Liste des 7 jours (horizontal) -->
        <div class="flex space-x-2">
            <?php foreach ($days as $day): ?>
                <?php
                    $isSelected = ($day === $selectedDate);
                    // Exemple : "lun. 10 mars"
                    $label = $formatterDays->format(new DateTime($day));
                ?>
                <a href="?view=planning&start=<?= $startDate ?>&day=<?= $day ?>"
                   class="px-4 py-2 rounded-md shadow-md 
                   <?= $isSelected ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' ?>">
                    <?= ucfirst($label) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Lien pour la semaine suivante -->
        <a href="?view=planning&start=<?= $nextStart ?>"
           class="px-4 py-2 bg-gray-200 rounded-md shadow-md hover:bg-gray-300">
           &gt;
        </a>
    </div>

    <!-- Titre du jour sélectionné (ex: "lundi 10 mars 2025") -->
    <h2 class="text-xl font-semibold mb-4">
        <?= ucfirst($formatterSelected->format(new DateTime($selectedDate))) ?>
    </h2>

    <!-- Formulaire pour sélectionner un créneau horaire -->
    <form method="post" action="?view=planning&start=<?= $startDate ?>&day=<?= $selectedDate ?>">
        
        <!-- Parcours de chaque groupe (Matin, Après-midi, Soir) -->
        <?php foreach ($timeSlots as $periodLabel => $slots): ?>
            <div class="mb-6">
                <h4 class="text-lg font-semibold mb-2"><?= $periodLabel ?></h4>
                <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
                    <?php foreach ($slots as $time): ?>
                        <button type="submit"
                                name="selectedTime"
                                value="<?= $time ?>"
                                class="px-4 py-2 rounded shadow
                                <?= ($selectedTime === $time)
                                     ? 'bg-blue-500 text-white'
                                     : 'bg-gray-100 hover:bg-gray-200'
                                ?>">
                            <?= $time ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </form>

    <!-- Affichage du créneau sélectionné et bouton de confirmation -->
    <?php if ($selectedTime): ?>
    <!-- Formulaire de confirmation du rendez-vous -->
    <form method="post" action="?view=planning&action=confirmer_rdv&start=<?= $startDate ?>&day=<?= $selectedDate ?>">
        <div class="mt-6 p-4 bg-gray-100 rounded shadow-md">
            <?php 
                // Format d'affichage "dd/mm/yyyy" pour la date
                $selectedDateFormatted = date('d/m/Y', strtotime($selectedDate));
            ?>
            <p class="mb-2">
                Créneau sélectionné : 
                <strong><?= $selectedDateFormatted ?></strong> 
                à <strong><?= $selectedTime ?></strong>
            </p>
            <!-- Transmet le créneau et la date en POST -->
            <input type="hidden" name="selectedTime" value="<?= $selectedTime ?>">
            <input type="hidden" name="day" value="<?= $selectedDate ?>">
            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600">
                Confirmer le rendez-vous
            </button>
        </div>
    </form>
<?php endif; ?>


</div>
</body>
</html>
