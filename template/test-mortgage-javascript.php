<?php
/**
 * Template Name1: Testing
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 16/12/15
 * Time: 3:27 PM
 */
get_header(); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h1>Mortgage Calculator</h1>
<form id="calculator-form">
    <div>
        <label for="homePrice">Home Price ($):</label>
        <input type="number" id="homePrice" value="200000">
    </div>
    <div>
        <label for="downPaymentPercentage">Down Payment (%):</label>
        <input type="number" id="downPaymentPercentage" value="10">
    </div>
    <div>
        <label for="annualInterestRate">Annual Interest Rate (%):</label>
        <input type="number" id="annualInterestRate" step="0.01" value="3.5">
    </div>
    <div>
        <label for="loanTermInYears">Loan Term (Years):</label>
        <input type="number" id="loanTermInYears" value="30">
    </div>
    <div>
        <label for="annualPropertyTaxRate">Annual Property Tax Rate (%):</label>
        <input type="number" id="annualPropertyTaxRate" step="0.01" value="1.2">
    </div>
    <div>
        <label for="annualHomeInsurance">Annual Home Insurance ($):</label>
        <input type="number" id="annualHomeInsurance">
    </div>
    <div>
        <label for="monthlyHOAFees">Monthly HOA Fees ($):</label>
        <input type="number" id="monthlyHOAFees">
    </div>
    <div>
        <label for="pmi">Private Mortgage Insurance (%):</label>
        <input type="number" id="pmi" step="0.01">
    </div>
</form>
<div id="results" style="margin-top: 20px; float: left;"></div>
<canvas id="chart" width="400" height="400"></canvas>

<script>
    function calculateMonthlyPayment(principal, annualInterestRate, loanTermInYears) {
        const monthlyInterestRate = annualInterestRate / 12 / 100;
        const numberOfMonths = loanTermInYears * 12;

        if (monthlyInterestRate === 0) {
            return principal / numberOfMonths;
        }

        return principal * (monthlyInterestRate * Math.pow((1 + monthlyInterestRate), numberOfMonths)) / (Math.pow((1 + monthlyInterestRate), numberOfMonths) - 1);
    }

    function updateResults() {
        const homePrice = parseFloat(document.getElementById('homePrice').value) || 0;
        const downPaymentPercentage = parseFloat(document.getElementById('downPaymentPercentage').value) || 0;
        const annualInterestRate = parseFloat(document.getElementById('annualInterestRate').value) || 0;
        const loanTermInYears = parseFloat(document.getElementById('loanTermInYears').value) || 0;
        const annualPropertyTaxRate = parseFloat(document.getElementById('annualPropertyTaxRate').value) || 0;
        const annualHomeInsurance = parseFloat(document.getElementById('annualHomeInsurance').value) || 0;
        const monthlyHOAFees = parseFloat(document.getElementById('monthlyHOAFees').value) || 0;
        const pmi = parseFloat(document.getElementById('pmi').value) || 0;

        const downPayment = homePrice * (downPaymentPercentage / 100);
        const principal = homePrice - downPayment;       
        const monthlyPayment = calculateMonthlyPayment(principal, annualInterestRate, loanTermInYears);
        const monthlyPropertyTax = (homePrice * (annualPropertyTaxRate / 100)) / 12;
        const monthlyHomeInsurance = annualHomeInsurance / 12;
        
        // Check if PMI is required
        const pmiRequired = (downPayment / homePrice) < 0.2;

        const monthlyPMI = pmiRequired ? (principal * (pmi / 100)) / 12 : 0;
        
        const totalMonthlyPayment = monthlyPayment + monthlyPropertyTax + monthlyHomeInsurance + monthlyHOAFees + monthlyPMI;

        const resultsElement = document.getElementById('results');
        resultsElement.innerHTML = `
            <p>Down Payment: $${downPayment.toFixed(2)}</p>
            <p>Loan Amount: $${principal.toFixed(2)}</p>
            <p>Monthly Mortgage Payment: $${monthlyPayment.toFixed(2)}</p>
            <p>Monthly Property Tax: $${monthlyPropertyTax.toFixed(2)}</p>
            <p>Monthly Home Insurance: $${monthlyHomeInsurance.toFixed(2)}</p>
            <p>Monthly HOA Fees: $${monthlyHOAFees.toFixed(2)}</p>
            ${pmiRequired ? `<p>Monthly PMI: $${monthlyPMI.toFixed(2)}</p>` : ''}
            <p>Total Monthly Payment: $${totalMonthlyPayment.toFixed(2)}</p>
        `;

        const chartData = [
            { label: 'Monthly Mortgage Payment', value: monthlyPayment, color: '#052286' },
            { label: 'Property Tax', value: monthlyPropertyTax, color: '#00adbb' },
            { label: 'Home Insurance', value: monthlyHomeInsurance, color: '#c2d500' },
            { label: 'HOA', value: monthlyHOAFees, color: '#fa8c68' },
        ];

        if (pmiRequired) {
            chartData.push({ label: 'PMI', value: monthlyPMI, color: '#d9534f' });
        }

        updateChart(chartData);
    }

    function updateChart(chartData) {
        

        const ctx = document.getElementById('chart').getContext('2d');

        if (window.myChart) {
            window.myChart.destroy();
        }

        window.myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartData.map(item => item.label),
                datasets: [{
                    data: chartData.map(item => item.value),
                    backgroundColor: chartData.map(item => item.color),
                }],
            },
            options: {
                cutoutPercentage: 85,
                responsive: false,
                tooltips: false,
            },
        });
    }

    document.querySelectorAll('#calculator-form input').forEach(input => {
        input.addEventListener('input', () => {
            updateResults();
        });
    });

    updateResults();
</script>


<?php
get_footer(); ?>