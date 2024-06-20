let searchByLangageInput = document.querySelector('#searchByLangageInput');
let searchByLangageButton = document.querySelector('#searchByLangageButton');


searchByLangageButton.addEventListener('click', searchByLangage );    



function searchByLangage() {
    console.log('searchByLangage');
    let userInput = searchByLangageInput.value;
    let searchedLangage = userInput.toUpperCase();

    console.log(searchedLangage);
   window.location.href=(`/jobs/langage/${searchedLangage}`);
    
}