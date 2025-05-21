<?php
// Simple death date calculator

// Process form submission
$deathDate = null;
$message = '';
$birthdate = '';
$gender = '';
$country = '';
$health = 'average';
$smoker = 'no';
$risk = 'no';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $birthdate = $_POST['birthdate'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $country = $_POST['country'] ?? '';
    $health = $_POST['health'] ?? 'average';
    $smoker = $_POST['smoker'] ?? 'no';
    $risk = $_POST['risk'] ?? 'no';
    
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
                
                // Enhanced life expectancy algorithm:
                // 1. Base expectancy by location and gender
                $baseExpectancyByCountry = [
                    'Netherlands' => 82, 'United States' => 78,
                    'Japan' => 84, 'India' => 69, 'Germany' => 81
                ];
                $base = $baseExpectancyByCountry[$country] ?? 80;
                if ($gender === 'female') {
                    $base += 3;
                } elseif ($gender === 'male') {
                    $base -= 2;
                }
                // 2. Health factor adjustment
                $healthAdj = ['poor' => -5, 'average' => 0, 'good' => 5][$health] ?? 0;
                // 3. Smoking
                $smokeAdj = ($smoker === 'yes') ? -10 : 3;
                // 4. Risk activities
                $riskAdj = ($risk === 'yes') ? -7 : 0;
                // 5. Advances in medicine: add small buffer
                $medicineAdv = 2;
                // Compute adjusted lifespan
                $adjustedLifespan = $base + $healthAdj + $smokeAdj + $riskAdj + $medicineAdv;
                
                // Remaining years (rough estimate)
                $remainingYears = $adjustedLifespan - $currentAge;
                
                if ($remainingYears <= 0) {
                    $message = "You've exceeded your personalized expectancy—enjoy your bonus time!";
                    $deathDate = "Bonus life!";
                } else {
                    // Calculate hypothetical death date using adjusted lifespan
                    $deathDateObj = clone $birth;
                    $deathDateObj->add(new DateInterval("P{$adjustedLifespan}Y"));
                    
                    // Format the death date
                    $deathDate = $deathDateObj->format('F j, Y');
                    $message = "Based on personalized factors, your hypothetical departure date would be:";
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
            color: #e30000;
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
        input[type="date"], input[type="text"], select {
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
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="country">Country:</label>
                <input type="text" id="country" name="country" placeholder="e.g. Netherlands" value="<?php echo htmlspecialchars($country); ?>" required>
            </div>
            <div class="form-group">
                <label for="health">Health Status:</label>
                <select id="health" name="health">
                    <option value="poor">Poor</option>
                    <option value="average" selected>Average</option>
                    <option value="good">Good</option>
                </select>
            </div>
            <div class="form-group">
                <label>Smoker?</label>
                <label><input type="radio" name="smoker" value="no" checked> No</label>
                <label><input type="radio" name="smoker" value="yes"> Yes</label>
            </div>
            <div class="form-group">
                <label>High-risk lifestyle activities?</label>
                <label><input type="radio" name="risk" value="no" checked> No</label>
                <label><input type="radio" name="risk" value="yes"> Yes</label>
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
            <p>Disclaimer: For entertainment only. This personalized calculation uses statistical averages adjusted for gender, country-based expectancy, health status, smoking habits, risk activities, and expected medical advances. It does not guarantee actual outcomes.</p>
        </div>
    </div>
</body>
</html>