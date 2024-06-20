document.addEventListener('DOMContentLoaded', sendRequest);

async function sendRequest() {
    try{
    let myHeaders = new Headers();
    myHeaders.append('Content-Type', 'application/json');
    response =  await fetch('/jobs/popularity',
        {
            method: 'GET',
            headers: myHeaders
        });
    let data = await response.json();
    console.log(data);
    displayChart(data);

    } catch (error) {
        console.error('Error:', error);
    }
}

function displayChart(data) {
    let container = document.querySelector('#chartContainer');
    
        let tableau = document.createElement('table');
        let headerRow = document.createElement('tr');

        let langageHeader = document.createElement('th');
        langageHeader.textContent = "Langage";

        let populariteHeader = document.createElement('th');
        populariteHeader.textContent = "Popularité";

        tableau.appendChild(headerRow);
    for (const[langage, popularite] of Object.entries(data)) {
        let row = document.createElement('tr');

        let langageEntry = document.createElement('td');
        langageEntry.textContent = langage;
        row.appendChild(langageEntry);

        let popularityEntry = document.createElement('td');
        popularityEntry.textContent = popularite.toFixed(2) + '%';
        row.appendChild(popularityEntry);

        tableau.appendChild(row);
        
    }
    container.appendChild(tableau);
}
    