//Milan Kruger
//u04948123

const api = localStorage.getItem('apiKey');
if(!api)
{
    window.location.href = 'login.php';
}
const url = 'https://wheatley.cs.up.ac.za/u04948123/api.php';

function logout()
{
    localStorage.setItem('apiKey',""); 
}

setTheme();
function calculator_mort()
{
    const formContent = document.getElementById('form-content');
    const form = document.createElement('form');
    while (formContent.firstChild) 
    {
        formContent.removeChild(formContent.firstChild);
    }
    
    const purchasePriceDiv = document.createElement('div');
    purchasePriceDiv.textContent = "Purchase Price";

    const purchasePriceSpan = document.createElement('span');
    purchasePriceSpan.textContent = "R ";

    const purchasePriceInput = document.createElement('input');
    purchasePriceInput.type = "text";
    purchasePriceInput.value = "1000000";

    purchasePriceSpan.appendChild(purchasePriceInput);

    const depositDiv = document.createElement('div');
    depositDiv.textContent = "Deposit";

    const depositSpan = document.createElement('span');
    depositSpan.textContent = "R ";

    const depositInput = document.createElement('input');
    depositInput.type = "text";
    depositInput.value = "0";

    depositSpan.appendChild(depositInput);

    const interestRateDiv = document.createElement('div');
    interestRateDiv.textContent = "Interest Rate";

    const interestRateInput = document.createElement('input');
    interestRateInput.type = "text";
    interestRateInput.value = "10";

    const interestRateSpan = document.createElement('span');
    interestRateSpan.appendChild(interestRateInput);
    interestRateSpan.innerHTML += " %";

    const loanTermDiv = document.createElement('div');
    loanTermDiv.textContent = "Loan Term";

    const loanTermInput = document.createElement('input');
    loanTermInput.type = "text";
    loanTermInput.value = "24";

    const loanTermSpan = document.createElement('span');
    loanTermSpan.appendChild(loanTermInput);
    loanTermSpan.innerHTML += " months";

    const submitDiv = document.createElement('div');
    submitDiv.className = "submit-form";

    const submitInput = document.createElement('input');
    submitInput.type = "submit";
    submitInput.value = "Submit";

    submitDiv.appendChild(submitInput);

    form.appendChild(purchasePriceDiv);
    form.appendChild(purchasePriceSpan);
    form.appendChild(depositDiv);
    form.appendChild(depositSpan);
    form.appendChild(interestRateDiv);
    form.appendChild(interestRateSpan);
    form.appendChild(loanTermDiv);
    form.appendChild(loanTermSpan);
    form.appendChild(submitDiv);

    formContent.appendChild(form);

    const resultsFormContent = document.createElement('div');
    resultsFormContent.className = "results-form-content";

    const monthlyPaymentDiv = document.createElement('div');
    monthlyPaymentDiv.innerHTML = "<span>Monthly Payment:</span> R <span id='monthlyPayment'>10,000</span>";

    const totalDiv = document.createElement('div');
    totalDiv.innerHTML = "<span>Total:</span> R <span id='totalPayment'>450,000</span>";

    const incomeRequiredDiv = document.createElement('div');
    incomeRequiredDiv.innerHTML = "<span>Income Required:</span> R <span id='incomeRequired'>69,420</span>";

    resultsFormContent.appendChild(monthlyPaymentDiv);
    resultsFormContent.appendChild(totalDiv);
    resultsFormContent.appendChild(incomeRequiredDiv);

    formContent.appendChild(resultsFormContent);



    resultsFormContent.appendChild(monthlyPaymentDiv);
    resultsFormContent.appendChild(totalDiv);
    resultsFormContent.appendChild(incomeRequiredDiv);

    formContent.appendChild(resultsFormContent);

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const purchasePrice = parseFloat(purchasePriceInput.value);
        const deposit = parseFloat(depositInput.value);
        const interestRate = parseFloat(interestRateInput.value) / 100;
        const loanTerm = parseFloat(loanTermInput.value);
        
        const principal = purchasePrice - deposit;
        const monthlyInterestRate = interestRate / 12;
        const numberOfPayments = loanTerm;
    

        const monthlyPayment = principal * monthlyInterestRate * Math.pow(1 + monthlyInterestRate, numberOfPayments) / (Math.pow(1 + monthlyInterestRate, numberOfPayments) - 1);
        const totalPayment = monthlyPayment * numberOfPayments;
        const incomeRequired = monthlyPayment * 0.4;
        
        document.getElementById('monthlyPayment').textContent = monthlyPayment.toFixed(2);
        document.getElementById('totalPayment').textContent = totalPayment.toFixed(2);
        document.getElementById('incomeRequired').textContent = incomeRequired.toFixed(2);
    });

}

function calculator_aff()
{

    const formContent = document.getElementById('form-content');
    const form = document.createElement('form');
    while (formContent.firstChild) 
    {
        formContent.removeChild(formContent.firstChild);
    }

    const incomeDiv = document.createElement('div');
    incomeDiv.textContent = "Gross Monthly Income";
 
    const incomeSpan = document.createElement('span');
    incomeSpan.textContent = "R ";
 
    const incomeInput = document.createElement('input');
    incomeInput.type = "text";
    incomeInput.value = "10000";
 
    incomeSpan.appendChild(incomeInput);

    const expenseDiv = document.createElement('div');
    expenseDiv.textContent = "Montly Expense";
 
    const expenseSpan = document.createElement('span');
    expenseSpan.textContent = "R ";
 
    const expenseInput = document.createElement('input');
    expenseInput.type = "text";
    expenseInput.value = "0";

    expenseSpan.appendChild(expenseInput);

    const percentageDiv = document.createElement('div');
    percentageDiv.textContent = "Budget";
 
    const percentageSpan = document.createElement('span');
 
    const percentageInput = document.createElement('input');
    percentageInput.type = "text";
    percentageInput.value = "15";
 
    const percentage = document.createElement('span');
    percentage.textContent ="%";
    
    percentageSpan.appendChild(percentageInput);
    percentageSpan.appendChild(percentage);
    const submitDiv = document.createElement('div');
    submitDiv.className = "submit-form";
 
    const submitInput = document.createElement('input');
    submitInput.type = "submit";
    submitInput.value = "Submit";
 
    submitDiv.appendChild(submitInput);
 
    form.appendChild(incomeDiv);
    form.appendChild(incomeSpan);
    form.appendChild(expenseDiv);
    form.appendChild(expenseSpan);
    form.appendChild(percentageDiv);
    form.appendChild(percentageSpan);
    form.appendChild(submitDiv);
    

    formContent.appendChild(form);

    const resultsFormContent = document.createElement('div');
    resultsFormContent.className = "results-form-content";

    const monthlyPaymentDiv = document.createElement('div');
    monthlyPaymentDiv.innerHTML = "<span>Net Monthly:</span> R <span id='netmonth'>10,000</span>";

    const monthDiv = document.createElement('div');
    monthDiv.innerHTML = "<span>Afford Monthly:</span> R <span id='affmonth'>10,000</span>";

    const totalDiv = document.createElement('div');
    totalDiv.innerHTML = "<span>Afford Yearly:</span> R <span id='affyear''>450,000</span>";

    resultsFormContent.appendChild(monthlyPaymentDiv);
    resultsFormContent.appendChild(monthDiv);
    resultsFormContent.appendChild(totalDiv);

    formContent.appendChild(resultsFormContent);

    form.addEventListener('submit', function(event) {
        event.preventDefault();
    
        const income = parseFloat(incomeInput.value);
        const expense = parseFloat(expenseInput.value);
        const perc = parseFloat(percentageInput.value)/100;

        const netMonthly = income - expense;
        const affordableMonthly = netMonthly * perc;
        const affordableYearly = affordableMonthly * 12;

        document.getElementById('netmonth').textContent = netMonthly.toFixed(2);
        document.getElementById('affmonth').textContent = affordableMonthly.toFixed(2);
        document.getElementById('affyear').textContent = affordableYearly.toFixed(2);
    });

}
async function setTheme(enabled=false)
{
    var element = document.body;
    let theme = await getTheme();
    theme = theme.data[0].theme;
    if(theme === 'light')
    {
        element.classList.toggle("light-mode");
    }
    if(theme === 'dark')
    {
        element.classList.toggle("dark-mode");
    }
    if(enabled === true)
    {
        if (theme === 'light') 
        {
            theme = 'dark';
            element.classList.toggle("dark-mode");
        } 
        else if (theme === 'dark') 
        {
            theme = 'light';
            element.classList.toggle("light-mode");
        }

        const xml = new XMLHttpRequest();
        xml.open('POST', url, true);
        xml.setRequestHeader('Content-Type', 'application/json');

        const username = 'u04948123';
        const password = '3918Milo';
        const auth = 'Basic ' + btoa(username + ':' + password);
        xml.setRequestHeader('Authorization', auth);

        xml.send(JSON.stringify({
            type: "SetTheme",
            apikey: api,
            theme: theme
        }));
    }
}

async function getTheme()
{
    {
        return new Promise((resolve) => 
        {
            let xml = new XMLHttpRequest();
            xml.open('POST', url, true);
            xml.setRequestHeader('Content-Type', 'application/json');
            
            const username = 'u04948123';
            const password = '3918Milo';
            const auth = 'Basic ' + btoa(username + ':' + password);
            xml.setRequestHeader('Authorization', auth);

            xml.onreadystatechange = function () {
                if (xml.readyState === 4 && xml.status === 200) 
                {
                    let response = JSON.parse(xml.responseText);
                    resolve(response);
                }
            };
            xml.send(JSON.stringify({
                type: "GetTheme",
                apikey: api,
            }));
        });
    }
}