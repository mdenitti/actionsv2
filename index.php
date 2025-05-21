<?php
// Simple death date calculator

// Process form submission
$deathDate = null;
$message = '';
$birthdate = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $birthdate = $_POST['birthdate'] ?? '';
    
    if (empty($birthdate)) {
        $message = "Please enter your birthdate";
    } else {
        try {
            // Validate date format
            $birth = new DateTime($birthdate);
            $now = new DateTime();
            
            // Basic validation - birthdate should be in the past
            if ($birth > $now) {
                $message = "Birthdate cannot be in the future";
            } else {
                // Calculate age
                $age = $now->diff($birth);
                $currentAge = $age->y;
                
                // Average life expectancy (assumed to be around 80 years)
                $averageLifespan = 80;
                
                // Remaining years (rough estimate)
                $remainingYears = $averageLifespan - $currentAge;
                
                if ($remainingYears <= 0) {
                    $message = "Based on average statistics, you've already exceeded the average lifespan. Congratulations!";
                    $deathDate = "You're living bonus time!";
                } else {
                    // Calculate hypothetical death date
                    $deathDateObj = clone $birth;
                    $deathDateObj->add(new DateInterval("P{$averageLifespan}Y"));
                    
                    // Format the death date
                    $deathDate = $deathDateObj->format('F j, Y');
                    $message = "Based on an average lifespan of {$averageLifespan} years, your hypothetical departure date would be:";
                }
            }
        } catch (Exception $e) {
            $message = "Please enter a valid date format";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life Expectancy Calculator</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #4a4a4a;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #333;
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-left: 4px solid #4a4a4a;
        }
        .disclaimer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Life Expectancy Calculator</h1>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="birthdate">Enter your birthdate:</label>
                <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($birthdate); ?>" required>
            </div>
            
            <button type="submit">Calculate</button>
        </form>
        
        <?php if($message): ?>
        <div class="result">
            <p><?php echo htmlspecialchars($message); ?></p>
            <?php if($deathDate): ?>
                <h2><?php echo htmlspecialchars($deathDate); ?></h2>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="disclaimer">
            <p>Disclaimer: This is for entertainment purposes only. The calculation is based on simple statistics and does not take into account personal health factors, lifestyle choices, accidents, advances in medicine, or countless other variables that influence actual life expectancy.</p>
        </div>
    </div>
</body>
</html>