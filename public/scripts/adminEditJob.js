async function editJob(id){
    let myHeaders = new Headers();
    myHeaders.append('Content-Type', 'application/json');

    let url = `/admin/jobs/edit/${id}`;
    let form = document.querySelector('#jobEditForm');
    let dataForm = new FormData(form);

    try{
        let raw = JSON.stringify({
            name: dataForm.get('name'),
            localisation: dataForm.get('localisation'),
            langage: dataForm.get('langage'),
            contact: dataForm.get('contact'),
            description: dataForm.get('description')
        });

        let response = await fetch(url, {
            method: 'PUT',
            headers: myHeaders,
            body: raw
        });
        if(response.ok){
            window.location.href = '/admin';
        }
        else{
            throw new Error('Une erreur est survenue lors de la modification de l\'offre d\'emploi');
        }
    } catch (error){
        console.error('Une erreur a eu lieu : ', error);
    }
    
}