function debounce(fn, delay) {
    let timeout;
    return function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn.apply(this, arguments), delay);
    };
}

$('.classe-student-search-name').on(
    'input',
    debounce(function () {
        console.log('Auto submitting on input debounce...');
        
        $(this).closest('form').trigger('submit');
    }, 500)
);

$(document).on('click', '[data-kt-search-element="clear"]', function (e) {
    e.preventDefault();
    const $form = $(this).closest('form');
    const $input = $form.find('.classe-student-search-name');
    $input.val('');
    $input.trigger('focus');
    $form.trigger('submit');
});
