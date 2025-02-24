window.onload = function() {
    const allScoreForms = document.querySelectorAll(`form[name="score"]`);
    const scoreSubmitButton = document.querySelector(`#scores-edition-btn`);
    const url = document.querySelector(`input[name="score_update"]`).value;
    const studentId = document.querySelector(`input[name="student_id"]`).value;
    const targetedModal = document.getElementById(`kt_modal_student_scores_${studentId}`);

    allScoreForms.forEach(scoreForm => {
        const selectElem = scoreForm.querySelector(`div[data-score-id] select`);
        const selectedOption = selectElem.options[selectElem.selectedIndex].text;
        let scoreDiv = scoreForm.querySelector(`div[id="score"]`);
        let scoreId = scoreDiv.getAttribute('data-score-id');
        const scoreInputValue = scoreDiv.querySelector(`input`).value;

        const targetElem = document.getElementById(`tartgeted-subject-name_${scoreId}`);
        const targetElemInput = document.getElementById(`tartgeted-subject-mark-value_${scoreId}`);
        
        targetElem.innerHTML = selectedOption;
        targetElemInput.value = scoreInputValue;
        scoreDiv.style.display = 'none';
        selectElem.style.display = 'none';
    });

    scoreSubmitButton.addEventListener('click', (e) => {
        e.preventDefault;

        allScoreForms.forEach(scoreForm => {
            let scoreDiv = scoreForm.querySelector(`div[id="score"]`);
            let scoreId = scoreDiv.getAttribute('data-score-id');
            let scoreInput = document.querySelector(`input[id="tartgeted-subject-mark-value_${scoreId}"]`);
            
            fetchUpdateScore(url, scoreId, scoreInput);
        });
    });


    function fetchUpdateScore(url, scoreId, scoreInput) {
        const displayedScoreSpan = document.getElementById(`score-element_${scoreId}`);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                scoreId: scoreId,
                newScore: scoreInput.value,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status == 'KO') {
                console.log(data.message);
            } else if(displayedScoreSpan) {
                targetedModal.querySelector('[data-bs-dismiss="modal"]').click();
                displayedScoreSpan.textContent = scoreInput.value;
            }
        })
        .catch(error => {
            console.error('Error updating score:', error);
        });
    }

}