window.onload = function() {
    const allScoreForms = document.querySelectorAll(`form[name="score_only"]`);
    const scoreSubmitButton = document.querySelector(`#scores-edition-btn`);
    const url = document.querySelector(`input[name="score_update"]`).value;
    const studentId = document.querySelector(`input[name="student_id"]`).value;
    const targetedModal = document.getElementById(`kt_modal_student_scores_${studentId}`);
    let hasError = false;

    allScoreForms.forEach(scoreForm => {
        const selectElem = scoreForm.querySelector(`div[data-score-id] select`);
        const selectedOption = selectElem.options[selectElem.selectedIndex].text;
        
        let scoreDiv = scoreForm.querySelector(`div[id="score_only"]`);
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
        e.preventDefault();
        hasError = false;

        allScoreForms.forEach(scoreForm => {
            let scoreDiv = scoreForm.querySelector(`div[id="score_only"]`);
            let scoreId = scoreDiv.getAttribute('data-score-id');
            let scoreInput = document.querySelector(`input[id="tartgeted-subject-mark-value_${scoreId}"]`);
            
            const value = parseFloat(scoreInput.value);

            if (isNaN(value) || value > 20) {
                hasError = true;
                showScoreError(
                    scoreInput,
                    'Toutes les notes doivent être sur 20 points.'
                );
                return;
            }

            // Optional: prevent negative values
            if (value < 0) {
                hasError = true;
                showScoreError(
                    scoreInput,
                    'Les notes doivent être supérieurs à 0.'
                );
                return;
            }

            clearScoreError(scoreInput);
            fetchUpdateScore(url, scoreId, scoreInput);
        });

        if (hasError) {
            console.warn('Score validation failed');
        }
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
                hasError = true;
                showScoreError(scoreInput, data.message);
                return;
            }

            if (displayedScoreSpan) {
                displayedScoreSpan.textContent = scoreInput.value;
            }

            // ✅ CLOSE MODAL ONLY IF NO ERRORS
            if (!hasError) {
                targetedModal
                    .querySelector('[data-bs-dismiss="modal"]')
                    .click();
            }
        })
        .catch(error => {
            console.error('Error updating score:', error);
        });
    }
}

function showScoreError(input, message) {
    input.classList.add('is-invalid');

    let errorDiv = input.nextElementSibling;
    if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
        errorDiv = document.createElement('div');
        errorDiv.classList.add('invalid-feedback');
        input.after(errorDiv);
    }

    errorDiv.textContent = message;
}

function clearScoreError(input) {
    input.classList.remove('is-invalid');

    const errorDiv = input.nextElementSibling;
    if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
        errorDiv.remove();
    }
}
