<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/public/styles/main.css" rel="stylesheet">
    <link href="/public/styles/profile.css" rel="stylesheet">
    <link href="/public/styles/navbar.css" rel="stylesheet">
    <script src="/public/js/flights.js" defer></script>
    <script src="https://kit.fontawesome.com/8fd9367667.js" crossorigin="anonymous"></script>
    <title>Profile</title>
</head>
<body>
    <nav>
        <?php include 'navbar.php'; ?>
    </nav>

    <div class="profile-container">
        <div class="logbook-section">
            <h2>Add Flight</h2>
            <form class="flight-form" method="POST" action="/add-flight">
                <input type="text" name="departure_airport" placeholder="Departure Airport ICAO" oninput="this.value = this.value.toUpperCase();" required>
                <input type="text" name="landing_airport" placeholder="Landing Airport ICAO" oninput="this.value = this.value.toUpperCase();" required>
                <input type="text" name="aircraft" placeholder="Aircraft" required oninput="this.value = this.value.toUpperCase();">
                <input type="datetime-local" name="departure_time" required>
                <input type="datetime-local" name="landing_time" required>
                <button type="submit">Add Flight</button>
            </form>
    
            <h2>Your Flights</h2>

            <div id="flights-container">
                <?php if (!empty($flights)): ?>
                    <?php foreach ($flights as $index => $flight): ?>
                        <div class="flight-card <?php echo $index >= 3 ? 'hidden-flight' : ''; ?>" data-id="<?php echo $flight->getId(); ?>">
                            <div class="flight-header">
                                <p><strong>Aircraft:</strong> <?php echo htmlspecialchars($flight->getAircraft()); ?></p>
                                
                                <button class="delete-btn" onclick="deleteFlight(<?php echo $flight->getId(); ?>)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                            <p><strong>From:</strong> <?php echo htmlspecialchars($flight->getDepartureAirport()); ?></p>
                            <p><strong>To:</strong> <?php echo htmlspecialchars($flight->getLandingAirport()); ?></p>
                            <p><strong>Flight Time:</strong> <?php echo htmlspecialchars($flight->getFlightTime()); ?> minutes</p>
                            <p><strong>Departure:</strong> <?php echo htmlspecialchars($flight->getDepartureTime()); ?></p>
                            <p><strong>Landing:</strong> <?php echo htmlspecialchars($flight->getLandingTime()); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No flights yet.</p>
                <?php endif; ?>
            </div>
            
          
            <?php if (count($flights) > 3): ?>
                <button id="show-more-btn" onclick="showMoreFlights()">
                    Show more (<?php echo count($flights) - 3; ?>)
                </button>
            <?php endif; ?>
            
            
        </div>
        
        <div class="profile-section">
            <div class="avatar-section">
                <img src="<?= htmlspecialchars($user->getAvatar()); ?>" alt="Avatar" class="profile-avatar">
                <form method="POST" action="/upload-avatar" enctype="multipart/form-data">
                    <input type="file" id="avatar-upload" name="avatar" accept="image/*" hidden required>
                    <label for="avatar-upload" class="upload-button">Choose Avatar</label>
                    <button type="submit" class="upload-avatar-btn">Change Avatar</button>
                </form>
            </div>

         
            <div class="user-info">
                <p><strong>Nick:</strong> <?= htmlspecialchars($user->getNickname()); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user->getEmail()); ?></p>                
                <p><strong>Total flight time:</strong> <?php echo htmlspecialchars($totalFlightTime ?? '0:00'); ?> hours</p>
                <p><strong>Favourite aircraft:</strong> <?php echo htmlspecialchars($favouriteAircraft ?? ''); ?></p>
                <p><strong>Favourite airport:</strong> <?php echo htmlspecialchars($favouriteAirport ?? ''); ?></p>
            </div>
        </div>
    </div>
    
</body>
</html>
