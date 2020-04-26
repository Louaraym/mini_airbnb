$('#add-image').click(function () {
    const widgetsCounter = $('#widgets-counter');
    const advertImages = $('#advert_images');

    // Get the index for the futures entries to create
    const index = +widgetsCounter.val();

    // Get the data-prototype
    const tmpl = advertImages.data('prototype').replace(/__name__/g, index);

    //Inject tmpl in the div
    advertImages.append(tmpl);

    // increase the index with one for the next item
    widgetsCounter.val(index+1);

    //Handle delete buttons
    handleDeleteButtons();
})

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter() {
    const count = +$('#advert_images div.form-group').length;
    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();