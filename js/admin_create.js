let create_option_container = document.querySelector('.create-option-container');
let increase_option = document.querySelector('.increase-option');
let decrease_option = document.querySelector('.decrease-option');

increase_option.addEventListener('click', function() {
    let length = parseInt(create_option_container.children.length);
    if (length < 10) {
        let option = document.createElement('div');
        option.classList.add('create-option');
        let p = document.createElement('p');
        p.innerText = 'Poll option';
        option.appendChild(p);
        let input = document.createElement('input');
        input.type = 'text';
        input.name = 'option';
        input.placeholder = 'Eg. Option ' + (length + 1);
        option.appendChild(input);
        create_option_container.appendChild(option);
    }
});


decrease_option.addEventListener('click', function() {
    let length = parseInt(create_option_container.children.length);
    if (length > 1) {
        create_option_container.removeChild(create_option_container.lastChild)
    }
});
