/**
 * BMI Calculator JavaScript
 *
 * @package ToolZoo
 */

document.addEventListener('DOMContentLoaded', function() {
    initBMICalculator();
});

/**
 * Initialize BMI Calculator
 */
function initBMICalculator() {
    setupEventListeners();
}

/**
 * Setup Event Listeners
 */
function setupEventListeners() {
    // Calculate button
    const calculateBtn = document.getElementById('toolzoo-bmi-calculate-btn');
    if (calculateBtn) {
        calculateBtn.addEventListener('click', calculateBMI);
    }

    // Height input field
    const heightInput = document.getElementById('toolzoo-bmi-height');
    if (heightInput) {
        heightInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                calculateBMI();
            }
        });
        heightInput.addEventListener('blur', function() {
            constrainValue(this, 100, 250);
        });
    }

    // Weight input field
    const weightInput = document.getElementById('toolzoo-bmi-weight');
    if (weightInput) {
        weightInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                calculateBMI();
            }
        });
        weightInput.addEventListener('blur', function() {
            constrainValue(this, 20, 200);
        });
    }
}

/**
 * Calculate BMI and display results
 */
function calculateBMI() {
    const heightCm = parseFloat(document.getElementById('toolzoo-bmi-height').value);
    const weightKg = parseFloat(document.getElementById('toolzoo-bmi-weight').value);

    // Validate input
    if (!validateInput(heightCm, weightKg)) {
        const errorMsg = (typeof toolzooBmiL10n !== 'undefined' && toolzooBmiL10n.invalidInput)
            ? toolzooBmiL10n.invalidInput
            : 'Height must be between 100-250 cm and weight between 20-200 kg.';
        showError(errorMsg);
        return;
    }

    // Calculate BMI
    const heightM = heightCm / 100;
    const bmi = weightKg / (heightM * heightM);

    // Get category
    const category = getCategory(bmi);

    // Calculate ideal weight range
    const minIdealWeight = heightM * heightM * 18.5;
    const maxIdealWeight = heightM * heightM * 25;

    // Display results
    displayResults(bmi, category, minIdealWeight, maxIdealWeight);
}

/**
 * Validate input values
 *
 * @param {number} height Height in cm
 * @param {number} weight Weight in kg
 * @return {boolean} True if valid
 */
function validateInput(height, weight) {
    return !isNaN(height) && !isNaN(weight) &&
           height >= 100 && height <= 250 &&
           weight >= 20 && weight <= 200;
}

/**
 * Get BMI category
 *
 * @param {number} bmi BMI value
 * @return {string} Category name
 */
function getCategory(bmi) {
    if (bmi < 18.5) {
        return 'underweight';
    } else if (bmi < 25) {
        return 'normal';
    } else if (bmi < 30) {
        return 'overweight';
    } else {
        return 'obese';
    }
}

/**
 * Get category label text
 *
 * @param {string} category Category key
 * @return {string} Category label
 */
function getCategoryLabel(category) {
    // Try to use localized strings if available
    if (typeof toolzooBmiL10n !== 'undefined' && toolzooBmiL10n[category]) {
        return toolzooBmiL10n[category];
    }

    // Fallback to English labels
    const labels = {
        'underweight': 'Underweight',
        'normal': 'Normal Weight',
        'overweight': 'Overweight',
        'obese': 'Obese'
    };
    return labels[category] || category;
}

/**
 * Display results
 *
 * @param {number} bmi BMI value
 * @param {string} category Category name
 * @param {number} minWeight Min ideal weight
 * @param {number} maxWeight Max ideal weight
 */
function displayResults(bmi, category, minWeight, maxWeight) {
    // Update BMI value
    const bmiValueEl = document.getElementById('toolzoo-bmi-value');
    if (bmiValueEl) {
        bmiValueEl.textContent = bmi.toFixed(1);
    }

    // Update category
    const categoryEl = document.getElementById('toolzoo-bmi-category');
    if (categoryEl) {
        categoryEl.textContent = getCategoryLabel(category);
        // Remove all category classes
        categoryEl.className = 'toolzoo-bmi-category';
        // Add specific category class
        categoryEl.classList.add(category);
    }

    // Update ideal weight
    const idealWeightEl = document.getElementById('toolzoo-bmi-ideal-weight');
    if (idealWeightEl) {
        idealWeightEl.textContent = minWeight.toFixed(1) + ' kg ~ ' + maxWeight.toFixed(1) + ' kg';
    }

    // Show results container
    const resultsContainer = document.getElementById('toolzoo-bmi-results-container');
    if (resultsContainer) {
        resultsContainer.style.display = 'block';
    }

    // Hide error message
    hideError();
}

/**
 * Show error message
 *
 * @param {string} message Error message
 */
function showError(message) {
    const errorDiv = document.getElementById('toolzoo-bmi-error-message');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }

    // Hide results container
    const resultsContainer = document.getElementById('toolzoo-bmi-results-container');
    if (resultsContainer) {
        resultsContainer.style.display = 'none';
    }
}

/**
 * Hide error message
 */
function hideError() {
    const errorDiv = document.getElementById('toolzoo-bmi-error-message');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

/**
 * Constrain input value to min/max range
 *
 * @param {HTMLInputElement} input Input element
 * @param {number} min Minimum value
 * @param {number} max Maximum value
 */
function constrainValue(input, min, max) {
    let value = parseFloat(input.value);

    if (isNaN(value)) {
        input.value = min;
        return;
    }

    if (value < min) {
        input.value = min;
    } else if (value > max) {
        input.value = max;
    }
}
