const poll_selection = document.querySelector('.poll-selection');
const poll_options = document.querySelector('.poll-options');
const poll_option = document.querySelectorAll('.poll-option');
const poll_total = document.querySelectorAll('.poll-total');
const options_selection = document.querySelectorAll('input[name="select"]');
const options_selections = document.querySelectorAll('input[name="selects[]"]');

const submit_button = document.querySelector('.submit-button');
const a = document.querySelector('.a');



delegate(poll_options, 'click', '.poll-option', function(event) {
    if (poll_selection.innerText === 'SINGLE CHOICE') {
        const target_index = Array.from(options_selection).indexOf(event.target);
        for (const option of poll_option) {
            option.classList.remove('selected');
        }
        poll_option[target_index].classList.add('selected');
    }
    else {
        const target_index = Array.from(options_selections).indexOf(event.target);
        if (poll_option[target_index].classList.value.includes('selected')) {
            poll_option[target_index].classList.remove('selected');
        }
        else {
            poll_option[target_index].classList.add('selected');
        }
    }
});






function loaded() {
    if (options_selection.length != 0) {
        options_selection.forEach(poll => {
            if (poll.checked == true) {
                poll.parentElement.classList.add('selected');
            }
        });
    }
    else {
        options_selections.forEach(poll => {
            if (poll.checked == true) {
                poll.parentElement.classList.add('selected');
            }
        });
    }
}


function delegate(parent, type, selector, handler) {
    parent.addEventListener(type, function(event) {
        const targetElement = event.target.closest(selector);
        if (this.contains(targetElement)) handler.call(targetElement, event);
    });
}