window.onload = function() {
  console.log('hehe!!!!!!!!!!');
  
  // const selectElem = document.getElementById('score_subject');
  const selectElem = document.querySelector('div[data-score-id] select');
  const targetElem = document.getElementById('tartgeted-subject-name_296');
  const selectedOption = selectElem.options[selectElem.selectedIndex].text;
  targetElem.text = selectedOption;
  selectElem.style.display = 'none';
}