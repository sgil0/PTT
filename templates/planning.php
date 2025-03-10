<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elpis - Prise de Rendez-vous</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: white;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #f57c00;
        }
        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: black;
        }
        .active {
            font-weight: bold;
            border-bottom: 2px solid #f57c00;
        }
        .login-btn {
            background-color: #f57c00;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        .appointment-container {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .calendar, .user-details {
            margin-top: 20px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            margin-top: 15px;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    
    <main>
        <h1 style="text-align: center;">Prenez rendez-vous</h1>
        <section class="appointment-container">
            <div class="appointment-details">
                <h2>Appel de consultation</h2>
                <p>Planifiez un appel personnalisé avec notre équipe d'experts pour discuter de vos besoins.</p>
                <p>Gratuit • 15 minutes</p>
            </div>
            <div class="calendar">
                <label for="date">Choisissez un jour :</label>
                <input type="date" id="date" name="date">
                <label for="time">Choisissez une heure :</label>
                <select id="time" name="time">
                    <option value="09:00">09:00</option>
                    <option value="09:15">09:15</option>
                    <option value="09:30">09:30</option>
                    <option value="09:45">09:45</option>
                    <option value="10:00">10:00</option>
                    <option value="10:15">10:15</option>
                    <option value="10:30">10:30</option>
                    <option value="10:45">10:45</option>
                    <option value="11:00">11:00</option>
                </select>
            </div>
            <div class="user-details">
                <h3>Entrer les détails</h3>
                <label for="name">Nom :</label>
                <input type="text" id="name" name="name" placeholder="Votre nom">
                <label for="phone">Numéro de téléphone :</label>
                <input type="tel" id="phone" name="phone" placeholder="Votre téléphone">
                <label for="email">E-mail :</label>
                <input type="email" id="email" name="email" placeholder="Votre email">
                <button type="submit">Confirmer le rendez-vous</button>
            </div>
        </section>
    </main>
    <script>
        document.getElementById('date').addEventListener('change', function() {
            console.log('Date sélectionnée :', this.value);
        });
        document.getElementById('time').addEventListener('change', function() {
            console.log('Heure sélectionnée :', this.value);
        });
    </script>
</body>
</html>
