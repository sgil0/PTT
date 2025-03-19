<?php
//session_start();
// Rediriger vers l'accueil si l'utilisateur n'est pas connecté
if (!isset($_SESSION['idUser'])) {
    header("Location: index.php?view=accueil");
    exit();
}

// ----------------------
// 1) PARAMÈTRES DE BASE
// ----------------------
$startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
$selectedDate = isset($_GET['day']) ? $_GET['day'] : $startDate;

$days = [];
for ($i = 0; $i < 7; $i++) {
    $days[] = date('Y-m-d', strtotime("$startDate + $i days"));
}

$prevStart = date('Y-m-d', strtotime("$startDate - 7 days"));
$nextStart = date('Y-m-d', strtotime("$startDate + 7 days"));

// -------------------------
// 2) CRÉNEAUX HORAIRES
// -------------------------
// On génère les créneaux de 09:00 à 19:00 par tranche de 15 minutes
$timeSlots = [
    'Matin'       => [],
    'Après-midi'  => [],
    'Soir'        => []
];

$startTime = new DateTime('09:00');
$endTime   = new DateTime('19:00');
$interval  = new DateInterval('PT15M');
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
    'EEE d MMM'
);
$formatterSelected = new IntlDateFormatter(
    'fr_FR',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'Europe/Paris',
    IntlDateFormatter::GREGORIAN,
    'EEEE d MMMM yyyy'
);

// -------------------------
// 5) RÉCUPÉRATION DES CRÉNEAUX RÉSERVÉS
// -------------------------
$reservedAppointments = getRendezVousByDate($selectedDate);
$reservedTimes = [];
if ($reservedAppointments) {
    foreach ($reservedAppointments as $appointment) {
        $reservedTimes[] = $appointment['time'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Planification d'appel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <!-- jQuery pour faciliter l'AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-50">
<div class="container mx-auto p-6 bg-white shadow-md mt-6 rounded">

    <!-- Titre et description -->
    <h1 class="text-2xl font-bold mb-2">Prenez rendez-vous</h1>
    <p class="mb-4 text-gray-700">
        Planifiez un appel personnalisé avec notre équipe d'experts pour discuter de vos besoins et obtenir des conseils sur mesure. Gratuit • 15 minutes
    </p>

    <!-- Navigation : semaine précédente / jours / semaine suivante -->
    <div class="flex justify-between items-center mb-4">
        <a href="?view=planning&start=<?= $prevStart ?>" class="px-4 py-2 bg-gray-200 rounded-md shadow-md hover:bg-gray-300">&lt;</a>
        <div class="flex space-x-2">
            <?php foreach ($days as $day): 
                $isSelected = ($day === $selectedDate);
                $label = $formatterDays->format(new DateTime($day));
            ?>
                <a href="?view=planning&start=<?= $startDate ?>&day=<?= $day ?>"
                   class="px-4 py-2 rounded-md shadow-md <?= $isSelected ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' ?>">
                    <?= ucfirst($label) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <a href="?view=planning&start=<?= $nextStart ?>" class="px-4 py-2 bg-gray-200 rounded-md shadow-md hover:bg-gray-300">&gt;</a>
    </div>

    <!-- Affichage de la date sélectionnée -->
    <h2 class="text-xl font-semibold mb-4"><?= ucfirst($formatterSelected->format(new DateTime($selectedDate))) ?></h2>

    <!-- Section des créneaux horaires -->
    <div id="timeSlots">
        <?php foreach ($timeSlots as $periodLabel => $slots): ?>
            <div class="mb-6">
                <h4 class="text-lg font-semibold mb-2"><?= $periodLabel ?></h4>
                <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
                    <?php foreach ($slots as $time): 
                        $isReserved = in_array($time, $reservedTimes);
                    ?>
                        <button type="button"
                                class="time-slot px-4 py-2 rounded shadow <?php 
                                    if ($isReserved) {
                                        echo 'bg-gray-300 text-gray-600 cursor-not-allowed';
                                    } else {
                                        echo 'bg-gray-100 hover:bg-gray-200';
                                    }
                                ?>"
                                value="<?= $time ?>"
                                <?= $isReserved ? "disabled" : "" ?>>
                            <?= $time ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Zone d'affichage de la sélection et de la description -->
    <div id="selectedInfo" class="mt-6 p-4 bg-gray-100 rounded shadow-md" style="display:none;">
        <p class="mb-2" id="selectedDisplay"></p>
        <div class="mb-2">
            <label for="rdvDescription" class="block text-gray-700 mb-1">Description du rendez-vous :</label>
            <textarea id="rdvDescription" rows="3" class="w-full border rounded p-2" placeholder="Entrez ici la description du rendez-vous"></textarea>
        </div>
        <button id="confirmButton" class="px-4 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600">Confirmer le rendez-vous</button>
    </div>

    <!-- Champs cachés pour stocker la sélection -->
    <input type="hidden" id="hiddenSelectedTime" value="">
    <input type="hidden" id="hiddenSelectedDate" value="<?= $selectedDate ?>">

    <!-- Zone de message de confirmation -->
    <div id="confirmationMessage" class="mt-4"></div>

    <?php if (isset($_GET['confirmation']) && $_GET['confirmation'] == 'ok'): ?>
        <div class="p-2 bg-green-100 text-green-700 rounded">
            Votre rendez-vous a bien été enregistré !
        </div>
    <?php endif; ?>

</div>

<script>
$(document).ready(function() {
    // Lorsqu'un créneau est cliqué
    $('.time-slot').on('click', function(e) {
        e.preventDefault();
        var selectedTime = $(this).val();
        $('#hiddenSelectedTime').val(selectedTime);
        $('#selectedDisplay').html("Créneau sélectionné : <strong>" + selectedTime + "</strong> le <strong><?= date('d/m/Y', strtotime($selectedDate)); ?></strong>");
        $('#selectedInfo').fadeIn();

        // Mise en surbrillance du créneau sélectionné
        $('.time-slot').removeClass('bg-blue-500 text-white').addClass('bg-gray-100 hover:bg-gray-200');
        $(this).removeClass('bg-gray-100 hover:bg-gray-200').addClass('bg-blue-500 text-white');
    });

    // Envoi en AJAX lors de la confirmation, avec description
    $('#confirmButton').on('click', function(e) {
        e.preventDefault();
        var selectedTime = $('#hiddenSelectedTime').val();
        var selectedDate = $('#hiddenSelectedDate').val();
        var description = $('#rdvDescription').val(); // Récupère la description saisie
        if (!selectedTime) {
            $('#confirmationMessage').html("<div class='p-2 bg-red-100 text-red-700 rounded'>Veuillez sélectionner un créneau.</div>");
            return;
        }
        // Désactiver le bouton pour éviter le spam
        $('#confirmButton').prop('disabled', true);
        $.ajax({
            url: 'controleur.php',
            type: 'POST',
            data: {
                action: 'confirmer_rdv',
                selectedTime: selectedTime,
                day: selectedDate,
                description: description
            },
            success: function(response) {
                $('#confirmationMessage').html("<div class='p-2 bg-green-100 text-green-700 rounded'>Votre rendez-vous a bien été enregistré !</div>");
                $('#selectedInfo').fadeOut();
                // Rafraîchir dynamiquement les créneaux pour mettre à jour les disponibilités
                $.ajax({
                    url: '<?= basename(__FILE__); ?>',
                    type: 'GET',
                    data: {
                        ajax: 1,
                        day: selectedDate,
                        start: '<?= $startDate ?>'
                    },
                    success: function(data) {
                        $('#timeSlots').html(data);
                    }
                });
            },
            error: function(xhr, status, error) {
                $('#confirmationMessage').html("<div class='p-2 bg-red-100 text-red-700 rounded'>Une erreur est survenue : " + error + "</div>");
                $('#confirmButton').prop('disabled', false);
            }
        });
    });
});
</script>
</body>
</html>
