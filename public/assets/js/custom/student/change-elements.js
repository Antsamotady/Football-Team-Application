window.onload = function() {
    const allScoreForms = document.querySelectorAll(`form[name="score"]`);

    allScoreForms.forEach(scoreForm => {
        const selectElem = scoreForm.querySelector(`div[data-score-id] select`);
        const selectedOption = selectElem.options[selectElem.selectedIndex].text;
        const scoreDiv = scoreForm.querySelector(`div[id="score"]`);
        const scoreId = scoreDiv.getAttribute('data-score-id');
        const targetElem = document.getElementById(`tartgeted-subject-name_${scoreId}`);
        
        targetElem.innerHTML = selectedOption;
        selectElem.style.display = 'none';
    });
}