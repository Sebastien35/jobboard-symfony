const userContainer=document.getElementById('userContainer');
const jobContainer=document.getElementById('jobContainer');


const showUserbtn=document.querySelectorAll('.userBtn');
showUserbtn.forEach(btn=>{
    btn.addEventListener('click', displayUserContainer);
});



function displayUserContainer(){
    
    HideAll();
    userContainer.classList.remove('d-none');
}


const showJobsBtn = document.querySelectorAll('.jobBtn');
showJobsBtn.forEach(btn=>{
    btn.addEventListener('click', displayJobContainer);
});

function displayJobContainer(){
    HideAll();
    jobContainer.classList.remove('d-none');
}



const SearchJobsBtn = document.querySelector('#searchJobsBtn');
SearchJobsBtn.addEventListener('click', searchJobs);

async function searchJobs() {
    let langageRecherche = document.querySelector('#LangageInput').value;
    let localisationRecherche = document.querySelector('#LocalisationInput').value;

    
    let url = `/jobs/search?langage=${encodeURIComponent(langageRecherche)}&localisation=${encodeURIComponent(localisationRecherche)}`;
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        let data = await response.json();
        RemplirTableau(data);
        
    } catch (error) {
        console.error('Une erreur a eu lieu : ', error);
    }
}


function RemplirTableau(data){
    const Tableau = document.querySelector('#CorpsTableauJobs');
    Tableau.innerHTML = '';
    data.forEach(job => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${job.name}</td>
            <td>${job.localisation}</td>
            <td>${job.langage}</td>
            <td>${job.contact}</td>
            <td>${toYMD(job.createdAt)}</td>
           <td><button type="button" class="btn btn-danger" onclick="deleteJobOffer('${job.id}')">Delete</button></td>
            
        `;
        Tableau.appendChild(row);
    });
    
        
}

function toYMD(date){
    let dateObj = new Date(date);
    return `${dateObj.getFullYear()}-${dateObj.getMonth()+1}-${dateObj.getDate()}`;
}








function HideAll(){
    const toHide = document.querySelectorAll('.showHide');
    toHide.forEach(item=>{
        item.classList.add('d-none');
    });

}




async function deleteJobOffer(id){
    const myHeaders = new Headers();
    myHeaders.append('Content-Type', 'application/json');
    
    try {
        response = await fetch(`/admin/jobs/delete/${id}`, {
            method: 'DELETE',
            headers: myHeaders
        });
        if(response.ok){
            searchJobs();
        } else {
            alert('Une erreur est survenue lors de la suppression de l\'offre d\'emploi');
        }
    } catch (error) {
        console.error('Une erreur a eu lieu : ', error);
    }  
}

const addJobBtn = document.querySelector('#addJobBtn');
addJobBtn.addEventListener('click', postNewJobOffer);

async function postNewJobOffer(){

    const form = document.querySelector('#jobAddForm');
    let dataform = new FormData(form);
    let raw= JSON.stringify(
        {
            name: dataform.get('name'),
            localisation: dataform.get('localisation'),
            langage: dataform.get('langage'),
            contact: dataform.get('contact')
        }
    );

    const myHeaders = new Headers();
    myHeaders.append('Content-Type', 'application/json');
    try {
        const response = await fetch('/admin/jobs/new', {
            method: 'POST',
            headers: myHeaders,
            body: raw
        });
        
        if(response.ok){
            searchJobs();
        } else {
            window.location.href="/"
        }
    } catch (erreur) {
        console.error('Une erreur a eu lieu : ', erreur);
    }
}
